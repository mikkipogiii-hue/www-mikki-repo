<?php 

    session_start();
    require_once "../model/database.php";
    require_once "../model/InventoryModel.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: LoginPage.php");
        exit;
    }

    $username = $_SESSION['username'] ?? "User";
    $userRole = $_SESSION['role'] ?? 'Staff';

    $database = new Database();
    $db = $database->connectDB();
    $invModel = new InventoryModel($db);
    $items = $invModel->readItems()->fetchAll(PDO::FETCH_ASSOC);
    $expiringCount = $invModel->getExpiringSoonCount();
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
    <title>HMS Dashboard | Inventory Control</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            --sidebar-bg: #1a237e;
            --accent-cyan: #00bcd4;
            --bg-light: #f4f7f9;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
        }
        
        /* 2. LAYOUT: Full-page utilization with fixed sidebar */
        header, main, footer { padding-left: 280px; } 
        @media only screen and (max-width : 992px) { header, main, footer { padding-left: 0; } }

        /* 3. PREMIUM SIDEBAR: Matches Registration Aside */
        .sidenav { 
            background: var(--primary-gradient); 
            border: none; 
            width: 280px !important;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1); 
        }
        .user-view { 
            padding: 40px 32px !important; 
            margin-bottom: 20px;
            background: transparent !important;
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        .user-view .circle { border: 2px solid var(--accent-cyan); }
        
        .sidenav li a { 
            color: rgba(255,255,255,0.7) !important; 
            font-weight: 400;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }
        .sidenav li a i { color: rgba(255,255,255,0.5) !important; margin-right: 20px !important; }
        .sidenav li a:hover { background: rgba(255,255,255,0.1) !important; color: #fff !important; }
        .sidenav li.active { background: rgba(0, 188, 212, 0.2); }
        .sidenav .subheader { color: var(--accent-cyan) !important; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        /* 4. TOP NAVBAR */
        .nav-top { 
            background: white !important; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.03) !important; 
            height: 70px;
            line-height: 70px;
        }
        .nav-top .brand-logo { color: #1a237e !important; font-weight: 800; margin-left: 30px; }

        /* 5. STATS CARDS: Immersive Floating Design */
        .card-stats { 
            border-radius: 15px; 
            border: none; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
            transition: 0.3s ease;
        }
        .card-stats:hover { transform: translateY(-5px); }
        .card-stats i { font-size: 2.5rem; opacity: 0.8; }

        /* 6. TABLE: High-End UI */
        .inventory-card { 
            border-radius: 20px; 
            margin-top: 20px; 
            padding: 20px; 
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        }
        #mikkiTable thead { background: #f8fafd; }
        #mikkiTable th { color: #1a237e; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 20px; }
        #mikkiTable td { padding: 15px 20px; color: #455a64; border-bottom: 1px solid #f1f1f1; }
        
        .status-badge { 
            border-radius: 6px; 
            font-weight: 700; 
            padding: 0 12px;
            text-transform: uppercase;
            font-size: 11px;
        }

        /* 7. MODALS: Premium Popups */
        .modal { border-radius: 20px !important; overflow: hidden; }
        .modal .modal-content { padding: 40px; }
        .modal-footer { background: #f8fafd !important; padding: 15px 40px !important; }

        /* Floating Add Button Refined */
        .btn-add-main {
            background: var(--primary-gradient) !important;
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3) !important;
        }
    </style>
</head>
<body>

    <ul id="slide-out" class="sidenav sidenav-fixed">
        <li>
            <div class="user-view">
                <a href="#user"><img class="circle" src="https://ui-avatars.com/api/?name=<?= $username ?>&background=00bcd4&color=fff"></a>
                <a href="#name"><span class="white-text name" style="font-size: 19px; font-weight: 700;"><?= htmlspecialchars($username) ?></span></a>
                <a href="#role"><span class="white-text email" style="opacity: 0.8; font-weight: 300;"><?= $userRole ?> | Medical Staff</span></a>
            </div>
        </li>
        <li class="active"><a href="#!" class="waves-effect"><i class="material-icons">dashboard</i>Dashboard</a></li>
        <li><div class="divider" style="background: rgba(255,255,255,0.1)"></div></li>
        <li><a class="subheader">Management</a></li>
        <li><a href="#!" class="waves-effect"><i class="material-icons">local_pharmacy</i>Pharmacy Stock</a></li>
        <li><a href="#!" class="waves-effect"><i class="material-icons">biotech</i>Laboratory Supplies</a></li>
        <li><a href="#!" class="waves-effect"><i class="material-icons">history</i>Activity Logs</a></li>
        <li><div class="divider" style="background: rgba(255,255,255,0.1)"></div></li>
        <li><a href="#!" class="waves-effect red-text text-lighten-3" onclick="logoutFunc()"><i class="material-icons">logout</i>Sign Out</a></li>
    </ul>

    <main>
        <nav class="nav-top">
            <div class="nav-wrapper">
                <span class="brand-logo">HMS <span style="font-weight: 300;">Control Center</span></span>
            </div>
        </nav>

        <div class="container-fluid" style="padding: 40px;">
            <div class="row">
                <div class="col s12 m4">
                    <div class="card card-stats white">
                        <div class="card-content">
                            <i class="material-icons right" style="color: #4caf50;">inventory_2</i>
                            <span class="grey-text text-darken-1" style="font-weight: 600;">Total Inventory</span>
                            <h3 style="margin: 10px 0; font-weight: 800;"><?= $totalItems ?></h3>
                            <p class="green-text" style="font-size: 0.9rem;">Registered Medical Items</p>
                        </div>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="card card-stats white">
                        <div class="card-content">
                            <i class="material-icons right" style="color: #ff5252;">report_problem</i>
                            <span class="grey-text text-darken-1" style="font-weight: 600;">Low Stock Alert</span>
                            <h3 style="margin: 10px 0; font-weight: 800; color: #ff5252;"><?= $lowStockCount ?></h3>
                            <p class="grey-text" style="font-size: 0.9rem;">Requires Immediate Restock</p>
                        </div>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="card card-stats white">
                        <div class="card-content">
                            <i class="material-icons right" style="color: #2196f3;">event_available</i>
                            <span class="grey-text text-darken-1" style="font-weight: 600;">Server Date</span>
                            <h4 style="margin: 22px 0; font-weight: 700;"><?= date("M d, Y") ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div class="card inventory-card">
                        <div class="card-content">
                            <div class="row" style="display: flex; align-items: center; margin-bottom: 30px;">
                                <div class="col s6">
                                    <h5 style="font-weight: 800; color: #1a237e; margin: 0;">Stock Control Center</h5>
                                    <p class="grey-text">Real-time medical supply tracking system</p>
                                </div>
                                <div class="col s6 right-align">
                                    <a class="btn-floating btn-large waves-effect waves-light btn-add-main modal-trigger" href="#modalAddItem">
                                        <i class="material-icons">add</i>
                                    </a>
                                </div>
                            </div>

                            <table class="highlight responsive-table" id="mikkiTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Name</th>
                                        <th>Size/Unit</th>
                                        <th>Current Stock</th>
                                        <th>Expiration</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $item): 
                                        $exp = strtotime($item['expiration_date']);
                                        $isCriticalExp = ($exp - time() < (30 * 86400)); 
                                    ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #9e9e9e;">#<?= $item['item_id'] ?></td>
                                        <td><span style="font-weight: 600; color: #1a237e;"><?= htmlspecialchars($item['item_name']) ?></span></td>
                                        <td><?= htmlspecialchars($item['size']) ?></td>
                                        <td>
                                            <span class="status-badge chip <?= $item['current_stock'] <= 10 ? 'red lighten-5 red-text' : 'green lighten-5 green-text' ?>">
                                                <?= $item['current_stock'] ?> Units
                                            </span>
                                        </td>
                                        <td class="<?= $isCriticalExp ? 'expiring-soon' : '' ?>">
                                            <?= date("M d, Y", $exp) ?>
                                            <?= $isCriticalExp ? '<i class="material-icons tiny" style="vertical-align: middle; margin-left: 5px;">error</i>' : '' ?>
                                        </td>
                                        <td>
                                            <a href="#!" class="indigo-text text-darken-4" onclick="editItem(<?= $item['item_id'] ?>)"><i class="material-icons">edit</i></a>
                                            <a href="#!" class="red-text" style="margin-left: 15px;" onclick="deleteItem(<?= $item['item_id'] ?>)"><i class="material-icons">delete_outline</i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="modalAddItem" class="modal">
        <div class="modal-content">
            <h4 style="font-weight: 800; color: #1a237e;">Register New Supply</h4>
            <p class="grey-text">Fill in the medical supply details to add them to the system.</p>
            <div class="row" style="margin-top: 30px;">
                <div class="input-field col s12">
                    <i class="material-icons prefix">medication</i>
                    <input id="inv_name" type="text">
                    <label for="inv_name">Item Name</label>
                </div>
                <div class="input-field col s6">
                    <i class="material-icons prefix">straighten</i>
                    <input id="inv_size" type="text" placeholder="e.g. 500mg/Box">
                    <label for="inv_size">Size/Unit</label>
                </div>
                <div class="input-field col s6">
                    <i class="material-icons prefix">inventory_2</i>
                    <input id="inv_stock" type="number">
                    <label for="inv_stock">Initial Stock</label>
                </div>
                <div class="input-field col s12">
                    <i class="material-icons prefix">event_busy</i>
                    <input id="inv_expiry" type="date">
                    <label for="inv_expiry">Expiration Date</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-close btn-flat grey-text">Cancel</button>
            <button class="btn indigo darken-4 waves-effect waves-light" onclick="saveNewInventory()" style="border-radius: 8px;">Confirm Entry</button>
        </div>
    </div>

    <div id="modalEditStock" class="modal">
        <div class="modal-content">
            <h4 style="font-weight: 800; color: #1a237e;">Update Stock Level</h4>
            <p class="grey-text">Modify current physical count for this item.</p>
            <div class="row" style="margin-top: 30px;">
                <form id="formEditStock">
                    <input type="hidden" id="edit_item_id">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">edit_note</i>
                        <input id="edit_stock_qty" type="number" class="validate">
                        <label for="edit_stock_qty">New Quantity Count</label>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect btn-flat grey-text">Dismiss</a>
            <button class="btn indigo darken-4 waves-effect waves-light" onclick="saveStockUpdate()" style="border-radius: 8px;">Update Database</button>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../scripts/Service.js"></script>

    <script>
        $(document).ready(function(){
            $('.sidenav').sidenav();
            $('.modal').modal({ dismissible: false }); 
            $('#mikkiTable').DataTable({
                retrieve: true,
                "pageLength": 10,
                "language": { "search": "Quick Search:" }
            });
        });
    </script>
</body>
</html>