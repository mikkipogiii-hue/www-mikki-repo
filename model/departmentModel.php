<?php
class DepartmentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDepartmentCensus() {
        $query = "SELECT d.department_name, COUNT(u.user_id) as total_users 
                  FROM tbl_departments d 
                  LEFT JOIN tbl_users u ON d.department_id = u.department_id 
                  GROUP BY d.department_id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readDepartment() {
        $query = "SELECT * FROM tbl_departments";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>