<?php 
    /**
     * 1. PHP SERVER-SIDE LOGIC
     * Standard session management for the login portal.
     */
    session_start();

    // If user is already logged in, redirect them directly to the dashboard
    if (isset($_SESSION['user_id'])) {
        header("Location: DashboardPage.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | HMS Premium Portal</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1a237e; /* Indigo darken-4 */
            --accent-color: #00bcd4;  /* Cyan */
        }

        /* FULL PAGE UTILIZATION */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            overflow: hidden;
        }

        .premium-container {
            display: flex;
            height: 100%;
            width: 100%;
        }

        /* BRAND PANEL (LEFT) - Matches Registration */
        .brand-aside {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3949ab 100%);
            color: white;
            flex: 1 1 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 80px;
            box-sizing: border-box;
            z-index: 10;
        }

        .brand-aside .logo-mark {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 300;
            letter-spacing: 1px;
        }

        .brand-aside h2 {
            font-weight: 300;
            font-size: 4rem;
            margin: 40px 0 20px 0;
            line-height: 1.1;
        }

        /* LOGIN PANEL (RIGHT) */
        .form-section {
            background: white;
            flex: 1 1 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            box-sizing: border-box;
        }

        .centered-form {
            padding: 0 15%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }

        .form-header h4 { font-weight: 800; color: var(--primary-color); margin-top: 0; }
        
        /* Premium Input Styling */
        .input-field input:focus + label,
        .input-field .prefix.active { color: var(--primary-color) !important; }
        .input-field input:focus { border-bottom: 1px solid var(--primary-color) !important; box-shadow: 0 1px 0 0 var(--primary-color) !important; }

        .btn-login {
            width: 100%;
            height: 55px;
            border-radius: 6px;
            background: var(--primary-color) !important;
            font-weight: 700;
            margin-top: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-register-outline {
            width: 100%;
            height: 55px;
            border-radius: 6px;
            background: transparent !important;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 700;
            margin-top: 15px;
            box-shadow: none;
            transition: 0.3s;
        }

        .btn-register-outline:hover {
            background: #f5f5f5 !important;
            box-shadow: none;
        }

        .premium-footer {
            padding: 30px 10%;
            background: #fafafa;
            border-top: 1px solid #eee;
            color: #b0bec5;
            font-size: 0.9rem;
        }

        /* Divider Text Styling */
        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #cfd8dc;
        }
        .divider-container::before, .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }
        .divider-container:not(:empty)::before { margin-right: 1em; }
        .divider-container:not(:empty)::after { margin-left: 1em; }

        /* Responsive Breakpoints */
        @media only screen and (max-width : 992px) {
            html, body { overflow: auto; }
            .premium-container { flex-direction: column; }
            .brand-aside, .form-section { flex: 1 1 100%; padding: 40px; }
            .centered-form { padding: 40px 0; }
            .brand-aside h2 { font-size: 2.8rem; }
        }
    </style>
</head>
<body>

<main class="premium-container">
    
    <div class="brand-aside">
        <div class="logo-mark">
            <i class="material-icons" style="margin-right: 10px;">local_hospital</i>
            HMS Portal
        </div>

        <div>
            <h2>Professional <br> Authentication</h2>
            <p>Welcome back. Please verify your credentials to access the Hospital Management System dashboard and secure medical records.</p>
            
            <div style="margin-top: 50px; font-size: 1.1rem;">
                <p><i class="material-icons tiny" style="color: var(--accent-color);">security</i> Encrypted Session Access</p>
                <p><i class="material-icons tiny" style="color: var(--accent-color);">history</i> Automated Activity Logging</p>
                <p><i class="material-icons tiny" style="color: var(--accent-color);">admin_panel_settings</i> Role-Based Permissions</p>
            </div>
        </div>

        <div style="font-size: 0.9rem; opacity: 0.6;">
            Authorized Personnel Only
        </div>
    </div>

    <div class="form-section">
        
        <div class="centered-form">
            <div class="form-header">
                <h4>System Login</h4>
                <p class="grey-text text-darken-1">Enter your assigned username and password to proceed.</p>
            </div>

            <div class="row" style="margin-top: 30px;">
                <div class="input-field col s12">
                    <i class="material-icons prefix">account_circle</i>
                    <input id="login_uName" type="text" class="validate">
                    <label for="login_uName">Account Username</label>
                </div>

                <div class="input-field col s12">
                    <i class="material-icons prefix">lock_outline</i>
                    <input id="login_pWord" type="password" class="validate">
                    <label for="login_pWord">Secure Password</label>
                </div>

                <div class="col s12">
                    <button class="btn waves-effect waves-light btn-login" onclick="loginFunc()">
                        Access Dashboard
                    </button>
                </div>

                <div class="col s12">
                    <div class="divider-container">NEW PERSONNEL?</div>
                </div>

                <div class="col s12">
                    <button class="btn waves-effect waves-light btn-register-outline" onclick="redirectFunc(3)">
                        Register Professional Account
                    </button>
                </div>
            </div>
        </div>

        <footer class="premium-footer center-align">
            &copy; <?= date("Y") ?> Hospital Management System | Professional Administration Suite
            <br>
            <small>Session timeout is active for your protection.</small>
        </footer>
    </div>
</main>

<script src="../scripts/Service.js"></script>

</body>
</html>