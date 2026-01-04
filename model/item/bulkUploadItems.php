<?php
/**
 * Bulk Upload Items Handler
 * File: model/item/bulkUploadItems.php
 * Description: Handles CSV file upload for bulk item import
 */

require_once('../../inc/config/constants.php');
require_once('../../inc/config/db.php');

if(isset($_FILES['bulkUploadFile'])){
    
    $file = $_FILES['bulkUploadFile'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];
    $fileSize = $file['size'];
    
    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Only CSV files are allowed
    $allowedExtensions = array('csv');
    
    if(!in_array($fileExt, $allowedExtensions)){
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Invalid File Type</h5>
                <p>The file you uploaded is not a CSV file. Please upload a file with <strong>.csv</strong> extension.</p>
                <hr>
                <p class="mb-0"><small><i class="fas fa-info-circle"></i> If you have an Excel file (.xlsx or .xls), please open it in Excel and save it as CSV format.</small></p>
              </div>';
        exit();
    }
    
    // Check file size (5MB max)
    if($fileSize > 5242880){ // 5MB in bytes
        $fileSizeMB = round($fileSize / 1048576, 2);
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> File Too Large</h5>
                <p>Your file size is <strong>' . $fileSizeMB . ' MB</strong>, which exceeds the maximum allowed size of <strong>5 MB</strong>.</p>
                <hr>
                <p class="mb-0"><small><i class="fas fa-lightbulb"></i> Tip: Try splitting your data into multiple smaller files.</small></p>
              </div>';
        exit();
    }
    
    if($fileError !== 0){
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Upload Error</h5>
                <p>There was an error uploading your file. Error code: ' . $fileError . '</p>
                <p class="mb-0">Please try again. If the problem persists, contact your system administrator.</p>
              </div>';
        exit();
    }
    
    // Process CSV file
    $handle = fopen($fileTmpName, 'r');
    
    if($handle === false){
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Cannot Read File</h5>
                <p>The system could not open or read your CSV file.</p>
                <p class="mb-0">Please ensure the file is not corrupted and try uploading again.</p>
              </div>';
        exit();
    }
    
    // Skip header row
    $header = fgetcsv($handle);
    
    $successCount = 0;
    $errorCount = 0;
    $updatedCount = 0;
    $errors = array();
    $rowNumber = 1; // Start from 1 (header is row 0)
    
    // Begin transaction for better performance and data integrity
    try {
        $conn->beginTransaction();
        
        while(($data = fgetcsv($handle)) !== false){
            $rowNumber++;
            
            // Skip empty rows
            if(count(array_filter($data)) == 0){
                continue;
            }
            
            // Expected format: itemNumber, itemName, discount, quantity, unitPrice, status, description
            if(count($data) < 5){
                $errorCount++;
                $errors[] = "Row $rowNumber: Missing required columns. Expected at least 5 columns (Item Number, Item Name, Discount, Quantity, Unit Price).";
                continue;
            }
            
            // Extract and sanitize data
            $itemNumber = htmlentities(trim($data[0]));
            $itemName = htmlentities(trim($data[1]));
            $discount = isset($data[2]) && is_numeric($data[2]) ? floatval($data[2]) : 0;
            $quantity = isset($data[3]) && is_numeric($data[3]) ? intval($data[3]) : 0;
            $unitPrice = isset($data[4]) && is_numeric($data[4]) ? floatval($data[4]) : 0;
            $status = isset($data[5]) && !empty(trim($data[5])) ? htmlentities(trim($data[5])) : 'Active';
            $description = isset($data[6]) ? htmlentities(trim($data[6])) : '';
            
            // Validate required fields
            if(empty($itemNumber)){
                $errorCount++;
                $errors[] = "Row $rowNumber: Item Number is required but was empty.";
                continue;
            }
            
            if(empty($itemName)){
                $errorCount++;
                $errors[] = "Row $rowNumber: Item Name is required but was empty.";
                continue;
            }
            
            if($quantity < 0){
                $errorCount++;
                $errors[] = "Row $rowNumber ($itemNumber): Quantity cannot be negative. Found: $quantity";
                continue;
            }
            
            if($unitPrice < 0){
                $errorCount++;
                $errors[] = "Row $rowNumber ($itemNumber): Unit Price cannot be negative. Found: $unitPrice";
                continue;
            }
            
            if($discount < 0 || $discount > 100){
                $errorCount++;
                $errors[] = "Row $rowNumber ($itemNumber): Discount must be between 0 and 100. Found: $discount";
                continue;
            }
            
            // Validate status
            if(!in_array($status, array('Active', 'Disabled'))){
                $errorCount++;
                $errors[] = "Row $rowNumber ($itemNumber): Status must be 'Active' or 'Disabled'. Found: '$status'";
                continue;
            }
            
            // Sanitize item number
            $itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);
            
            try {
                // Check if item already exists
                $checkSql = 'SELECT stock, itemName FROM item WHERE itemNumber = :itemNumber';
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute(['itemNumber' => $itemNumber]);
                
                if($checkStmt->rowCount() > 0){
                    // Update existing item - add quantity to existing stock
                    $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    $newStock = $row['stock'] + $quantity;
                    
                    $updateSql = 'UPDATE item SET itemName = :itemName, discount = :discount, stock = :stock, unitPrice = :unitPrice, status = :status, description = :description WHERE itemNumber = :itemNumber';
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->execute([
                        'itemName' => $itemName,
                        'discount' => $discount,
                        'stock' => $newStock,
                        'unitPrice' => $unitPrice,
                        'status' => $status,
                        'description' => $description,
                        'itemNumber' => $itemNumber
                    ]);
                    
                    // Also update item name in sale and purchase tables
                    $updateSaleSql = 'UPDATE sale SET itemName = :itemName WHERE itemNumber = :itemNumber';
                    $updateSaleStmt = $conn->prepare($updateSaleSql);
                    $updateSaleStmt->execute(['itemName' => $itemName, 'itemNumber' => $itemNumber]);
                    
                    $updatePurchaseSql = 'UPDATE purchase SET itemName = :itemName WHERE itemNumber = :itemNumber';
                    $updatePurchaseStmt = $conn->prepare($updatePurchaseSql);
                    $updatePurchaseStmt->execute(['itemName' => $itemName, 'itemNumber' => $itemNumber]);
                    
                    $updatedCount++;
                    $successCount++;
                } else {
                    // Insert new item
                    $insertSql = 'INSERT INTO item(itemNumber, itemName, discount, stock, unitPrice, status, description) VALUES(:itemNumber, :itemName, :discount, :stock, :unitPrice, :status, :description)';
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->execute([
                        'itemNumber' => $itemNumber,
                        'itemName' => $itemName,
                        'discount' => $discount,
                        'stock' => $quantity,
                        'unitPrice' => $unitPrice,
                        'status' => $status,
                        'description' => $description
                    ]);
                    $successCount++;
                }
            } catch(PDOException $e){
                $errorCount++;
                $errors[] = "Row $rowNumber ($itemNumber): Database error - " . $e->getMessage();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
    } catch(Exception $e){
        // Rollback on error
        $conn->rollBack();
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Database Error</h5>
                <p>An error occurred while saving data to the database:</p>
                <p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>
                <p class="mb-0">All changes have been rolled back. Please contact your system administrator if this problem continues.</p>
              </div>';
        fclose($handle);
        exit();
    }
    
    fclose($handle);
    
    // Display results
    $newCount = $successCount - $updatedCount;
    
    // SUCCESS MESSAGE
    if($successCount > 0){
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                      <h4 class="alert-heading"><i class="fas fa-check-circle"></i> Upload Successful!</h4>
                      <p>Your CSV file has been processed successfully. Here\'s what happened:</p>
                      <hr>
                      <div class="row">
                          <div class="col-md-4">
                              <div class="text-center p-3 bg-light rounded">
                                  <h2 class="text-success mb-1">' . $successCount . '</h2>
                                  <small><i class="fas fa-database"></i> Total Items Processed</small>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="text-center p-3 bg-light rounded">
                                  <h2 class="text-primary mb-1">' . $newCount . '</h2>
                                  <small><i class="fas fa-plus-circle"></i> New Items Added</small>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="text-center p-3 bg-light rounded">
                                  <h2 class="text-info mb-1">' . $updatedCount . '</h2>
                                  <small><i class="fas fa-sync-alt"></i> Items Updated</small>
                              </div>
                          </div>
                      </div>
                      <hr>
                      <p class="mb-0"><i class="fas fa-info-circle"></i> <strong>Note:</strong> For updated items, the new quantity was added to existing stock. You can view all items in the <strong>Search</strong> or <strong>Reports</strong> tab.</p>
                    </div>';
        echo $message;
    }
    
    // ERROR MESSAGE
    if($errorCount > 0){
        $errorMessage = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                          <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Warning: Some Rows Had Errors</h5>
                          <p><strong>' . $errorCount . ' row(s)</strong> could not be processed due to validation errors. Please review the errors below and fix them in your CSV file:</p>
                          <hr>
                          <div style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                              <ul class="mb-0" style="font-family: monospace; font-size: 0.9rem;">';
        
        // Show first 20 errors
        $displayErrors = array_slice($errors, 0, 20);
        foreach($displayErrors as $error){
            $errorMessage .= '<li class="mb-2"><i class="fas fa-times-circle text-danger"></i> ' . $error . '</li>';
        }
        
        if(count($errors) > 20){
            $remaining = count($errors) - 20;
            $errorMessage .= '<li class="mb-2"><em><i class="fas fa-ellipsis-h"></i> ... and <strong>' . $remaining . '</strong> more error(s)</em></li>';
        }
        
        $errorMessage .= '    </ul>
                          </div>
                          <hr>
                          <p class="mb-0"><i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Fix the errors in your CSV file and upload again. Successfully processed items will be updated with new data.</p>
                        </div>';
        echo $errorMessage;
    }
    
    // NO DATA MESSAGE
    if($successCount == 0 && $errorCount == 0){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="alert-heading"><i class="fas fa-info-circle"></i> No Data Found</h5>
                <p>The CSV file you uploaded appears to be empty or contains no valid data rows.</p>
                <p><strong>Please check that:</strong></p>
                <ul>
                    <li>Your CSV file contains data rows (not just headers)</li>
                    <li>Data rows are not blank</li>
                    <li>The file format matches the template</li>
                </ul>
                <p class="mb-0"><i class="fas fa-download"></i> Download the template file above to see the correct format.</p>
              </div>';
    }
    
    exit();
    
} else {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="alert-heading"><i class="fas fa-exclamation-circle"></i> No File Received</h5>
            <p>No file was uploaded. Please select a CSV file and try again.</p>
          </div>';
    exit();
}
?>