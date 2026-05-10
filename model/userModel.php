<?php

class UserModel {

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createUser($fName, $lName, $bDate, $phone, $email, $uName, $pWord, $deptID): bool {
        $insertQuery = "INSERT INTO tbl_users (
                            first_name, last_name, birth_date, phone_number, 
                            email_address, username, password, department_id, 
                            createdAt, updatedAt
                        ) 
                        VALUES (
                            :fName, :lName, :bDate, :phone, 
                            :email, :username, :password, :deptID, 
                            :createdAt, :updatedAt
                        )";
        
        $hashedPassword = password_hash($pWord, PASSWORD_ARGON2ID);
        $dateNow = date("Y-m-d H:i:s");

        $stmt = $this->conn->prepare($insertQuery);
        
        $stmt->bindParam(':fName', $fName);
        $stmt->bindParam(':lName', $lName);
        $stmt->bindParam(':bDate', $bDate);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $uName);
        $stmt->bindParam(':password', $hashedPassword); 
        $stmt->bindParam(':deptID', $deptID);
        $stmt->bindParam(':createdAt', $dateNow);
        $stmt->bindParam(':updatedAt', $dateNow);

        return $stmt->execute();
    }

    public function loginFunc($uName, $pWord) {
        $query = "SELECT * FROM tbl_users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $uName);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pWord, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function updateUser($uID, $uName, $pWord): bool {
        $updateQuery = "UPDATE tbl_users 
                        SET username = :username, password = :password, updatedAt = :updatedAt 
                        WHERE user_id = :userID";
        
        $hashedPassword = password_hash($pWord, PASSWORD_ARGON2ID);
        $dateNow = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare($updateQuery);
        
        $stmt->bindParam(":username", $uName);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":updatedAt", $dateNow);
        $stmt->bindParam(":userID", $uID);

        return $stmt->execute();
    }

    public function readUser(): PDOStatement {
        $selectQuery = "SELECT * FROM tbl_users";
        $stmt = $this->conn->prepare($selectQuery);
        $stmt->execute();
        return $stmt;
    }

    public function readAdvancedUser(): PDOStatement {
        $selectQuery = "SELECT u.*, d.department_name 
                        FROM tbl_users u 
                        INNER JOIN tbl_departments d ON u.department_id = d.department_id";
        $stmt = $this->conn->prepare($selectQuery);
        $stmt->execute();
        return $stmt;
    }

    public function deleteUser($uID): mixed {   
        $deleteQuery = "DELETE FROM tbl_users WHERE user_id = :userID";
        $stmt = $this->conn->prepare($deleteQuery);
        $stmt->bindParam(":userID", $uID);
        
        return $stmt->execute();
    }
}
?>