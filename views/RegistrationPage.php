<?php 
    session_start();
    require_once "../bl/UserManager.php"; 
    require_once "../bl/DepartmentManager.php";
    
    $departmentManager = new DepartmentManager();
    $departments = $departmentManager->getDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Onboarding | HMS Premium Portal</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #0f172a;
            --slate-blue: #1e293b;
            --accent-color: #00bcd4;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            overflow: hidden;
        }

        .premium-container {
            display: flex;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .brand-aside {
            background: linear-gradient(135deg, var(--primary-bg) 0%, var(--slate-blue) 100%);
            color: white;
            flex: 1 1 40%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 80px;
            box-sizing: border-box;
            z-index: 1; 
        }

        .brand-aside h2 {
            font-weight: 800;
            font-size: 4rem;
            margin: 40px 0 20px 0;
            line-height: 1.1;
        }

        .form-section {
            background: white;
            flex: 1 1 60%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
            z-index: 10; 
            position: relative;
            box-sizing: border-box;
        }

        .scrollable-content {
            padding: 80px 10%;
            overflow-y: auto;
            flex: 1;
        }

        .form-header h4 { font-weight: 800; color: var(--primary-bg); }
        
        .section-separator { 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            color: #94a3b8; 
            letter-spacing: 2px; 
            margin: 40px 0 20px 0;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
            font-weight: 700;
        }

        .btn-register {
            width: 100%;
            height: 60px;
            background: var(--primary-bg) !important;
            font-weight: 700;
            margin-top: 40px;
            border-radius: 12px;
            text-transform: uppercase;
        }

        .premium-footer {
            padding: 30px 10%;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }

        @media only screen and (max-width : 992px) {
            html, body { overflow: auto; }
            .premium-container { flex-direction: column; height: auto; }
            .brand-aside, .form-section { flex: 1 1 100%; padding: 40px; height: auto; }
            .scrollable-content { padding: 40px 0; overflow-y: visible; }
        }
    </style>
</head>
<body>

<main class="premium-container">
    
    <div class="brand-aside">
        <div class="logo-mark">
            <i class="material-icons" style="margin-right: 10px; color: var(--accent-color);">local_hospital</i>
            HMS Portal
        </div>

        <div>
            <h2>Secure Staff <br> Onboarding</h2>
            <p>Welcome to the premium Hospital Management System portal.</p>
            
            <div class="check-list" style="margin-top: 40px;">
                <p><i class="material-icons" style="color: var(--accent-color);">verified</i> HIPAA-Compliant Security</p>
                <p><i class="material-icons" style="color: var(--accent-color);">verified</i> Inventory Management</p>
                <p><i class="material-icons" style="color: var(--accent-color);">verified</i> Direct Departmental Sync</p>
            </div>
        </div>

        <div style="font-size: 0.9rem; opacity: 0.6;">
            Professional Healthcare Suite v2.1
        </div>
    </div>

    <div class="form-section">
        
        <div class="scrollable-content">
            <div class="form-header">
                <h4>Registration</h4>
                <p class="grey-text text-darken-1">Please enter your credentials below.</p>
            </div>

            <div class="row" style="margin-top: 40px;">
                
                <p class="section-separator">Personal Profile</p>
                
                <div class="input-field col s6">
                    <i class="material-icons prefix">person_outline</i>
                    <input id="fName" name="fName" type="text" class="validate">
                    <label for="fName">First Name</label>
                </div>
                <div class="input-field col s6">
                    <input id="lName" name="lName" type="text" class="validate">
                    <label for="lName">Last Name</label>
                </div>
                
                <div class="input-field col s6">
                    <i class="material-icons prefix">event</i>
                    <input id="bDate" name="bDate" type="text" class="datepicker">
                    <label for="bDate">Birth Date</label>
                </div>
                <div class="input-field col s6">
                    <i class="material-icons prefix">phone_android</i>
                    <input id="phone" name="phone" type="tel">
                    <label for="phone">Phone Number</label>
                </div>
                
                <div class="input-field col s12">
                    <i class="material-icons prefix">alternate_email</i>
                    <input id="email" name="email" type="email">
                    <label for="email">Medical Email Address</label>
                </div>

                <p class="section-separator">Security Assignment</p>
                
                <div class="input-field col s6">
                    <select id="deptID" name="deptID">
                        <option value="" disabled selected>Select Your Assigned Unit</option>
                        <?php foreach($departments as $dept): ?>
                            <option value="<?= $dept['department_id'] ?>">
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Department</label>
                </div>

                <div class="input-field col s6">
                    <select id="role" name="role">
                        <option value="Staff" selected>Staff</option>
                        <option value="Admin">Admin</option>
                    </select>
                    <label>Account Type</label>
                </div>

                <div class="input-field col s6">
                    <input id="uName" name="uName" type="text">
                    <label for="uName">Username</label>
                </div>
                <div class="input-field col s6">
                    <input id="pWord" name="pWord" type="password">
                    <label for="pWord">Password</label>
                </div>

                <div class="col s12">
                    <button class="btn waves-effect waves-light btn-register" onclick="addFunc()">
                        Initialize Onboarding
                    </button>
                </div>

                <div class="col s12 center-align" style="margin-top: 25px;">
                    <a href="loginpage.php" class="indigo-text" style="font-weight: 700;">Already registered? <strong>Log In</strong></a>
                </div>
            </div>
        </div>

        <footer class="premium-footer center-align">
            &copy; <?= date("Y") ?> Hospital Management System | Professional Administration Suite
        </footer>
    </div>
</main>

<script src="../scripts/Service.js"></script>

<script>
    $(document).ready(function(){
        $('select').formSelect();
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoClose: true,
            maxDate: new Date(),
            yearRange: [1920, 2026],
            container: 'body' 
        });
    });
</script>

</body>
</html>