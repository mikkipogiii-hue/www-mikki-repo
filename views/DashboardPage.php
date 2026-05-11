<?php 
    session_start();
    require_once "../model/database.php";
    require_once "../model/InventoryModel.php";
    require_once "../bl/DepartmentManager.php";
    require_once "../model/departmentModel.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: LoginPage.php");
        exit;
    }

    $username = $_SESSION['username'] ?? "User";
    $userRole = $_SESSION['role'] ?? 'Staff';

    $database = new Database();
    $db = $database->connectDB();
    
    $invModel = new InventoryModel($db);
    $deptManager = new DepartmentManager();

    $items = $invModel->readItems()->fetchAll(PDO::FETCH_ASSOC);
    
    if ($userRole === 'Admin') {
        $censusData = $deptManager->getCardDepartments(); 
        $labels = []; $counts = [];
        foreach($censusData as $row) {
            $labels[] = $row['department_name'];
            $counts[] = (int)$row['total_users'];
        }
    }

    $totalItems = count($items);
    $lowStockCount = 0;
    foreach($items as $i) { 
        if($i['current_stock'] <= 10) $lowStockCount++; 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMS Premium | Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
    
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --main-bg: #f8fafc;
            --accent-cyan: #00bcd4;
            --card-border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { background-color: var(--main-bg); font-family: 'Inter', 'Segoe UI', sans-serif; margin: 0; color: var(--text-main); }
        
        header, main, footer { padding-left: 280px; transition: 0.3s; } 
        @media only screen and (max-width : 992px) { header, main, footer { padding-left: 0; } }

        /* --- PREMIUM SIDEBAR --- */
        .sidenav { 
            background: var(--sidebar-bg); 
            width: 280px !important;
            border: none;
            display: flex;
            flex-direction: column;
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
        }

        .user-view { 
            padding: 60px 32px 40px 32px !important; 
            text-align: center;
            background: linear-gradient(to bottom, rgba(30, 41, 59, 0.4), transparent);
        }

        .user-view .circle { 
            width: 80px; height: 80px; 
            border: 3px solid var(--accent-cyan);
            box-shadow: 0 0 20px rgba(0, 188, 212, 0.3);
            margin: 0 auto 15px auto;
        }

        .user-view .name { font-size: 20px !important; font-weight: 700; color: #fff !important; }
        .user-view .role-tag { 
            font-size: 10px !important; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            color: var(--accent-cyan);
            font-weight: 800;
        }

        .sidenav li a { 
            color: #94a3b8 !important; 
            margin: 8px 16px;
            border-radius: 12px;
            height: 50px;
            line-height: 50px;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }

        .sidenav li a i { color: #64748b !important; font-size: 22px; margin-right: 15px !important; }
        .sidenav li.active a { background: rgba(0, 188, 212, 0.1) !important; color: #fff !important; }
        .sidenav li.active a i { color: var(--accent-cyan) !important; }
        .sidenav li a:hover { background: rgba(255, 255, 255, 0.05) !important; color: #fff !important; transform: translateX(5px); }

        .logout-container { margin-top: auto; padding-bottom: 30px; }

       
        .nav-top { 
            background: #fff !important; 
            height: 80px; 
            line-height: 80px; 
            box-shadow: 0 1px 0 rgba(0,0,0,0.05); 
        }

        .nav-top .brand-logo { color: var(--text-main) !important; font-weight: 800; margin-left: 40px; font-size: 22px; }

        .stats-card { 
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid var(--card-border);
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }

        .stats-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }

        .stats-card .icon-bg {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 80px;
            color: rgba(0,0,0,0.03);
            transform: rotate(-15deg);
        }

        .inventory-card { 
            background: #fff;
            border-radius: 24px; 
            padding: 30px; 
            border: 1px solid var(--card-border);
            margin-top: 30px;
        }

        
        table.highlight tbody tr:hover { background-color: #f1f5f9 !important; }
        thead th { color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        
        .chip { font-weight: 700; border-radius: 8px; font-size: 12px; }
        .btn-floating.btn-large { background: var(--sidebar-bg) !important; }

        .premium-footer { 
            padding: 40px 10%; 
            color: var(--text-muted); 
            font-size: 13px;
            border-top: 1px solid var(--card-border);
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <ul id="slide-out" class="sidenav sidenav-fixed">
        <li>
            <div class="user-view">
                <img class="circle" src="https://ui-avatars.com/api/?name=<?= $username ?>&background=0f172a&color=00bcd4&bold=true">
                <span class="name"><?= htmlspecialchars($username) ?></span>
                <span class="role-tag"><?= strtoupper($userRole) ?> ACCESS</span>
            </div>
        </li>
        <li class="active"><a href="#!"><i class="material-icons">dashboard</i>Dashboard</a></li>
        <li><a href="#!"><i class="material-icons">inventory_2</i>Inventory</a></li>
        <li><a href="#!"><i class="material-icons">history</i>Activity Logs</a></li>
        
        <div class="logout-container">
            <li><a href="#!" onclick="logoutFunc()" style="color: #ef4444 !important;"><i class="material-icons" style="color: inherit;">power_settings_new</i>Sign Out</a></li>
        </div>
    </ul>

    <main>
        <nav class="nav-top">
            <div class="nav-wrapper">
                <span class="brand-logo"><?= $userRole ?> <span style="font-weight: 300; color: var(--text-muted);">Overview</span></span>
            </div>
        </nav>

        <div class="container-fluid" style="padding: 40px;">
            <div class="row">
                <div class="col s12 m4">
                    <div class="stats-card">
                        <i class="material-icons icon-bg">inventory</i>
                        <span class="grey-text" style="font-weight: 700; font-size: 12px; text-transform: uppercase;">Medical Items</span>
                        <h3 style="font-weight: 800; margin: 10px 0;"><?= $totalItems ?></h3>
                        <p style="margin:0; font-size:12px; color:#10b981;">+ Live Tracking</p>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="stats-card">
                        <i class="material-icons icon-bg">warning</i>
                        <span class="grey-text" style="font-weight: 700; font-size: 12px; text-transform: uppercase;">Stock Alerts</span>
                        <h3 style="font-weight: 800; margin: 10px 0; color: #ef4444;"><?= $lowStockCount ?></h3>
                        <p style="margin:0; font-size:12px; color:<?= $lowStockCount > 0 ? '#ef4444' : '#10b981' ?>;">Requires Attention</p>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="stats-card">
                        <i class="material-icons icon-bg">today</i>
                        <span class="grey-text" style="font-weight: 700; font-size: 12px; text-transform: uppercase;">System Time</span>
                        <h4 style="font-weight: 700; margin: 21px 0;"><?= date("M d, Y") ?></h4>
                    </div>
                </div>
            </div>

            <?php if ($userRole === 'Admin'): ?>
            <div class="row">
                <div class="col s12">
                    <div class="inventory-card">
                        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 30px;">Personnel Distribution</h6>
                        <div style="height: 350px; position: relative;">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col s12">
                    <div class="inventory-card">
                        <div class="row" style="display: flex; align-items: center; margin-bottom: 20px;">
                            <div class="col s6">
                                <h6 style="font-weight: 800; color: var(--text-main);">Inventory Control</h6>
                            </div>
                            <div class="col s6 right-align">
                                <a class="btn-floating btn-large waves-effect waves-light modal-trigger" href="#modalAddItem">
                                    <i class="material-icons">add</i>
                                </a>
                            </div>
                        </div>

                        <table class="highlight responsive-table" id="mikkiTable">
                            <thead>
                                <tr>
                                    <th>Item Details</th>
                                    <th>Unit/Size</th>
                                    <th>Stock Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td style="color: var(--text-muted);"><?= htmlspecialchars($item['size']) ?></td>
                                    <td>
                                        <span class="chip <?= $item['current_stock'] <= 10 ? 'red lighten-5 red-text' : 'green lighten-5 green-text' ?>">
                                            <?= $item['current_stock'] ?> Units
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#!" class="indigo-text text-darken-4" onclick="editItem(<?= $item['item_id'] ?>)"><i class="material-icons">edit</i></a>
                                        <a href="#!" class="red-text text-lighten-1" style="margin-left: 15px;" onclick="deleteItem(<?= $item['item_id'] ?>)"><i class="material-icons">delete_outline</i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer class="premium-footer center-align">
            &copy; <?= date("Y") ?> Hospital Management System | Enterprise Suite
            <br>
            <small style="opacity: 0.5;">Secure session active for <?= htmlspecialchars($username) ?>. HIPAA Compliance V.3</small>
        </footer>
    </main>

    <div id="modalAddItem" class="modal" style="border-radius: 24px; max-width: 500px;">
        <div class="modal-content">
            <h5 style="font-weight: 800; color: var(--text-main);">Add New Stock</h5>
            <div class="row">
                <div class="input-field col s12"><input id="inv_name" type="text"><label for="inv_name">Item Name</label></div>
                <div class="input-field col s6"><input id="inv_size" type="text"><label for="inv_size">Size</label></div>
                <div class="input-field col s6"><input id="inv_stock" type="number"><label for="inv_stock">Quantity</label></div>
                <div class="input-field col s12"><input id="inv_expiry" type="date"></div>
            </div>
        </div>
        <div class="modal-footer" style="padding-right: 30px; padding-bottom: 20px;">
            <a href="#!" class="modal-close waves-effect btn-flat">Cancel</a>
            <a href="#!" class="btn indigo darken-4" onclick="saveNewInventory()" style="border-radius: 12px; height: 45px;">Save Entry</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <?php if ($userRole === 'Admin'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    <script src="../scripts/Service.js"></script>

    <script>
        $(document).ready(function(){
            $('.sidenav').sidenav();
            $('.modal').modal();
            $('#mikkiTable').DataTable({ 
                "pageLength": 5,
                "language": { "search": "" },
                "dom": 'frt<"bottom"p><"clear">'
            });
            
            $('.dataTables_filter input').attr("placeholder", "Search Inventory...").css({"border":"1px solid #e2e8f0","border-radius":"10px","padding":"0 15px"});

            <?php if ($userRole === 'Admin'): ?>
            const ctx = document.getElementById("myChart").getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Assigned Staff',
                        data: <?php echo json_encode($counts); ?>,
                        backgroundColor: '#00bcd4',
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>