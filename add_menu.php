<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$pid = decryptId($_GET['pid'] ?? '');
$rid = $_GET['rid'] ?? '';
$db = getDbInstance();

// Hotel info
$db->where('id', $pid);
$hotel = $db->getOne('properties');

// Get restaurants for this property
$db->where('property_id', $pid);
$restaurants = $db->get('restaurants');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if we're adding a new category or menu item
    if (isset($_POST['add_category'])) {
        // Add new category
        $categoryData = array(
            'restaurant_id' => $_POST['restaurant_id'],
            'category' => $_POST['new_category'],
            'subcategory' => $_POST['new_subcategory']
        );

        $category_id = $db->insert('menu_categories', $categoryData);

        if ($category_id) {
            $_SESSION['success'] = 'Category added successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to add category: ' . $db->getLastError();
        }
    } else {
        // Add new menu item
        $data = array(
            'restaurant_id' => $_POST['restaurant_id'],
            'category_id' => $_POST['category_id'],
            'item_name' => $_POST['item_name'],
            'type' => $_POST['type'],
            'price' => $_POST['price'],
            'created_at' => date('Y-m-d H:i:s')
        );

        $last_id = $db->insert('restaurant_menu', $data);

        if ($last_id) {
            $_SESSION['success'] = 'Menu item added successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to add menu item: ' . $db->getLastError();
        }
    }

    header('Location: add_menu.php?pid=' . encryptId($pid) . '&rid=' . $_POST['restaurant_id']);
    exit();
}

// Get menu categories for selected restaurant
$categories = [];
if ($rid) {
    $db->where('restaurant_id', $rid);
    $db->orderBy('category', 'ASC');
    $db->orderBy('subcategory', 'ASC');
    $categories = $db->get('menu_categories');

    // Get menu items for selected restaurant with category info
    $menuItems = [];
    if (!empty($categories)) {
        $db->where('rm.restaurant_id', $rid);
        $db->join('menu_categories mc', 'rm.category_id=mc.id', 'LEFT');
        $db->orderBy('mc.category', 'ASC');
        $db->orderBy('mc.subcategory', 'ASC');
        $db->orderBy('rm.item_name', 'ASC');
        $menuItems = $db->get('restaurant_menu rm', null, 'rm.*, mc.category, mc.subcategory');
    }
}

