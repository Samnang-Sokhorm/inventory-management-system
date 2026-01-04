<?php
/**
 * first index.php
 */
session_start();

// Redirect the user to login page if he is not logged in.
if(!isset($_SESSION['loggedIn'])){
    header('Location: login.php');
    exit();
}

// Include language file
require_once('inc/language.php');

require_once('inc/config/constants.php');
require_once('inc/config/db.php');
require_once('inc/header.html');
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('inventory_system'); ?></title>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Font for Khmer language -->
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600&display=swap" rel="stylesheet">
    
   <style>
    /* Language-specific font styling */
    :lang(km) * {
        font-family: 'Kantumruy Pro', 'Khmer OS', sans-serif;
    }
    
    /* Ensure language switcher is visible */
    .language-switcher {
        display: flex !important;
        gap: 5px;
        margin-right: 15px;
    }
    
    .language-switcher a {
        min-width: 40px;
        text-align: center;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 3px;
        font-weight: 500;
        font-size: 14px;
    }
    
    .language-switcher .active {
        background: #007bff !important;
        color: white !important;
        border: 1px solid #007bff;
    }
    
    .language-switcher :not(.active) {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .requiredIcon {
        color: red;
    }
    
    .blueText {
        color: #007bff;
    }
    
    .nav-link.active {
        background-color: #007bff !important;
        color: white !important;
    }
    
    body {
        padding-top: 70px;
    }
    
    /* Bulk upload styles */
    .preview-table {
        font-size: 12px;
    }
    
    .preview-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 5px;
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .upload-area:hover {
        border-color: #007bff;
        background-color: #e9ecef;
    }
    
    .upload-area i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .upload-area.dragover {
        border-color: #28a745;
        background-color: #d4edda;
    }
    
    .template-download {
        border-left: 4px solid #007bff;
        padding-left: 15px;
    }
</style>
</head>
<body>

<!-- Include Navigation -->
<?php 
// First, let's check if we have navigation file
if (file_exists('inc/navigation.php')) {
    require_once('inc/navigation.php');
} else {
    // Create a simple navigation if file doesn't exist
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="https://numer.digital/public/template/university/images/logo/num.png" 
                     width="30" height="30" class="mr-2" alt="Logo">
                <?php echo t('inventory_system'); ?>
            </a>
            
            <!-- Simple language switcher -->
            <div class="language-switcher">
                <a href="?lang=en" class="<?php echo ($_SESSION['lang'] == 'en') ? 'active' : ''; ?>">EN</a>
                <a href="?lang=km" class="<?php echo ($_SESSION['lang'] == 'km') ? 'active' : ''; ?>">ខ្មែរ</a>
            </div>
            
            <div class="ml-auto d-flex align-items-center">
                <span class="text-white mr-3"><?php echo t('welcome'); ?> <?php echo htmlspecialchars($_SESSION['fullName'] ?? 'Admin'); ?></span>
                <a href="model/login/logout.php" class="btn btn-outline-light btn-sm"><?php echo t('logout'); ?></a>
            </div>
        </div>
    </nav>
    <?php
}
?>

<!-- Page Content -->
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-2">
      <h1 class="my-4"></h1>
      <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <a class="nav-link active" id="v-pills-item-tab" data-toggle="pill" href="#v-pills-item" role="tab" aria-controls="v-pills-item" aria-selected="true"><?php echo t('item'); ?></a>
        <a class="nav-link" id="v-pills-purchase-tab" data-toggle="pill" href="#v-pills-purchase" role="tab" aria-controls="v-pills-purchase" aria-selected="false"><?php echo t('purchase'); ?></a>
        <a class="nav-link" id="v-pills-vendor-tab" data-toggle="pill" href="#v-pills-vendor" role="tab" aria-controls="v-pills-vendor" aria-selected="false"><?php echo t('vendor'); ?></a>
        <a class="nav-link" id="v-pills-sale-tab" data-toggle="pill" href="#v-pills-sale" role="tab" aria-controls="v-pills-sale" aria-selected="false"><?php echo t('sale'); ?></a>
        <a class="nav-link" id="v-pills-customer-tab" data-toggle="pill" href="#v-pills-customer" role="tab" aria-controls="v-pills-customer" aria-selected="false"><?php echo t('customer'); ?></a>
        <a class="nav-link" id="v-pills-search-tab" data-toggle="pill" href="#v-pills-search" role="tab" aria-controls="v-pills-search" aria-selected="false"><?php echo t('search'); ?></a>
        <a class="nav-link" id="v-pills-reports-tab" data-toggle="pill" href="#v-pills-reports" role="tab" aria-controls="v-pills-reports" aria-selected="false"><?php echo t('reports'); ?></a>
      </div>
    </div>
    
    <div class="col-lg-10">
      <div class="tab-content" id="v-pills-tabContent">
        
        <!-- ITEM TAB -->
        <div class="tab-pane fade show active" id="v-pills-item" role="tabpanel" aria-labelledby="v-pills-item-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header"><?php echo t('item_details'); ?></div>
            <div class="card-body">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#itemDetailsTab"><?php echo t('item'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#itemImageTab"><?php echo t('upload_image'); ?></a>
                </li>
              </ul>
              
              <div class="tab-content">
                <div id="itemDetailsTab" class="container-fluid tab-pane active">
                  <br>
                  <div id="itemDetailsMessage"></div>
                  <form>
                    <div class="form-row">
                      <div class="form-group col-md-3" style="display:inline-block">
                        <label for="itemDetailsItemNumber"><?php echo t('item_number'); ?><span class="requiredIcon">*</span></label>
                        <input type="text" class="form-control" name="itemDetailsItemNumber" id="itemDetailsItemNumber" autocomplete="off">
                        <div id="itemDetailsItemNumberSuggestionsDiv" class="customListDivWidth"></div>
                      </div>
                      <div class="form-group col-md-3">
                        <label for="itemDetailsProductID"><?php echo t('product_id'); ?></label>
                        <input class="form-control invTooltip" type="number" readonly id="itemDetailsProductID" name="itemDetailsProductID" title="<?php echo t('auto_generated_note'); ?> <?php echo strtolower(t('item')); ?>">
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="itemDetailsItemName"><?php echo t('item_name'); ?><span class="requiredIcon">*</span></label>
                        <input type="text" class="form-control" name="itemDetailsItemName" id="itemDetailsItemName" autocomplete="off">
                        <div id="itemDetailsItemNameSuggestionsDiv" class="customListDivWidth"></div>
                      </div>
                      <div class="form-group col-md-2">
                        <label for="itemDetailsStatus"><?php echo t('status'); ?></label>
                        <select id="itemDetailsStatus" name="itemDetailsStatus" class="form-control chosenSelect">
                          <?php include('inc/statusList.html'); ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-6" style="display:inline-block">
                        <textarea rows="4" class="form-control" placeholder="<?php echo t('description'); ?>" name="itemDetailsDescription" id="itemDetailsDescription"></textarea>
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label for="itemDetailsDiscount"><?php echo t('discount'); ?></label>
                        <input type="text" class="form-control" value="0" name="itemDetailsDiscount" id="itemDetailsDiscount">
                      </div>
                      <div class="form-group col-md-3">
                        <label for="itemDetailsQuantity"><?php echo t('quantity'); ?><span class="requiredIcon">*</span></label>
                        <input type="number" class="form-control" value="0" name="itemDetailsQuantity" id="itemDetailsQuantity">
                      </div>
                      <div class="form-group col-md-3">
                        <label for="itemDetailsUnitPrice"><?php echo t('unit_price'); ?><span class="requiredIcon">*</span></label>
                        <input type="text" class="form-control" value="0" name="itemDetailsUnitPrice" id="itemDetailsUnitPrice">
                      </div>
                      <div class="form-group col-md-3">
                        <label for="itemDetailsTotalStock"><?php echo t('total_stock'); ?></label>
                        <input type="text" class="form-control" name="itemDetailsTotalStock" id="itemDetailsTotalStock" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <div id="imageContainer"></div>
                      </div>
                    </div>
                    <button type="button" id="addItem" class="btn btn-success"><?php echo t('add_item'); ?></button>
                    <button type="button" id="updateItemDetailsButton" class="btn btn-primary"><?php echo t('update'); ?></button>
                    <button type="button" id="deleteItem" class="btn btn-danger"><?php echo t('delete'); ?></button>
                    <button type="reset" class="btn" id="itemClear"><?php echo t('clear'); ?></button>
                  </form>
                </div>
                
                <div id="itemImageTab" class="container-fluid tab-pane fade">
                  <br>
                  <div id="itemImageMessage"></div>
                  <p><?php echo t('item_image_upload_note'); ?></p>
                  <p><?php echo t('item_image_upload_note2'); ?></p>
                  <br>
                  <form name="imageForm" id="imageForm" method="post">
                    <div class="form-row">
                      <div class="form-group col-md-3" style="display:inline-block">
                        <label for="itemImageItemNumber"><?php echo t('item_number'); ?><span class="requiredIcon">*</span></label>
                        <input type="text" class="form-control" name="itemImageItemNumber" id="itemImageItemNumber" autocomplete="off">
                        <div id="itemImageItemNumberSuggestionsDiv" class="customListDivWidth"></div>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="itemImageItemName"><?php echo t('item_name'); ?></label>
                        <input type="text" class="form-control" name="itemImageItemName" id="itemImageItemName" readonly>
                      </div>
                    </div>
                    <br>
                    <div class="form-row">
                      <div class="form-group col-md-7">
                        <label for="itemImageFile"><?php echo t('select_image'); ?> ( <span class="blueText">jpg</span>, <span class="blueText">jpeg</span>, <span class="blueText">gif</span>, <span class="blueText">png</span> only )</label>
                        <input type="file" class="form-control-file btn btn-dark" id="itemImageFile" name="itemImageFile">
                      </div>
                    </div>
                    <br>
                    <button type="button" id="updateImageButton" class="btn btn-primary"><?php echo t('upload_image_btn'); ?></button>
                    <button type="button" id="deleteImageButton" class="btn btn-danger"><?php echo t('delete_image'); ?></button>
                    <button type="reset" class="btn"><?php echo t('clear'); ?></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- PURCHASE TAB -->
        <div class="tab-pane fade" id="v-pills-purchase" role="tabpanel" aria-labelledby="v-pills-purchase-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header"><?php echo t('purchase_details'); ?></div>
            <div class="card-body">
              <div id="purchaseDetailsMessage"></div>
              <form>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="purchaseDetailsItemNumber"><?php echo t('item_number'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control" id="purchaseDetailsItemNumber" name="purchaseDetailsItemNumber" autocomplete="off">
                    <div id="purchaseDetailsItemNumberSuggestionsDiv" class="customListDivWidth"></div>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="purchaseDetailsPurchaseDate"><?php echo t('purchase_date'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control datepicker" id="purchaseDetailsPurchaseDate" name="purchaseDetailsPurchaseDate" readonly value="<?php echo date('Y-m-d'); ?>">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="purchaseDetailsPurchaseID"><?php echo t('purchase_id'); ?></label>
                    <input type="text" class="form-control invTooltip" id="purchaseDetailsPurchaseID" name="purchaseDetailsPurchaseID" title="<?php echo t('auto_generated_note'); ?> <?php echo strtolower(t('purchase')); ?>" autocomplete="off">
                    <div id="purchaseDetailsPurchaseIDSuggestionsDiv" class="customListDivWidth"></div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="purchaseDetailsItemName"><?php echo t('item_name'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control invTooltip" id="purchaseDetailsItemName" name="purchaseDetailsItemName" readonly title="<?php echo t('auto_filled_note'); ?>">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="purchaseDetailsCurrentStock"><?php echo t('current_stock'); ?></label>
                    <input type="text" class="form-control" id="purchaseDetailsCurrentStock" name="purchaseDetailsCurrentStock" readonly>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="purchaseDetailsVendorName"><?php echo t('vendor_name'); ?><span class="requiredIcon">*</span></label>
                    <select id="purchaseDetailsVendorName" name="purchaseDetailsVendorName" class="form-control chosenSelect">
                      <?php 
                      // Check if vendor names file exists
                      if (file_exists('model/vendor/getVendorNames.php')) {
                          require('model/vendor/getVendorNames.php');
                      } else {
                          echo '<option value="">' . t('no_vendors') . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-2">
                    <label for="purchaseDetailsQuantity"><?php echo t('quantity'); ?><span class="requiredIcon">*</span></label>
                    <input type="number" class="form-control" id="purchaseDetailsQuantity" name="purchaseDetailsQuantity" value="0">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="purchaseDetailsUnitPrice"><?php echo t('unit_price'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control" id="purchaseDetailsUnitPrice" name="purchaseDetailsUnitPrice" value="0">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="purchaseDetailsTotal"><?php echo t('total_cost'); ?></label>
                    <input type="text" class="form-control" id="purchaseDetailsTotal" name="purchaseDetailsTotal" readonly>
                  </div>
                </div>
                <button type="button" id="addPurchase" class="btn btn-success"><?php echo t('add_purchase'); ?></button>
                <button type="button" id="updatePurchaseDetailsButton" class="btn btn-primary"><?php echo t('update'); ?></button>
                <button type="reset" class="btn"><?php echo t('clear'); ?></button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- VENDOR TAB -->
        <div class="tab-pane fade" id="v-pills-vendor" role="tabpanel" aria-labelledby="v-pills-vendor-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header"><?php echo t('vendor_details'); ?></div>
            <div class="card-body">
              <div id="vendorDetailsMessage"></div>
              <form>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="vendorDetailsFullName"><?php echo t('full_name'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control" id="vendorDetailsFullName" name="vendorDetailsFullName" autocomplete="off">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="vendorDetailsVendorID"><?php echo t('vendor_id'); ?></label>
                    <input type="text" class="form-control invTooltip" id="vendorDetailsVendorID" name="vendorDetailsVendorID" readonly title="<?php echo t('auto_generated_note'); ?> <?php echo strtolower(t('vendor')); ?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="vendorDetailsPhone"><?php echo t('phone_mobile'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control invTooltip" id="vendorDetailsPhone" name="vendorDetailsPhone" autocomplete="off" title="<?php echo t('phone_note'); ?>">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="vendorDetailsPhone2"><?php echo t('phone_2'); ?></label>
                    <input type="text" class="form-control" id="vendorDetailsPhone2" name="vendorDetailsPhone2" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="vendorDetailsEmail"><?php echo t('email'); ?></label>
                    <input type="email" class="form-control" id="vendorDetailsEmail" name="vendorDetailsEmail" autocomplete="off">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="vendorDetailsAddress"><?php echo t('address'); ?></label>
                    <input type="text" class="form-control" id="vendorDetailsAddress" name="vendorDetailsAddress" autocomplete="off">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="vendorDetailsCity"><?php echo t('city'); ?></label>
                    <input type="text" class="form-control" id="vendorDetailsCity" name="vendorDetailsCity" autocomplete="off">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="vendorDetailsDistrict"><?php echo t('district'); ?></label>
                    <input type="text" class="form-control" id="vendorDetailsDistrict" name="vendorDetailsDistrict" autocomplete="off">
                  </div>
                </div>
                <button type="button" id="addVendor" class="btn btn-success"><?php echo t('add_vendor'); ?></button>
                <button type="button" id="updateVendorDetailsButton" class="btn btn-primary"><?php echo t('update'); ?></button>
                <button type="button" id="deleteVendor" class="btn btn-danger"><?php echo t('delete'); ?></button>
                <button type="reset" class="btn"><?php echo t('clear'); ?></button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- SALE TAB -->
        <div class="tab-pane fade" id="v-pills-sale" role="tabpanel" aria-labelledby="v-pills-sale-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header"><?php echo t('sale_details'); ?></div>
            <div class="card-body">
              <div id="saleDetailsMessage"></div>
              <form>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="saleDetailsItemNumber"><?php echo t('item_number'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control" id="saleDetailsItemNumber" name="saleDetailsItemNumber" autocomplete="off">
                    <div id="saleDetailsItemNumberSuggestionsDiv" class="customListDivWidth"></div>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="saleDetailsSaleDate"><?php echo t('sale_date'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control datepicker" id="saleDetailsSaleDate" name="saleDetailsSaleDate" readonly value="<?php echo date('Y-m-d'); ?>">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="saleDetailsSaleID"><?php echo t('sale_id'); ?></label>
                    <input type="text" class="form-control invTooltip" id="saleDetailsSaleID" name="saleDetailsSaleID" title="<?php echo t('auto_generated_note'); ?> <?php echo strtolower(t('sale')); ?>" autocomplete="off">
                    <div id="saleDetailsSaleIDSuggestionsDiv" class="customListDivWidth"></div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="saleDetailsItemName"><?php echo t('item_name'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control invTooltip" id="saleDetailsItemName" name="saleDetailsItemName" readonly title="<?php echo t('auto_filled_note'); ?>">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="saleDetailsCurrentStock"><?php echo t('current_stock'); ?></label>
                    <input type="text" class="form-control" id="saleDetailsCurrentStock" name="saleDetailsCurrentStock" readonly>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="saleDetailsCustomerName"><?php echo t('customer_name'); ?><span class="requiredIcon">*</span></label>
                    <select id="saleDetailsCustomerName" name="saleDetailsCustomerName" class="form-control chosenSelect">
                      <?php 
                      if (file_exists('model/customer/getCustomerNames.php')) {
                          require('model/customer/getCustomerNames.php');
                      } else {
                          echo '<option value="">' . t('no_customers') . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-2">
                    <label for="saleDetailsQuantity"><?php echo t('quantity'); ?><span class="requiredIcon">*</span></label>
                    <input type="number" class="form-control" id="saleDetailsQuantity" name="saleDetailsQuantity" value="0">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="saleDetailsUnitPrice"><?php echo t('unit_price'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control" id="saleDetailsUnitPrice" name="saleDetailsUnitPrice" value="0">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="saleDetailsTotal"><?php echo t('total'); ?></label>
                    <input type="text" class="form-control" id="saleDetailsTotal" name="saleDetailsTotal" readonly>
                  </div>
                </div>
                <button type="button" id="addSale" class="btn btn-success"><?php echo t('add_sale'); ?></button>
                <button type="button" id="updateSaleDetailsButton" class="btn btn-primary"><?php echo t('update'); ?></button>
                <button type="reset" class="btn"><?php echo t('clear'); ?></button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- CUSTOMER TAB -->
        <div class="tab-pane fade" id="v-pills-customer" role="tabpanel" aria-labelledby="v-pills-customer-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header"><?php echo t('customer_details'); ?></div>
            <div class="card-body">
              <div id="customerDetailsMessage"></div>
              <form>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="customerDetailsCustomerFullName"><?php echo t('full_name'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control" id="customerDetailsCustomerFullName" name="customerDetailsCustomerFullName" autocomplete="off">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="customerDetailsStatus"><?php echo t('status'); ?></label>
                    <select id="customerDetailsStatus" name="customerDetailsStatus" class="form-control chosenSelect">
                      <?php include('inc/statusList.html'); ?>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="customerDetailsCustomerID"><?php echo t('customer_id'); ?></label>
                    <input type="text" class="form-control invTooltip" id="customerDetailsCustomerID" name="customerDetailsCustomerID" title="<?php echo t('auto_generated_note'); ?> <?php echo strtolower(t('customer')); ?>" autocomplete="off">
                    <div id="customerDetailsCustomerIDSuggestionsDiv" class="customListDivWidth"></div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="customerDetailsCustomerMobile"><?php echo t('phone_mobile'); ?><span class="requiredIcon">*</span></label>
                    <input type="text" class="form-control invTooltip" id="customerDetailsCustomerMobile" name="customerDetailsCustomerMobile" title="<?php echo t('phone_note'); ?>" autocomplete="off">
                  </div>
                  <div class="form-group col-md-3">
                    <label for="customerDetailsCustomerPhone2"><?php echo t('phone_2'); ?></label>
                    <input type="text" class="form-control invTooltip" id="customerDetailsCustomerPhone2" name="customerDetailsCustomerPhone2" title="<?php echo t('phone_note'); ?>" autocomplete="off">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="customerDetailsCustomerEmail"><?php echo t('email'); ?></label>
                    <input type="email" class="form-control" id="customerDetailsCustomerEmail" name="customerDetailsCustomerEmail" autocomplete="off">
                  </div>
                </div>
                <div class="form-group">
                  <label for="customerDetailsCustomerAddress"><?php echo t('address'); ?><span class="requiredIcon">*</span></label>
                  <input type="text" class="form-control" id="customerDetailsCustomerAddress" name="customerDetailsCustomerAddress" autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="customerDetailsCustomerAddress2"><?php echo t('address_2'); ?></label>
                  <input type="text" class="form-control" id="customerDetailsCustomerAddress2" name="customerDetailsCustomerAddress2" autocomplete="off">
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="customerDetailsCustomerCity"><?php echo t('city'); ?></label>
                    <input type="text" class="form-control" id="customerDetailsCustomerCity" name="customerDetailsCustomerCity" autocomplete="off">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="customerDetailsCustomerDistrict"><?php echo t('district'); ?></label>
                    <select id="customerDetailsCustomerDistrict" name="customerDetailsCustomerDistrict" class="form-control chosenSelect">
                      <?php include('inc/districtList.html'); ?>
                    </select>
                  </div>
                </div>
                <button type="button" id="addCustomer" name="addCustomer" class="btn btn-success"><?php echo t('add_customer'); ?></button>
                <button type="button" id="updateCustomerDetailsButton" class="btn btn-primary"><?php echo t('update'); ?></button>
                <button type="button" id="deleteCustomerButton" class="btn btn-danger"><?php echo t('delete'); ?></button>
                <button type="reset" class="btn"><?php echo t('clear'); ?></button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- SEARCH TAB -->
        <div class="tab-pane fade" id="v-pills-search" role="tabpanel" aria-labelledby="v-pills-search-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header">
              <?php echo t('search_inventory'); ?>
              <button id="searchTablesRefresh" name="searchTablesRefresh" class="btn btn-warning float-right btn-sm"><?php echo t('refresh'); ?></button>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#itemSearchTab"><?php echo t('item'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#customerSearchTab"><?php echo t('customer'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#saleSearchTab"><?php echo t('sale'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#purchaseSearchTab"><?php echo t('purchase'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#vendorSearchTab"><?php echo t('vendor'); ?></a>
                </li>
              </ul>
              
              <div class="tab-content">
                <div id="itemSearchTab" class="container-fluid tab-pane active">
                  <br>
                  <p><?php echo t('search_all_items'); ?></p>
                  <div class="table-responsive" id="itemDetailsTableDiv"></div>
                </div>
                <div id="customerSearchTab" class="container-fluid tab-pane fade">
                  <br>
                  <p><?php echo t('search_all_customers'); ?></p>
                  <div class="table-responsive" id="customerDetailsTableDiv"></div>
                </div>
                <div id="saleSearchTab" class="container-fluid tab-pane fade">
                  <br>
                  <p><?php echo t('search_sale_details'); ?></p>
                  <div class="table-responsive" id="saleDetailsTableDiv"></div>
                </div>
                <div id="purchaseSearchTab" class="container-fluid tab-pane fade">
                  <br>
                  <p><?php echo t('search_purchase_details'); ?></p>
                  <div class="table-responsive" id="purchaseDetailsTableDiv"></div>
                </div>
                <div id="vendorSearchTab" class="container-fluid tab-pane fade">
                  <br>
                  <p><?php echo t('search_vendor_details'); ?></p>
                  <div class="table-responsive" id="vendorDetailsTableDiv"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- REPORTS TAB -->
        <div class="tab-pane fade" id="v-pills-reports" role="tabpanel" aria-labelledby="v-pills-reports-tab">
          <div class="card card-outline-secondary my-4">
            <div class="card-header">
              <?php echo t('reports_title'); ?>
              <button id="reportsTablesRefresh" name="reportsTablesRefresh" class="btn btn-warning float-right btn-sm"><?php echo t('refresh'); ?></button>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#itemReportsTab"><?php echo t('item'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#customerReportsTab"><?php echo t('customer'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#saleReportsTab"><?php echo t('sale'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#purchaseReportsTab"><?php echo t('purchase'); ?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#vendorReportsTab"><?php echo t('vendor'); ?></a>
                </li>
              </ul>
              
              <div class="tab-content">
                <div id="itemReportsTab" class="container-fluid tab-pane active">
                  <br>
                  <div class="row mb-4">
                    <div class="col-md-8">
                      <p><?php echo t('get_item_reports'); ?></p>
                    </div>
                    <div class="col-md-4 text-right">
                      <!-- Bulk Upload Button -->
                      <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#bulkUploadModal">
                        <i class="fas fa-upload"></i> <?php echo t('bulk_upload_items'); ?>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Bulk Upload Modal -->
                  <div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title" id="bulkUploadModalLabel">
                            <i class="fas fa-file-upload"></i> <?php echo t('bulk_upload_items'); ?>
                          </h5>
                          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-5">
                              <div class="template-download mb-4">
                                <h6><i class="fas fa-file-download text-primary"></i> <?php echo t('download_template'); ?></h6>
                                <p class="small"><?php echo t('template_description'); ?></p>
                                <button type="button" id="downloadTemplate" class="btn btn-outline-primary btn-sm btn-block">
                                  <i class="fas fa-download"></i> <?php echo t('download_csv_template'); ?>
                                </button>
                              </div>
                              
                              <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> <?php echo t('upload_instructions'); ?></h6>
                                <ul class="mb-0 small">
                                  <li><i class="fas fa-check-circle text-success"></i> <?php echo t('csv_only'); ?></li>
                                  <li><i class="fas fa-check-circle text-success"></i> <?php echo t('max_file_size'); ?>: 5MB</li>
                                  <li><i class="fas fa-check-circle text-success"></i> <?php echo t('required_fields'); ?>: Item Number, Item Name</li>
                                  <li><i class="fas fa-check-circle text-success"></i> <?php echo t('existing_items'); ?></li>
                                </ul>
                              </div>
                              
                              <div class="card mt-3">
                                <div class="card-header bg-light">
                                  <h6 class="mb-0"><i class="fas fa-columns"></i> <?php echo t('csv_format'); ?></h6>
                                </div>
                                <div class="card-body p-2">
                                  <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                      <tr>
                                        <th class="py-1">Column</th>
                                        <th class="py-1">Required</th>
                                        <th class="py-1">Example</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <td class="py-1">itemNumber</td>
                                        <td class="py-1"><span class="badge badge-danger">Required</span></td>
                                        <td class="py-1">ITEM001</td>
                                      </tr>
                                      <tr>
                                        <td class="py-1">itemName</td>
                                        <td class="py-1"><span class="badge badge-danger">Required</span></td>
                                        <td class="py-1">School Bag</td>
                                      </tr>
                                      <tr>
                                        <td class="py-1">discount</td>
                                        <td class="py-1"><span class="badge badge-secondary">Optional</span></td>
                                        <td class="py-1">5.5</td>
                                      </tr>
                                      <tr>
                                        <td class="py-1">quantity</td>
                                        <td class="py-1"><span class="badge badge-warning">Recommended</span></td>
                                        <td class="py-1">100</td>
                                      </tr>
                                      <tr>
                                        <td class="py-1">unitPrice</td>
                                        <td class="py-1"><span class="badge badge-warning">Recommended</span></td>
                                        <td class="py-1">25.99</td>
                                      </tr>
                                      <tr>
                                        <td class="py-1">status</td>
                                        <td class="py-1"><span class="badge badge-secondary">Optional</span></td>
                                        <td class="py-1">Active</td>
                                      </tr>
                                      <tr>
                                        <td class="py-1">description</td>
                                        <td class="py-1"><span class="badge badge-secondary">Optional</span></td>
                                        <td class="py-1">Red school bag</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-md-7">
                              <div class="upload-area" id="dropArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h5><?php echo t('drag_drop_file'); ?></h5>
                                <p class="text-muted"><?php echo t('or_click_to_browse'); ?></p>
                                <input type="file" class="d-none" id="bulkUploadFile" name="bulkUploadFile" accept=".csv">
                                <button type="button" class="btn btn-outline-primary mt-2" id="browseFileBtn">
                                  <i class="fas fa-folder-open"></i> <?php echo t('browse_files'); ?>
                                </button>
                                <p class="small text-muted mt-2 mb-0" id="selectedFileName"></p>
                              </div>
                              
                              <div class="form-group mt-3">
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" id="updateExisting" name="updateExisting" checked>
                                  <label class="custom-control-label" for="updateExisting">
                                    <strong><?php echo t('update_existing_items'); ?></strong>
                                  </label>
                                  <small class="form-text text-muted">
                                    <?php echo t('update_existing_note'); ?>
                                  </small>
                                </div>
                              </div>
                              
                              <div id="filePreview" class="mt-3 d-none">
                                <div class="card">
                                  <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-eye"></i> <?php echo t('file_preview'); ?></h6>
                                    <span class="badge badge-info" id="previewRowCount"></span>
                                  </div>
                                  <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                      <table class="table table-sm table-bordered mb-0 preview-table" id="previewTable">
                                        <thead class="thead-light sticky-top">
                                          <tr>
                                            <th>Item Number</th>
                                            <th>Item Name</th>
                                            <th>Discount</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <!-- Preview rows will be inserted here -->
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> <?php echo t('cancel'); ?>
                          </button>
                          <button type="button" class="btn btn-primary" id="uploadCSV" disabled>
                            <i class="fas fa-upload"></i> <?php echo t('upload_and_process'); ?>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Upload Results will be displayed here -->
                  <div id="uploadResultsContainer"></div>
                  
                  <!-- Item Reports Table -->
                  <div class="table-responsive" id="itemReportsTableDiv"></div>
                </div>
                
                <div id="customerReportsTab" class="container-fluid tab-pane fade">
                  <br>
                  <p><?php echo t('get_customer_reports'); ?></p>
                  <div class="table-responsive" id="customerReportsTableDiv"></div>
                </div>
                <div id="saleReportsTab" class="container-fluid tab-pane fade">
                  <br>
                  <form>
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label for="saleReportStartDate"><?php echo t('start_date'); ?></label>
                        <input type="text" class="form-control datepicker" id="saleReportStartDate" value="<?php echo date('Y-m-d'); ?>" name="saleReportStartDate" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label for="saleReportEndDate"><?php echo t('end_date'); ?></label>
                        <input type="text" class="form-control datepicker" id="saleReportEndDate" value="<?php echo date('Y-m-d'); ?>" name="saleReportEndDate" readonly>
                      </div>
                    </div>
                    <button type="button" id="showSaleReport" class="btn btn-dark"><?php echo t('show_report'); ?></button>
                    <button type="reset" id="saleFilterClear" class="btn"><?php echo t('clear'); ?></button>
                  </form>
                  <br><br>
                  <div class="table-responsive" id="saleReportsTableDiv"></div>
                </div>
                <div id="purchaseReportsTab" class="container-fluid tab-pane fade">
                  <br>
                  <form>
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label for="purchaseReportStartDate"><?php echo t('start_date'); ?></label>
                        <input type="text" class="form-control datepicker" id="purchaseReportStartDate" value="<?php echo date('Y-m-d'); ?>" name="purchaseReportStartDate" readonly>
                      </div>
                      <div class="form-group col-md-3">
                        <label for="purchaseReportEndDate"><?php echo t('end_date'); ?></label>
                        <input type="text" class="form-control datepicker" id="purchaseReportEndDate" value="<?php echo date('Y-m-d'); ?>" name="purchaseReportEndDate" readonly>
                      </div>
                    </div>
                    <button type="button" id="showPurchaseReport" class="btn btn-dark"><?php echo t('show_report'); ?></button>
                    <button type="reset" id="purchaseFilterClear" class="btn"><?php echo t('clear'); ?></button>
                  </form>
                  <br><br>
                  <div class="table-responsive" id="purchaseReportsTableDiv"></div>
                </div>
                <div id="vendorReportsTab" class="container-fluid tab-pane fade">
                  <br>
                  <p><?php echo t('get_vendor_reports'); ?></p>
                  <div class="table-responsive" id="vendorReportsTableDiv"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>

<?php 
// Include footer if exists
if (file_exists('inc/footer.php')) {
    require_once('inc/footer.php');
} else {
    echo '</body></html>';
}
?>

<script>
$(document).ready(function() {
    // File upload handling
    $('#browseFileBtn').click(function() {
        $('#bulkUploadFile').click();
    });
    
    $('#bulkUploadFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#selectedFileName').html('<i class="fas fa-file-csv text-success"></i> ' + fileName);
            $('#uploadCSV').prop('disabled', false);
            previewCSVFile(this);
        }
    });
    
    // Drag and drop functionality
    const dropArea = $('#dropArea')[0];
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        $(dropArea).addClass('dragover');
    }
    
    function unhighlight() {
        $(dropArea).removeClass('dragover');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            $('#bulkUploadFile')[0].files = files;
            $('#bulkUploadFile').trigger('change');
        }
    }
    
    // Download template
    $('#downloadTemplate').click(function(e) {
        e.preventDefault();
        downloadCSVTemplate();
    });
    
    // Upload CSV file
    $('#uploadCSV').click(function() {
        uploadCSVFile();
    });
    
    // Close upload results
    $(document).on('click', '.alert-dismissible .close', function() {
        $(this).closest('.alert').remove();
    });
});

function previewCSVFile(input) {
    const file = input.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const contents = e.target.result;
        const lines = contents.split('\n').filter(line => line.trim() !== '');
        
        if (lines.length === 0) {
            $('#filePreview').addClass('d-none');
            return;
        }
        
        const previewRows = lines.slice(0, 6); // Show first 5 data rows + header
        
        let tableHTML = '';
        const headers = previewRows[0].split(',').map(h => h.trim());
        
        // Build header row
        let headerRow = '<thead class="thead-light sticky-top"><tr>';
        headers.forEach(header => {
            headerRow += `<th>${header}</th>`;
        });
        headerRow += '</tr></thead><tbody>';
        tableHTML += headerRow;
        
        // Build data rows (max 5)
        for (let i = 1; i < Math.min(previewRows.length, 6); i++) {
            if (previewRows[i].trim() === '') continue;
            tableHTML += '<tr>';
            const cells = previewRows[i].split(',').map(c => c.trim());
            cells.forEach(cell => {
                tableHTML += `<td>${cell}</td>`;
            });
            tableHTML += '</tr>';
        }
        tableHTML += '</tbody>';
        
        $('#previewTable').html(tableHTML);
        const totalRows = lines.length - 1;
        const previewCount = Math.min(totalRows, 5);
        $('#previewRowCount').text(`${previewCount} of ${totalRows} rows`);
        $('#filePreview').removeClass('d-none');
    };
    
    reader.readAsText(file);
}

function downloadCSVTemplate() {
    const csvContent = "itemNumber,itemName,discount,quantity,unitPrice,status,description\n" +
                      "ITEM001,First Item,5,100,25.99,Active,Description of first item\n" +
                      "ITEM002,Second Item,10,50,49.99,Active,Description of second item\n" +
                      "ITEM003,Third Item,0,200,15.50,Disabled,Description of third item\n" +
                      "ITEM004,Fourth Item,15,75,99.99,Active,Description of fourth item\n" +
                      "ITEM005,Fifth Item,2.5,150,12.75,Active,Description of fifth item";
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'item_bulk_upload_template.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    showToast('Template downloaded successfully!', 'success');
}

function uploadCSVFile() {
    const fileInput = $('#bulkUploadFile')[0];
    const updateExisting = $('#updateExisting').is(':checked');
    
    if (!fileInput.files.length) {
        showToast('Please select a CSV file first.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('bulkUploadFile', fileInput.files[0]);
    formData.append('updateExisting', updateExisting ? '1' : '0');
    
    // Show simple loading message instead of problematic modal
    $('#uploadResultsContainer').html(`
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-spinner fa-spin"></i> Processing Upload</h5>
            <p>Please wait while your CSV file is being processed...</p>
        </div>
    `);
    
    // Hide bulk upload modal
    $('#bulkUploadModal').modal('hide');
    
    // Disable upload button during processing
    $('#uploadCSV').prop('disabled', true);
    
    $.ajax({
        url: 'model/item/bulkUploadItems.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // Display results immediately
            $('#uploadResultsContainer').html(response);
            
            // Refresh item reports table
            $('#reportsTablesRefresh').trigger('click');
            
            // Reset form
            $('#bulkUploadFile').val('');
            $('#selectedFileName').html('');
            $('#uploadCSV').prop('disabled', true);
            $('#filePreview').addClass('d-none');
            
            // Show the bulk upload modal again if there were errors
            if (response.includes('alert-warning') || response.includes('alert-danger')) {
                setTimeout(function() {
                    $('#bulkUploadModal').modal('show');
                }, 300);
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Upload Error</h5>' +
                '<p>An error occurred during the upload process:</p>' +
                '<p><strong>' + error + '</strong></p>' +
                '<p class="mb-0">Please check your network connection and try again.</p>' +
                '</div>';
            
            $('#uploadResultsContainer').html(errorMessage);
            
            // Re-show the modal
            setTimeout(function() {
                $('#bulkUploadModal').modal('show');
            }, 300);
        }
    });
}

function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }
    
    const toastId = 'toast-' + Date.now();
    const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    
    const toastHTML = `
        <div id="${toastId}" class="toast ${bgColor} text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
            <div class="toast-header ${bgColor} text-white">
                <i class="fas fa-${icon} mr-2"></i>
                <strong class="mr-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    $('#toastContainer').append(toastHTML);
    $(`#${toastId}`).toast('show');
    
    // Remove toast after it's hidden
    $(`#${toastId}`).on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
</script>