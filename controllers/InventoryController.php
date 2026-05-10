<?php
/**
 * INVENTORY CONTROLLER
 * This file acts as the middleman between your Dashboard (Service.js)
 * and the Database (InventoryModel.php).
 */
session_start();
require_once "../model/database.php";
require_once "../model/InventoryModel.php";

// 1. Initialize the Database Connection
$database = new Database();
$db = $database->connectDB();

// 2. Initialize the Inventory Model
$inventoryModel = new InventoryModel($db);

// 3. Check if an "action" was sent via AJAX POST
if (isset($_POST["action"])) {
    $action = $_POST["action"];

    /**
     * ACTION: ADJUST (Quick Stock Update)
     * Triggered by saveStockUpdate() in Service.js
     */
    if ($action == "adjust") {
        $id = $_POST["id"];
        $newQty = $_POST["newQty"];

        // Call the updateStock method in the Model
        // We use if() to check if the SQL query actually worked
        if ($inventoryModel->updateStock($id, $newQty)) {
            // CRITICAL: We must echo "success" exactly for Service.js to refresh the page
            echo "success";
        } else {
            echo "Database Error: Unable to update the quantity.";
        }
        exit; // Stop execution after sending the response
    }

    /**
     * ACTION: ADD (Register New Item)
     * Triggered by saveNewInventory() in Service.js
     */
    else if ($action == "add") {
        $name   = $_POST["name"];
        $size   = $_POST["size"];
        $stock  = $_POST["stock"];
        $expiry = $_POST["expiry"];

        if ($inventoryModel->createItem($name, $size, $stock, $expiry)) {
            echo "success";
        } else {
            echo "Error: Could not add item to database.";
        }
        exit;
    }

    /**
     * ACTION: DELETE (Remove Item)
     * Triggered by deleteItem() in Service.js
     */
    else if ($action == "delete") {
        $id = $_POST["id"];
        if ($inventoryModel->deleteItem($id)) {
            echo "success";
        } else {
            echo "Error: Could not delete item.";
        }
        exit;
    }
}
?>