<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
require_once 'PDFGenerate.php';

$companies = setBillingDetail();

$id = isset($_GET['ID']) && !empty($_GET['ID']) ? decryptId($_GET['ID']) : "";
$comp = isset($_GET['comp']) && !empty($_GET['comp']) ? $_GET['comp']: "Go2 Ladakh";
$u_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data_to_store = array_filter($_POST); 
  
  $comp_invoice['invoice_comp'] = $data_to_store['invoice_comp']; 
  $db->where('id', $id);
  $invoice_detail = $db->update('agent_queries', $data_to_store);

  $db = getDbInstance();
  $db->where('id', $id);
  $queries = $db->getOne("agent_queries");

  $pdfObj1 = new PDFGenerate;
  $file_name = "invoice_{$id}_" . uniqid();
  $pdfObj1->generate_invoice($queries);
  $filePath = $pdfObj1->generatePDF($file_name);
  $data_to_store['invoice_pdf'] = $filePath;
  $data_to_store['invoice_no'] = sprintf("CTL%04d", $queries['id']);
  $db->where('id', $id);
  $invoice_detail = $db->update('agent_queries', $data_to_store);
}
$db = getDbInstance();
$db->where('id', $id);
$rows = $db->getOne('agent_queries');

$db = getDbInstance();
$package_id = $rows['package_id'];
$db->where('id', $package_id);
$row = $db->getOne('packages');

$db = getDbInstance();
$db->where('id', $u_id);
$data = $db->getOne('agents');

$db->where('id', $rows['created_by']);
$result = $db->getOne('agents');

//$_SESSION['success'] = "The invoice has been generated successfully.";

include BASE_PATH . '/includes/header.php';
?>
<!-- Layout container -->
<div class="layout-page">
  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">	
<div class="card">
 
  <div class="card-body">
	<div class="form-group row">
    <label for="invoice_company" class="col-sm-4 col-form-label">Change Company For Invoice</label>
	<div class="col-sm-8">
    <select class="form-control" id="invoice_company" onchange="return changeCompany(this.value);">
	<?php foreach($companies as $key=>$company){
		$select = $key==$comp? 'selected':"";
      echo "<option value=\"$key\" $select>$key</option>";
	} ?>
    </select>
	</div>
  </div>
  </div>
