<?php

class DepartmentModel {
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readDepartment(): mixed {
        $selectQuery = "SELECT * FROM tbl_departments";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();

        return $response;
    }

    public function cardDepartments() {
        $selectQuery = "SELECT d.department_name, COUNT(u.user_id) AS total_users 
                        FROM tbl_departments d 
                        LEFT JOIN tbl_users u ON d.department_id = u.department_id 
                        GROUP BY d.department_id";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    
}