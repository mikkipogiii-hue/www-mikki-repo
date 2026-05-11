<?php
require_once "../model/departmentModel.php";
require_once "../model/database.php";

class DepartmentManager {
    private $deptModel;

    public function __construct() {
        $database = new Database();
        $db = $database->connectDB();
        $this->deptModel = new DepartmentModel($db);
    }

    public function getCardDepartments() {
        return $this->deptModel->getDepartmentCensus();
    }

    public function getDepartments() {
        return $this->deptModel->readDepartment()->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>