</div>
</div>

    <form method="post" enctype="multipart/form-data">
      <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">Edit Invoice</h4>
        <!-- Basic Layout -->
        <div class="row">
          <div class="col-xl">
            <div class="card mb-4">
              <div class="card-body">
                <div class="invoice-table" style="margin-top: 30px;">
                  <table cellpadding="0" cellspacing="0" class="custom-table">
                    <tr>
                      <td colspan="4" style="padding-top: 30px; padding-bottom:30px;"><img src="<?=$result['logo']?>" alt="logo" style="width: 150px"></td>
                      <td colspan="4" style="font-size: 24px; text-align:center; font-weight: bold;">TAX INVOICE</td>
                    </tr>
                    <tr>
                      <td colspan="3" style="font-size:16px; font-weight:bold; color:#696cff">Seller</td>
                      <td colspan="3" style="font-size:16px; font-weight:bold; color:#696cff">Buyer</td>
                      <td colspan="2" style="font-size:16px; font-weight:bold; color:#696cff"><strong>ADDITIONAL DETAILS</strong></td>
                    </tr>
                    <tr>

                    <td colspan="3">
                        <div class="switch-seller">
							<?php echo $companies["$comp"]['detail'] ?> 
                        </div>
                        
                    </td>
                      <td colspan="3">
                        <div class="edit-buyer-info">
                          TC Tours Ltd.<br>
                          <?= $result['complete_address'] ?><br>
                          GSTIN/UIN: <?= $result['gst_no'] ?><br>
                          State Name: <?= $result['state'] ?>,code: <?=$result['agent_code'] ?><br>
                          Place of Supply: Ladakh
                        </div>
                        <!-- <div class="edit-btn"><a href="#">Edit details</a></div> -->

                      </td>
                      <td colspan="2" style="padding: 0;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;">
                          <tr>
                            <td colspan="2"><strong><?= $rows['booking_code'] ?></strong></td>
                          </tr>
                          <tr>
                            <td><strong>Travel Date</strong></td>
                            <td><?= $rows['tour_start_date'] ?></td>
                          </tr>
                          <tr>
                            <td><strong>No of Travel Day</strong></td>
                            <td><?= $rows['duration'] ?></td>
                          </tr>
                          <tr>
                            <td><strong>Invoice Date</strong></td>
                            <td><?= !empty($rows['invoice_date'])?$rows['invoice_date']:date("Y-m-d") ?></td>
                          </tr>
                          <tr>
                            <td><strong>Due Date</strong></td>
                            <td><?= !empty($rows['due_date'])?$rows['due_date']:date("Y-m-d") ?></td>
                          </tr>
                          <tr>
                            <td><strong>Place of Supply</strong></td>
                            <td>Ladakh</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6"></td>
                      <td><strong>Invoice No</strong></td>
                      <td><strong><?= !empty($rows['invoice_no'])?$rows['invoice_no']:sprintf("CTL%04d", $rows['id']) ?></strong></td>
                    </tr>
                    <tr class="blue-bg">
                      <td class="align-center">S.No.</td>
                      <td colspan="2" style="width:40%;">Name/Description/Services</td>
                      <td class="align-center" style="width: 10%;">No of pax</td>
                      <td style="width: 20%;" colspan="2">Per Person Rate in INR</td>
                      <td>DISCOUNT</td>
                      <td>TOTAL</td>
                    </tr>
                    <tr>
                      <td class="align-center"><strong>1</strong></td>
                      <td colspan="2"><?= $row['package_name'] ?></td>
                      <td class="align-center"><?= $rows['total_pax'] ?></td>
                      <?php
                      $other_charge = isset($rows['other_charge']) ? $rows['other_charge'] : 0;
                      $other_charge_2 = isset($rows['other_charge_2']) ? $rows['other_charge_2'] : 0;
                      $other_charge_3 = isset($rows['other_charge_3']) ? $rows['other_charge_3'] : 0;
                      $other_charge_4 = isset($rows['other_charge_4']) ? $rows['other_charge_4'] : 0;

                      $charge = $other_charge + $other_charge_2 + $other_charge_3 + $other_charge_4;

                      $discount = ($rows['your_budget']>0 && $rows['without_gst']> $rows['your_budget'])? ($rows['without_gst']-$rows['your_budget']):$rows['without_gst'];
                      $change_price = ($rows['your_budget']>0)?$rows['your_budget']:$rows['without_gst'] ;
                      $tatal_price = $change_price  + $charge; 
                      //print_r($tatal_price );
                      $igstPrice = round(($rows['without_gst'] * 0.025),2);
                      $taxPrice = round(($rows['without_gst'] * 0.025),2);
                      $finalPrice = ($tatal_price + $igstPrice + $taxPrice);
                      $perPerson = round(($finalPrice / $rows['total_pax']),2);

                      ?>
                      <td colspan="2"><?= $perPerson ?></td>
                      <td>Rs.<?=$discount?></td>
                      <td>Rs.<?=$change_price ?></td>
                    </tr>
                    <tr>
                      <td style="color: #696cff"><strong>BANK DETAILS</strong></td>
                      <td colspan="2"></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>OTHER CHARGE</td>
                      <td>Rs.<?= $charge ?></td>
                    </tr>
                    <tr>
                      <td><strong>Account Name</strong></td>
                      <td colspan="2">CHUMIK TOUR AND TRAVEL</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>SUB TOTAL</td>
                      <td>Rs.<?=$tatal_price ?></td>
                    </tr>
                    <tr>
                      <td><strong>Account No</strong></td>
                      <td colspan="2">*0069010100002805</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>IGST 2.5%</td>
                      <td>Rs.<?=$igstPrice ?></td>
                    </tr>
                    <tr>
                      <td><strong>IFSC Code</strong></td>
                      <td colspan="2">JAKA0PRIEST</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>TOTAL TAX 2.5%</td>
                      <td>Rs.<?= $taxPrice ?></td>
                    </tr>
                    <tr>
                      <td><strong>Branch</strong></td>
                      <td colspan="2">Leh main market</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td style="color: #696cff"><strong>TERMS & NOTES</strong></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td style="color: #696cff; font-size: 18px;"><strong>GRAND TOTAL</strong></td>
                      <td style="font-size: 18px"><strong>Rs.<?= $finalPrice ?></strong></td>
                    </tr>

                  </table>

                  <div class="row mb-3" style="margin-top: 10px;">
                  <div class="row mb-3">
                  <div class="col-md text-bold">
                    <label class="form-label" for="basic-default-phone"><strong>Other Charge 1</strong></label>
                    <input type="number"class="form-control" name="other_charge" value="<?= $rows['other_charge'] ?>" />
                  </div>
                  <div class="col-md text-bold">
                    <label class="form-label" for="basic-default-phone"><strong>Other Charge 2</strong></label>
                    <input type="number" class="form-control" name="other_charge_2" value="<?= $rows['other_charge_2'] ?>" />
                  </div>
                  
                </div>
                <div class="row mb-3">
                  <div class="col-md text-bold">
                    <label class="form-label" for="basic-default-phone"><strong>Other Charge 3</strong></label>
                    <input type="number" class="form-control" name="other_charge_3" value="<?= $rows['other_charge_3'] ?>" />
                  </div>
                  <div class="col-md text-bold">
                    <label class="form-label" for="basic-default-phone"><strong>Other Charge 4</strong></label>
                    <input type="number" class="form-control" name="other_charge_4" value="<?= $rows['other_charge_4'] ?>" />
                  </div>
                  
                </div>

                    <!-- <div class="col-md text-bold"><label class="form-label"><strong>Other Charge</strong></label></div>
                    <div class="col-md" id="summary-duration"><input type="number" name="other_charge" class="form-control" value="<?= $rows['other_charge'] ?>"></div>
                     -->
                    <input type="hidden" name="invoice_date" value="<?= !empty($rows['invoice_date'])?$rows['invoice_date']:date("Y-m-d") ?>">
                    <input type="hidden" name="due_date" value="<?= !empty($rows['due_date'])?$rows['due_date']:date("Y-m-d") ?>">
					 <input type="hidden" name="invoice_comp" value="<?= $comp ?>">

                 
                  </div>
                </div>
                <div class="row get-quote-btn" style="margin-top: 10px;">
                  <button type="submit" class="btn btn-primary" name="form_submit">Generate invoice</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    <!-- / Content -->
  </div>
</div>

<script>
function changeCompany(company_name) {
	//var company_name = e.val();
	console.log(company_name);
	const url = `<?php echo BASE_URL;?>generate_invoice.php?ID=<?php echo $_GET['ID']?>&comp=${company_name}`;
	window.location.href = url;
}
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>