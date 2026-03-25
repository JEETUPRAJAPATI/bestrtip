<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col');
$order_by = filter_input(INPUT_GET, 'order_by');

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
  $page = 1;
}

if (!$filter_col) {
  $filter_col = 'agent_queries.id';
}
if (!$order_by) {
  $order_by = 'asc';
}

$db = getDbInstance();
$db->join('packages', 'agent_queries.package_id = packages.id', 'LEFT');
$db->join('agents', 'agent_queries.created_by = agents.id', 'LEFT');
$select = array('agent_queries.id', 'name', 'booking_code', 'query_code', 'total_pax', 'invoice_no', 'total_amount', 'invoice_pdf', 'created_by', 'updated_by', 'agent_queries.created_at', 'agent_queries.updated_at',  'packages.package_name',
  'agents.agent_code','without_gst');
$db->where("invoice_no", null, "IS NOT");
if ($search_string) {
  $db->where("$filter_col", '%' . $search_string . '%', 'like');
}

if ($order_by) {
  $db->orderBy($filter_col, $order_by);
}

$db->pageLimit = PAGE_LIMIT;

$rows = $db->arraybuilder()->paginate('agent_queries', $page, $select);
$total_pages = $db->totalPages;


include BASE_PATH . '/includes/header.php';
?>
<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <form>
        <div class="card mb-4">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-5">
                <label class="form-label">Search by</label>
                <div class="input-group">
                  <label class="input-group-text">Options</label>
                  <select class="form-select" name="filter_col">
                    <option selected="">Choose...</option>
                    <option value="invoice_no">Invoice No</option>
                    <option value="booking_code">Booking Number</option>
                    <option value="agent_code">Agent Code</option>
                    <option value="package_name">Package Name</option>
                    <option value="name">Guest Name</option>
                  </select>
                </div>
              </div>
              <div class="col-md-5">
                <label class="form-label">Enter Text</label>
                <input class="form-control" name="search_string" type="text" placeholder="Search...">
              </div>
              <div class="col-md">
                <label class="form-label" style="display: block;">&nbsp;</label>
                <button type="submit" class="btn btn-primary">Search</button>
              </div>

            </div>
          </div>
      </form>

    </div>
    <h4 class="py-3 mb-4">All Invoices</h4>

    <!-- Basic Layout -->
    <div class="row">
      <div class="col-xl">
        <div class="card mb-4">
          <div class="card-body">
            <div class="table-responsive text-nowrap border-light border-solid mb-3">
              <table class="table">
                <thead>
                  <tr class="text-nowrap bg-dark align-middle">
                    <th class="text-white border-right-white">Invoice No</th>
                    <th class="text-white border-right-white">Booking No</th>
                    <th class="text-white border-right-white">Agent Code</th>
                    <th class="text-white border-right-white">Package Type</th>
                    <th class="text-white border-right-white">Guest Name</th>
                    <th class="text-white border-right-white">No of Pax</th>
					<th class="text-white border-right-white">Booking Amount</th>
					<th class="text-white border-right-white">GST Amount</th>
                    <th class="text-white border-right-white">Total Amount</th>
                    <th class="text-white border-right-white">Download Invoice</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php
                  $k = ($page != 1) ? (($page - 1) * PAGE_LIMIT) + 1 : 1;
                  foreach ($rows as $row) : ?>
                    <tr>
                      <td class="border-right-dark"><?php echo xss_clean($row['invoice_no']); ?></td>
                      <td class="border-right-dark"><?php echo xss_clean($row['booking_code']); ?></td>
                      <td class="border-right-dark"><?php echo xss_clean($row['agent_code']); ?></td>
                      <td class="border-right-dark"><?php echo xss_clean($row['package_name']); ?></td>
                      <td class="border-right-dark"><?php echo xss_clean($row['name']); ?></td>
                      <td class="border-right-dark"><?php echo xss_clean($row['total_pax']); ?></td>
					  <td class="border-right-dark">₹<?= $row['without_gst'] ?></td>
					  <td class="border-right-dark">₹<?= ($row['total_amount'] -$row['without_gst']) ?></td>
                      <td class="border-right-dark">₹<?php echo xss_clean($row['total_amount']); ?></td>
                      <td class="border-right-dark">
                        <div class="dropdown pos-relative">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow toggle-options">
                            <i class="bx bx-dots-vertical-rounded"><span></span></i>
                          </button>
                          <div class="dropdown-menu custom-dd-menu">
                            <a class="dropdown-item"  href="generate_invoice.php?ID=<?php echo encryptId($row['id']); ?>"><i class="bx bx-edit-alt me-1"></i> Edit Invoice</a>
                            <a class="dropdown-item" href="<?=$row['invoice_pdf']?>" target="_blank"><i class="bx bx-trash me-1"></i> Download Invoice</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                  <?php
                    $k++;
                  endforeach; ?>

                </tbody>
              </table>
            </div>
            <!-- Pagination -->
            <div class="text-center">
              <?php echo paginationLinks($page, $total_pages, 'invoice.php'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->
</div>
</div>
<?php include BASE_PATH . '/includes/footer.php'; ?>