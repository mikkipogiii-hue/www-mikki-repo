<?php 
    session_start();

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
            height: 100%;
            width: 100%;
        }

        .brand-aside {
            background: linear-gradient(135deg, var(--primary-bg) 0%, var(--slate-blue) 100%);
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
            font-weight: 800;
            font-size: 4rem;
            margin: 40px 0 20px 0;
            line-height: 1.1;
        }

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

        .form-header h4 { font-weight: 800; color: var(--primary-bg); margin-top: 0; }
        
        .input-field input:focus + label,
        .input-field .prefix.active { color: var(--accent-color) !important; }
        .input-field input:focus { border-bottom: 1px solid var(--accent-color) !important; box-shadow: 0 1px 0 0 var(--accent-color) !important; }

        .btn-login {
            width: 100%;
            height: 55px;
            border-radius: 12px;
            background: var(--primary-bg) !important;
            font-weight: 700;
            margin-top: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-register-outline {
            width: 100%;
            height: 55px;
            border-radius: 12px;
            background: transparent !important;
            border: 2px solid var(--primary-bg);
            color: var(--primary-bg);
            font-weight: 700;
            margin-top: 15px;
            box-shadow: none;
            transition: 0.3s;
        }

        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #cbd5e1;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .divider-container::before, .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .divider-container:not(:empty)::before { margin-right: 1.5em; }
        .divider-container:not(:empty)::after { margin-left: 1.5em; }

        .premium-footer {
            padding: 30px 10%;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.9rem;
        }

        @media only screen and (max-width : 992px) {
            html, body { overflow: auto; }
            .premium-container { flex-direction: column; }
            .brand-aside, .form-section { flex: 1 1 100%; padding: 40px; }
            .centered-form { padding: 40px 0; }
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
            <h2>Authorized <br> Authentication</h2>
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