<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    $db->where('id', $category_id);
    $category = $db->getOne('menu_categories');

    if ($category) {
        // Get all categories for the restaurant (for parent category selection)
        $db->where('restaurant_id', $category['restaurant_id']);
        $db->where('id', $category_id, '!='); // Exclude current category
        $categories = $db->get('menu_categories');
?>
        <form id="editCategoryForm" method="post" action="update_menu_category.php" class="space-y-4">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">

            <div>
                <label for="edit_category_name" class="block text-sm font-medium text-gray-700">Category Name *</label>
                <input type="text" id="edit_category_name" name="category" value="<?= htmlspecialchars($category['category']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="edit_subcategory" class="block text-sm font-medium text-gray-700">Subcategory</label>
                <input type="text" id="edit_subcategory" name="subcategory" value="<?= htmlspecialchars($category['subcategory']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save Changes
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-edit-category-modal">
                    Cancel
                </button>
            </div>
        </form>
<?php
    } else {
        echo '<div class="text-red-500">Category not found.</div>';
    }
} else {
    echo '<div class="text-red-500">Invalid request.</div>';
}
?>