include BASE_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .category-header {
            background-color: #f3f4f6;
            border-left: 4px solid #4f46e5;
        }

        .menu-item {
            transition: all 0.2s ease;
        }


        /* Modal positioning fix */
        .modal-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        /* Improved menu item card design */
        .menu-item-card {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .menu-item-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .veg-indicator {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .veg {
            background-color: #10b981;
            border: 2px solid #10b981;
        }

        .non-veg {
            background-color: #ef4444;
            border: 2px solid #ef4444;
        }

        .dot {
            width: 4px;
            height: 4px;
            background-color: currentColor;
            border-radius: 50%;
            display: inline-block;
            margin: 0 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="layout-page">
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-utensils text-indigo-600 mr-2"></i>
                            Manage Menu: <?= htmlspecialchars($hotel['property_name']) ?>
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Add and organize menu items for your restaurants
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($rid): ?>
                            <button id="addCategoryBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-plus mr-2"></i> Add Category
                            </button>
                            <button id="addMenuItemBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-utensil-spoon mr-2"></i> Add Menu Item
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <?= $_SESSION['success'] ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['failure'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?= $_SESSION['failure'] ?>
                        <?php unset($_SESSION['failure']); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">Restaurant Selection</h2>
                    </div>
                    <div class="p-4">
                        <form class="flex items-center space-x-4">
                            <div class="w-full">
                                <label for="restaurant_id" class="block text-sm font-medium text-gray-700 mb-1">Select Restaurant</label>
                                <select id="restaurant_id" name="restaurant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Select Restaurant --</option>
                                    <?php foreach ($restaurants as $restaurant): ?>
                                        <option value="<?= $restaurant['id'] ?>" <?= $rid == $restaurant['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($restaurant['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($rid): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Categories Panel -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="p-4 border-b flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-800">Categories</h2>
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    <?= count($categories) ?> categories
                                </span>
                            </div>
                            <div class="p-4">
                                <?php if (empty($categories)): ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                                        <p class="text-gray-500">No categories found</p>
                                        <button id="addFirstCategoryBtn" class="mt-4 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Add First Category
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-3">
                                        <?php
                                        $groupedCategories = [];
                                        foreach ($categories as $cat) {
                                            $groupedCategories[$cat['category']][] = $cat;
                                        }
                                        ?>

                                        <?php foreach ($groupedCategories as $category => $subcategories): ?>
                                            <div class="border rounded-lg overflow-hidden">
                                                <div class="category-header px-3 py-2 font-medium flex justify-between items-center">
                                                    <span><?= htmlspecialchars($category) ?></span>
                                                    <div class="flex space-x-2">
                                                        <button class="text-indigo-600 hover:text-indigo-800 edit-category" data-id="<?= $subcategories[0]['id'] ?>">
                                                            <i class="fas fa-edit fa-xs"></i>
                                                        </button>
                                                        <button class="text-red-600 hover:text-red-800 delete-category" data-id="<?= $subcategories[0]['id'] ?>">
                                                            <i class="fas fa-trash fa-xs"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="divide-y divide-gray-100">
                                                    <?php foreach ($subcategories as $subcat): ?>
                                                        <div class="px-3 py-2 text-sm flex justify-between items-center">
                                                            <span><?= htmlspecialchars($subcat['subcategory'] ?: 'No subcategory') ?></span>
                                                            <div class="flex space-x-2">
                                                                <button class="text-indigo-600 hover:text-indigo-800 edit-subcategory" data-id="<?= $subcat['id'] ?>">
                                                                    <i class="fas fa-edit fa-xs"></i>
                                                                </button>
                                                                <button class="text-red-600 hover:text-red-800 delete-subcategory" data-id="<?= $subcat['id'] ?>">
                                                                    <i class="fas fa-trash fa-xs"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div class="px-3 py-2 bg-gray-50">
                                                        <button class="text-xs text-indigo-600 hover:text-indigo-800 add-subcategory" data-category="<?= htmlspecialchars($category) ?>">
                                                            <i class="fas fa-plus mr-1"></i> Add Subcategory
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Menu Items Panel -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b flex justify-between items-center">
                                    <h2 class="text-lg font-semibold text-gray-800">Menu Items</h2>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?= count($menuItems) ?> items
                                    </span>
                                </div>
                                <div class="p-4">
                                    <?php if (empty($menuItems)): ?>
                                        <div class="text-center py-8">
                                            <i class="fas fa-utensils text-gray-300 text-4xl mb-3"></i>
                                            <p class="text-gray-500">No menu items found</p>
                                            <button id="addFirstMenuItemBtn" class="mt-4 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Add First Menu Item
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="space-y-4">
                                            <?php
                                            $currentCategory = '';
                                            foreach ($menuItems as $item):
                                                $categoryHeader = $item['category'] . ($item['subcategory'] ? ' > ' . $item['subcategory'] : '');

                                                if ($categoryHeader != $currentCategory) {
                                                    $currentCategory = $categoryHeader;
                                            ?>
                                                    <div class="mb-4">
                                                        <h3 class="text-md font-semibold text-gray-700 mb-2 flex items-center">
                                                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded mr-2">
                                                                <?= htmlspecialchars($item['category']) ?>
                                                            </span>
                                                            <?php if ($item['subcategory']): ?>
                                                                <i class="fas fa-chevron-right text-xs text-gray-400 mx-1"></i>
                                                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                                    <?= htmlspecialchars($item['subcategory']) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </h3>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <?php } ?>

                                                        <div class="menu-item-card bg-white p-4">
                                                            <div class="flex justify-between items-start">
                                                                <div>
                                                                    <div class="font-medium text-gray-800 flex items-center">
                                                                        <span class="veg-indicator <?= $item['type'] == 'Veg' ? 'veg' : 'non-veg' ?>"></span>
                                                                        <?= htmlspecialchars($item['item_name']) ?>
                                                                    </div>
                                                                    <div class="text-sm text-gray-600 mt-1">
                                                                        ₹<?= number_format($item['price'], 2) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="flex space-x-2">
                                                                    <button class="text-indigo-600 hover:text-indigo-800 edit-item" data-id="<?= $item['id'] ?>">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button class="text-red-600 hover:text-red-800 delete-item" data-id="<?= $item['id'] ?>">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                        // Check if next item has different category to close the div
                                                        $nextIndex = array_search($item, $menuItems) + 1;
                                                        if ($nextIndex >= count($menuItems) || $menuItems[$nextIndex]['category'] . ($menuItems[$nextIndex]['subcategory'] ? ' > ' . $menuItems[$nextIndex]['subcategory'] : '') != $currentCategory) {
                                                            echo '</div></div>';
                                                        }
                                                        ?>
                                                    <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    </div>
                                        </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden p-8 text-center">
                                <i class="fas fa-utensils text-gray-300 text-5xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-700 mb-2">No Restaurant Selected</h3>
                                <p class="text-gray-500 mb-4">Please select a restaurant from the dropdown above to manage its menu</p>
                            </div>
                        <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="fixed inset-0 z-50 hidden" id="addCategoryModal">
        <div class="modal-container">
            <div class="bg-white rounded-lg shadow-xl transform transition-all w-full max-w-md">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-folder-plus text-indigo-600 mr-2"></i>
                                Add New Category
                            </h3>
                            <form id="addCategoryForm" method="post" class="space-y-4">
                                <input type="hidden" name="restaurant_id" value="<?= $rid ?>">
                                <input type="hidden" name="add_category" value="1">

                                <div>
                                    <label for="new_category" class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                                    <input type="text" id="new_category" name="new_category" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g., Main Course, Appetizers">
                                </div>

                                <div>
                                    <label for="new_subcategory" class="block text-sm font-medium text-gray-700 mb-1">Subcategory (optional)</label>
                                    <input type="text" id="new_subcategory" name="new_subcategory"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g., Pasta, Salads">
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        <i class="fas fa-save mr-2"></i> Save Category
                                    </button>
                                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-category-modal">
                                        <i class="fas fa-times mr-2"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Menu Item Modal -->
    <div class="fixed inset-0 z-50 hidden" id="addMenuItemModal">
        <div class="modal-container">
            <div class="bg-white rounded-lg shadow-xl transform transition-all w-full max-w-md">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                                Add New Menu Item
                            </h3>
                            <form action="" method="post" class="space-y-4">
                                <input type="hidden" name="restaurant_id" value="<?= $rid ?>">

                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                    <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Select Category --</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>">
                                                <?= htmlspecialchars($category['category']) ?>
                                                <?php if ($category['subcategory']): ?>
                                                    > <?= htmlspecialchars($category['subcategory']) ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                                    <input type="text" id="item_name" name="item_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Margherita Pizza">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="Veg">Vegetarian</option>
                                            <option value="Non-Veg">Non-Vegetarian</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (₹) *</label>
                                        <input type="number" step="0.01" id="price" name="price" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        <i class="fas fa-save mr-2"></i> Save Item
                                    </button>
                                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-menu-item-modal">
                                        <i class="fas fa-times mr-2"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="fixed inset-0 z-50 hidden" id="editModal">
        <div class="modal-container">
            <div class="bg-white rounded-lg shadow-xl transform transition-all w-full max-w-md">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-edit text-indigo-600 mr-2"></i>
                                Edit Menu Item
                            </h3>
                            <div id="editFormContainer">
                                Loading form...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="fixed inset-0 z-50 hidden" id="editCategoryModal">
        <div class="modal-container">
            <div class="bg-white rounded-lg shadow-xl transform transition-all w-full max-w-md">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-edit text-indigo-600 mr-2"></i>
                                Edit Category
                            </h3>
                            <div id="editCategoryFormContainer">
                                Loading form...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subcategory Modal -->
    <div class="fixed inset-0 z-50 hidden" id="addSubcategoryModal">
        <div class="modal-container">
            <div class="bg-white rounded-lg shadow-xl transform transition-all w-full max-w-md">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i class="fas fa-folder-plus text-indigo-600 mr-2"></i>
                                Add Subcategory
                            </h3>
                            <form id="addSubcategoryForm" method="post" class="space-y-4">
                                <input type="hidden" name="restaurant_id" value="<?= $rid ?>">
                                <input type="hidden" name="add_category" value="1">
                                <input type="hidden" id="parent_category" name="new_category">

                                <div>
                                    <label for="subcategory_name" class="block text-sm font-medium text-gray-700 mb-1">Subcategory Name *</label>
                                    <input type="text" id="subcategory_name" name="new_subcategory" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g., Starters, Desserts">
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        <i class="fas fa-save mr-2"></i> Save Subcategory
                                    </button>
                                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-subcategory-modal">
                                        <i class="fas fa-times mr-2"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/custom-script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filter by restaurant
            $('#restaurant_id').change(function() {
                const restaurantId = $(this).val();
                const url = new URL(window.location.href);

                if (restaurantId) {
                    url.searchParams.set('rid', restaurantId);
                } else {
                    url.searchParams.delete('rid');
                }

                window.location.href = url.toString();
            });

            // Show add category modal
            $('#addCategoryBtn, #addFirstCategoryBtn').click(function() {
                if (!$('#restaurant_id').val()) {
                    alert('Please select a restaurant first');
                    return;
                }
                $('#addCategoryModal').removeClass('hidden');
            });

            // Show add menu item modal
            $('#addMenuItemBtn, #addFirstMenuItemBtn').click(function() {
                if (!$('#restaurant_id').val()) {
                    alert('Please select a restaurant first');
                    return;
                }
                $('#addMenuItemModal').removeClass('hidden');
            });

            // Show add subcategory modal
            $('.add-subcategory').click(function() {
                const categoryName = $(this).data('category');
                $('#parent_category').val(categoryName);
                $('#addSubcategoryModal').removeClass('hidden');
            });

            // Close modals
            $('.close-category-modal').click(function() {
                $('#addCategoryModal').addClass('hidden');
            });

            $('.close-menu-item-modal').click(function() {
                $('#addMenuItemModal').addClass('hidden');
            });

            $('.close-subcategory-modal').click(function() {
                $('#addSubcategoryModal').addClass('hidden');
            });

            $('.close-edit-modal').click(function() {
                $('#editModal').addClass('hidden');
            });

            $('.close-edit-category-modal').click(function() {
                $('#editCategoryModal').addClass('hidden');
            });

            // Edit item modal
            $('.edit-item').click(function() {
                const itemId = $(this).data('id');
                $('#editModal').removeClass('hidden');

                $.ajax({
                    url: 'get_menu_item.php',
                    method: 'GET',
                    data: {
                        id: itemId
                    },
                    success: function(response) {
                        $('#editFormContainer').html(response);
                    },
                    error: function() {
                        $('#editFormContainer').html('<div class="text-red-500">Failed to load item details.</div>');
                    }
                });
            });

            // Edit category modal
            $('.edit-category, .edit-subcategory').click(function() {
                const categoryId = $(this).data('id');
                $('#editCategoryModal').removeClass('hidden');

                $.ajax({
                    url: 'get_menu_category.php',
                    method: 'GET',
                    data: {
                        id: categoryId
                    },
                    success: function(response) {
                        $('#editCategoryFormContainer').html(response);
                    },
                    error: function() {
                        $('#editCategoryFormContainer').html('<div class="text-red-500">Failed to load category details.</div>');
                    }
                });
            });

            // Delete item
            $('.delete-item').click(function() {
                if (confirm('Are you sure you want to delete this menu item?')) {
                    const itemId = $(this).data('id');

                    $.ajax({
                        url: 'delete_menu_item.php',
                        method: 'POST',
                        data: {
                            id: itemId
                        },
                        success: function() {
                            window.location.reload();
                        },
                        error: function() {
                            alert('Failed to delete menu item');
                        }
                    });
                }
            });

             // Delete category
            $('.delete-category, .delete-subcategory').click(function() {
                const action = $(this).hasClass('delete-category') ?
                    'delete this category and ALL its subcategories and menu items?' :
                    'delete this subcategory and its menu items?';

                if (confirm('Are you sure you want to ' + action)) {
                    const categoryId = $(this).data('id');

                    $.ajax({
                        url: 'delete_menu_category.php',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            id: categoryId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                window.location.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Request failed: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>