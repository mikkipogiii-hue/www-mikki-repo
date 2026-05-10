<?php
require_once "../model/database.php";
require_once "../model/userModel.php";

class UserManager {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->connectDB();
        $this->userModel = new UserModel($db);
    }

    public function addUserFunc($fName, $lName, $bDate, $phone, $email, $uName, $pWord, $deptID) {
        try {
            return $this->userModel->createUser($fName, $lName, $bDate, $phone, $email, $uName, $pWord, $deptID);
        } catch (Exception $ex) {
            http_response_code(500);
            echo $ex->getMessage();
            return false;
        }
    }

    public function loginUserFunc($uName, $pWord): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $user = $this->userModel->loginFunc($uName, $pWord);
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['dept_id'] = $user['department_id'];
            $_SESSION['role'] = $user['role'] ?? 'Staff'; 
            echo "true";
        } else {
            echo "false";
        }
    }

    public function updateUserFunc($uName, $pWord, $userID): void {
        try {
            if ($this->userModel->updateUser($userID, $uName, $pWord)) {
                echo "success"; 
            } else {
                echo "Error updating user.";
            }
        } catch (PDOException $ex) {
            http_response_code(500);
            echo $ex->getMessage();
        }
    }

    public function deleteUserFunc($userID): void {
        try {
            if ($this->userModel->deleteUser($userID)) {
                echo "success";
            } else {
                echo "Error deleting user.";
            }
        } catch (PDOException $ex) {
            http_response_code(500);
            echo $ex->getMessage();
        }
    }

    public function getAdvancedUser(): mixed {
        $response = $this->userModel->readAdvancedUser();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }
}