<?php

class InventoryModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createItem($name, $size, $stock, $expiry): bool {
        try {
            $this->conn->beginTransaction();

            // 1. Insert into tbl_items
            $query = "INSERT INTO tbl_items (item_name, size, current_stock, expiration_date) 
                      VALUES (:name, :size, :stock, :expiry)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':name'   => $name,
                ':size'   => $size,
                ':stock'  => $stock,
                ':expiry' => $expiry
            ]);

            $newItemID = $this->conn->lastInsertId();
            $adminName = $_SESSION['username'] ?? 'System';

            // 2. Insert into tbl_logs for Audit Trail
            $logQuery = "INSERT INTO tbl_logs (log_message, createdAt) 
                         VALUES (:msg, :cAt)";
            $logStmt = $this->conn->prepare($logQuery);
            $message = "Staff '$adminName' added new item: $name ($size) with $stock units.";
            $logStmt->execute([
                ':msg' => $message,
                ':cAt' => date("Y-m-d H:i:s")
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            die($e->getMessage());
        }
    }

    public function readItems(): PDOStatement {
        $query = "SELECT * FROM tbl_items ORDER BY item_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * UPDATING STOCK: Matches the 'adjust' action in the Controller
     * Line-by-line:
     * 1. Prepare the SQL to change current_stock for a specific item_id
     * 2. Bind the new number and the ID to prevent SQL injection
     * 3. Execute and return true/false
     */
    public function updateStock($id, $newQty): bool {
        $query = "UPDATE tbl_items SET current_stock = :qty WHERE item_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':qty', $newQty);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteItem($id): bool {
        $query = "DELETE FROM tbl_items WHERE item_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function getExpiringSoonCount() {
    $query = "SELECT COUNT(*) AS expiring_count 
              FROM tbl_items 
              WHERE expiration_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['expiring_count'] ?? 0;
}
}