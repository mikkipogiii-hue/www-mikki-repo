<?php 
session_start();
require_once "../bl/UserManager.php";
require_once "../bl/DepartmentManager.php";
require_once "../helper/send.php";

$userManager = new UserManager();
$deptManager = new DepartmentManager();

if (isset($_POST["uName"], $_POST["pWord"], $_POST["fName"], $_POST["lName"]) && !isset($_POST["uID"])) {
    
    $res = $userManager->addUserFunc(
        fName:  $_POST["fName"],
        lName:  $_POST["lName"],
        bDate:  $_POST["bDate"],
        phone:  $_POST["phone"],
        email:  $_POST["email"],
        uName:  $_POST["uName"], 
        pWord:  $_POST["pWord"], 
        deptID: $_POST["deptID"]
    );

    if ($res === true) {
        $fullName = htmlspecialchars($_POST["fName"] . " " . $_POST["lName"]);
        $dept = htmlspecialchars($_POST["deptID"]);
        $user = htmlspecialchars($_POST["uName"]);
        $emailAddr = htmlspecialchars($_POST["email"]);

        $body = "
        <div style='font-family: Segoe UI, Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f9; padding: 40px; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);'>
                <div style='background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%); padding: 30px; text-align: center;'>
                    <h2 style='color: #ffffff; margin: 0; letter-spacing: 1px; font-weight: 300;'>Staff Registration Alert</h2>
                    <p style='color: #00bcd4; margin: 5px 0 0 0; font-weight: bold; text-transform: uppercase; font-size: 12px;'>Hospital Management System</p>
                </div>
                <div style='padding: 40px;'>
                    <p style='font-size: 16px; line-height: 1.6; color: #455a64;'>A new professional account has been successfully created. Review the credentials below:</p>
                    
                    <table style='width: 100%; margin-top: 25px; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; color: #78909c; font-size: 14px;'>FULL NAME</td>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; text-align: right; color: #1a237e; font-weight: bold;'>$fullName</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; color: #78909c; font-size: 14px;'>EMAIL</td>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; text-align: right; color: #37474f;'>$emailAddr</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; color: #78909c; font-size: 14px;'>DEPARTMENT</td>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; text-align: right; color: #37474f;'>Unit #$dept</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; color: #78909c; font-size: 14px;'>USERNAME</td>
                            <td style='padding: 12px 0; border-bottom: 1px solid #eee; text-align: right; color: #37474f; font-family: monospace;'>$user</td>
                        </tr>
                    </table>

                    <div style='margin-top: 40px; text-align: center;'>
                        <div style='display: inline-block; padding: 12px 25px; background-color: #1a237e; color: #ffffff; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;'>System Verified</div>
                    </div>
                </div>
                <div style='background-color: #fafafa; padding: 20px; text-align: center; font-size: 11px; color: #b0bec5; border-top: 1px solid #eee;'>
                    This is an automated notification from the HMS Administration Suite.<br>
                    &copy; " . date("Y") . " HMS Premium | Secure Access Port
                </div>
            </div>
        </div>";

        sendEmail(
            "jaspermdistroyer@gmail.com",
            "Admin",
            "New Staff Registration: $fullName",
            $body
        );

        echo "success";
    } else {
        echo "Database Error: User could not be saved.";
    }
    exit;
} 

else if (isset($_POST["uName"], $_POST["pWord"], $_POST["uID"])) {
    $userManager->updateUserFunc(
        uName:  $_POST["uName"], 
        pWord:  $_POST["pWord"], 
        userID: $_POST["uID"]
    );
    exit;
} 

else if (isset($_POST["dID"])) {
    $userManager->deleteUserFunc(userID: $_POST["dID"]);
    exit;
}

else if (isset($_POST["loginUName"], $_POST["loginPWord"])) {
    $userManager->loginUserFunc(
        uName: $_POST["loginUName"], 
        pWord: $_POST["loginPWord"]
    );
    exit;
}

else if (isset($_POST["action"]) && $_POST["action"] == "logout") {
    session_destroy();
    echo "success";
    exit;
}
?>