<?php

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Handle language switch
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Complete translation array (copy from the complete_translations_file artifact)
$translations = [
    
    'en' => [
        'no_vendors' => 'No vendors available',
'no_customers' => 'No customers available',
        // Navigation & Header
        'inventory_system' => 'Inventory Management System',
        'welcome' => 'Welcome',
        'logout' => 'Log Out',
        'admin' => 'admin',
        
        // Main Menu
        'item' => 'Item',
        'purchase' => 'Purchase',
        'vendor' => 'Vendor',
        'sale' => 'Sale',
        'customer' => 'Customer',
        'search' => 'Search',
        'reports' => 'Reports',
        
        // Item Section
        'item_details' => 'Item Details',
        'upload_image' => 'Upload Image',
        'item_number' => 'Item Number',
        'product_id' => 'Product ID',
        'item_name' => 'Item Name',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'description' => 'Description',
        'discount' => 'Discount %',
        'quantity' => 'Quantity',
        'unit_price' => 'Unit Price',
        'total_stock' => 'Total Stock',
        'select_image' => 'Select Image',
        'item_image_upload_note' => 'You can upload an image for a particular item using this section.',
        'item_image_upload_note2' => 'Please make sure the item is already added to database before uploading the image.',
        
        // Purchase Section
        'purchase_details' => 'Purchase Details',
        'purchase_date' => 'Purchase Date',
        'purchase_id' => 'Purchase ID',
        'current_stock' => 'Current Stock',
        'vendor_name' => 'Vendor Name',
        'total_cost' => 'Total Cost',
        
        // Vendor Section
        'vendor_details' => 'Vendor Details',
        'full_name' => 'Full Name',
        'vendor_id' => 'Vendor ID',
        'phone_mobile' => 'Phone (mobile)',
        'phone_2' => 'Phone 2',
        'email' => 'Email',
        'address' => 'Address',
        'address_2' => 'Address 2',
        'city' => 'City',
        'district' => 'District',
        'phone_note' => 'Do not enter leading 0',
        'auto_generated_note' => 'This will be auto-generated when you add a new',
        'auto_filled_note' => 'This will be auto-filled when you enter the item number above',
        
        // Sale Section
        'sale_details' => 'Sale Details',
        'customer_id' => 'Customer ID',
        'customer_name' => 'Customer Name',
        'sale_id' => 'Sale ID',
        'sale_date' => 'Sale Date',
        'total' => 'Total',
        
        // Customer Section
        'customer_details' => 'Customer Details',
        
        // Search Section
        'search_inventory' => 'Search Inventory',
        'refresh' => 'Refresh',
        'search_all_items' => 'Use the grid below to search all details of items',
        'search_all_customers' => 'Use the grid below to search all details of customers',
        'search_sale_details' => 'Use the grid below to search sale details',
        'search_purchase_details' => 'Use the grid below to search purchase details',
        'search_vendor_details' => 'Use the grid below to search vendor details',
        
        // Reports Section
        'reports_title' => 'Reports',
        'get_item_reports' => 'Use the grid below to get reports for items',
        'get_customer_reports' => 'Use the grid below to get reports for customers',
        'get_sale_reports' => 'Use the grid below to get reports for sales',
        'get_purchase_reports' => 'Use the grid below to get reports for purchases',
        'get_vendor_reports' => 'Use the grid below to get reports for vendors',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'show_report' => 'Show Report',
        
        // Buttons
        'add_item' => 'Add Item',
        'add_purchase' => 'Add Purchase',
        'add_vendor' => 'Add Vendor',
        'add_sale' => 'Add Sale',
        'add_customer' => 'Add Customer',
        'update' => 'Update',
        'delete' => 'Delete',
        'clear' => 'Clear',
        'upload_image_btn' => 'Upload Image',
        'delete_image' => 'Delete Image',
        
        // Common
        'required' => 'Required',
    ],
    'km' => [
        // In English array:


// In Khmer array:
'no_vendors' => 'គ្មានអ្នកផ្គត់ផ្គង់ដែលអាចប្រើបាន',
'no_customers' => 'គ្មានអតិថិជនដែលអាចប្រើបាន',
        // Navigation & Header
        'inventory_system' => 'ប្រព័ន្ធគ្រប់គ្រងសារពើភ័ណ្ឌ',
        'welcome' => 'សូមស្វាគមន៍',
        'logout' => 'ចាកចេញ',
        'admin' => 'អ្នកគ្រប់គ្រង',
        
        // Main Menu
        'item' => 'ផលិតផល',
        'purchase' => 'ការទិញ',
        'vendor' => 'អ្នកផ្គត់ផ្គង់',
        'sale' => 'ការលក់',
        'customer' => 'អតិថិជន',
        'search' => 'ស្វែងរក',
        'reports' => 'របាយការណ៍',
        
        // Item Section
        'item_details' => 'ព័ត៌មានលម្អិតផលិតផល',
        'upload_image' => 'ផ្ទុករូបភាព',
        'item_number' => 'លេខផលិតផល',
        'product_id' => 'លេខសម្គាល់ផលិតផល',
        'item_name' => 'ឈ្មោះផលិតផល',
        'status' => 'ស្ថានភាព',
        'active' => 'សកម្ម',
        'inactive' => 'អសកម្ម',
        'description' => 'ការពណ៌នា',
        'discount' => 'បញ្ចុះតម្លៃ %',
        'quantity' => 'បរិមាណ',
        'unit_price' => 'តម្លៃឯកតា',
        'total_stock' => 'ស្តុកសរុប',
        'select_image' => 'ជ្រើសរើសរូបភាព',
        'item_image_upload_note' => 'អ្នកអាចផ្ទុករូបភាពសម្រាប់ផលិតផលជាក់លាក់មួយដោយប្រើផ្នែកនេះ។',
        'item_image_upload_note2' => 'សូមប្រាកដថាផលិតផលត្រូវបានបន្ថែមរួចហើយក្នុងមូលដ្ឋានទិន្នន័យមុនពេលផ្ទុករូបភាព។',
        
        // Purchase Section
        'purchase_details' => 'ព័ត៌មានលម្អិតនៃការទិញ',
        'purchase_date' => 'កាលបរិច្ឆេទទិញ',
        'purchase_id' => 'លេខសម្គាល់ការទិញ',
        'current_stock' => 'ស្តុកបច្ចុប្បន្ន',
        'vendor_name' => 'ឈ្មោះអ្នកផ្គត់ផ្គង់',
        'total_cost' => 'តម្លៃសរុប',
        
        // Vendor Section
        'vendor_details' => 'ព័ត៌មានលម្អិតអ្នកផ្គត់ផ្គង់',
        'full_name' => 'ឈ្មោះពេញ',
        'vendor_id' => 'លេខសម្គាល់អ្នកផ្គត់ផ្គង់',
        'phone_mobile' => 'ទូរស័ព្ទ (ចល័ត)',
        'phone_2' => 'ទូរស័ព្ទ ២',
        'email' => 'អ៊ីមែល',
        'address' => 'អាសយដ្ឋាន',
        'address_2' => 'អាសយដ្ឋាន ២',
        'city' => 'ទីក្រុង',
        'district' => 'ស្រុក/ខណ្ឌ',
        'phone_note' => 'មិនត្រូវបញ្ចូលលេខ 0 នៅមុខ',
        'auto_generated_note' => 'នេះនឹងត្រូវបានបង្កើតដោយស្វ័យប្រវត្តិនៅពេលអ្នកបន្ថែមថ្មី',
        'auto_filled_note' => 'នេះនឹងត្រូវបានបំពេញដោយស្វ័យប្រវត្តិនៅពេលអ្នកបញ្ចូលលេខផលិតផលខាងលើ',
        
        // Sale Section
        'sale_details' => 'ព័ត៌មានលម្អិតនៃការលក់',
        'customer_id' => 'លេខសម្គាល់អតិថិជន',
        'customer_name' => 'ឈ្មោះអតិថិជន',
        'sale_id' => 'លេខសម្គាល់ការលក់',
        'sale_date' => 'កាលបរិច្ឆេទលក់',
        'total' => 'សរុប',
        
        // Customer Section
        'customer_details' => 'ព័ត៌មានលម្អិតអតិថិជន',
        
        // Search Section
        'search_inventory' => 'ស្វែងរកសារពើភ័ណ្ឌ',
        'refresh' => 'ធ្វើឱ្យស្រស់',
        'search_all_items' => 'ប្រើក្រឡាខាងក្រោមដើម្បីស្វែងរកព័ត៌មានលម្អិតទាំងអស់នៃផលិតផល',
        'search_all_customers' => 'ប្រើក្រឡាខាងក្រោមដើម្បីស្វែងរកព័ត៌មានលម្អិតទាំងអស់នៃអតិថិជន',
        'search_sale_details' => 'ប្រើក្រឡាខាងក្រោមដើម្បីស្វែងរកព័ត៌មានលម្អិតនៃការលក់',
        'search_purchase_details' => 'ប្រើក្រឡាខាងក្រោមដើម្បីស្វែងរកព័ត៌មានលម្អិតនៃការទិញ',
        'search_vendor_details' => 'ប្រើក្រឡាខាងក្រោមដើម្បីស្វែងរកព័ត៌មានលម្អិតនៃអ្នកផ្គត់ផ្គង់',
        
        // Reports Section
        'reports_title' => 'របាយការណ៍',
        'get_item_reports' => 'ប្រើក្រឡាខាងក្រោមដើម្បីទទួលបានរបាយការណ៍សម្រាប់ផលិតផល',
        'get_customer_reports' => 'ប្រើក្រឡាខាងក្រោមដើម្បីទទួលបានរបាយការណ៍សម្រាប់អតិថិជន',
        'get_sale_reports' => 'ប្រើក្រឡាខាងក្រោមដើម្បីទទួលបានរបាយការណ៍សម្រាប់ការលក់',
        'get_purchase_reports' => 'ប្រើក្រឡាខាងក្រោមដើម្បីទទួលបានរបាយការណ៍សម្រាប់ការទិញ',
        'get_vendor_reports' => 'ប្រើក្រឡាខាងក្រោមដើម្បីទទួលបានរបាយការណ៍សម្រាប់អ្នកផ្គត់ផ្គង់',
        'start_date' => 'កាលបរិច្ឆេទចាប់ផ្តើម',
        'end_date' => 'កាលបរិច្ឆេទបញ្ចប់',
        'show_report' => 'បង្ហាញរបាយការណ៍',
        
        // Buttons
        'add_item' => 'បន្ថែមផលិតផល',
        'add_purchase' => 'បន្ថែមការទិញ',
        'add_vendor' => 'បន្ថែមអ្នកផ្គត់ផ្គង់',
        'add_sale' => 'បន្ថែមការលក់',
        'add_customer' => 'បន្ថែមអតិថិជន',
        'update' => 'កែប្រែ',
        'delete' => 'លុប',
        'clear' => 'សម្អាត',
        'upload_image_btn' => 'ផ្ទុករូបភាព',
        'delete_image' => 'លុបរូបភាព',
        
        // Common
        'required' => 'ទាមទារ',
    ]
];

function t($key) {
    global $translations;
    $lang = $_SESSION['lang'];
    return isset($translations[$lang][$key]) ? $translations[$lang][$key] : $key;
}
?>