var fNameInput = document.getElementById("fName");
var lNameInput = document.getElementById("lName");

function validateNameFields(element) {
    element.value = element.value.replace(/[^a-zA-Z\s]/g, '');
}

if (fNameInput) {
    fNameInput.addEventListener("input", function() {
        validateNameFields(this);
    });
}

if (lNameInput) {
    lNameInput.addEventListener("input", function() {
        validateNameFields(this);
    });
}

function addFunc() {
    const fName = $("#fName").val();
    const lName = $("#lName").val();
    const bDate = $("#bDate").val();
    const phone = $("#phone").val();
    const email = $("#email").val();
    const username = $("#uName").val();
    const password = $("#pWord").val();
    const deptID = $("#deptID").val();
    const role = $("#role").val();

    if (!fName || !lName || !username || !password || !deptID || !role) {
        Swal.fire("Required", "Please fill in all professional credentials.", "error");
        return;
    }

    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { 
            fName: fName, 
            lName: lName, 
            bDate: bDate, 
            phone: phone, 
            email: email, 
            uName: username, 
            pWord: password, 
            deptID: deptID,
            role: role
        },
        success: function (res) {
            if (res.trim() === "success") {
                Swal.fire("Registered!", "Staff account created successfully.", "success")
                    .then(() => { redirectFunc(1); });
            } else {
                Swal.fire("Error", res, "error");
            }
        }
    });
}

function loginFunc() {
    const username = $("#login_uName").val();
    const password = $("#login_pWord").val();

    if (!username || !password) {
        Swal.fire("Login Failed", "Please enter both username and password.", "warning");
        return;
    }

    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { loginUName: username, loginPWord: password },
        success: function (response) {
            if (response.trim() === "true") {
                redirectFunc(2);
            } else {
                Swal.fire("Access Denied", "Invalid credentials.", "error");
            }
        }
    });
}

function saveNewInventory() {
    const name = $("#inv_name").val();
    const size = $("#inv_size").val();
    const stock = $("#inv_stock").val();
    const expiry = $("#inv_expiry").val();

    $.ajax({
        url: "../controllers/InventoryController.php", 
        type: "POST",
        data: { 
            action: "add", 
            name: name, 
            size: size, 
            stock: stock, 
            expiry: expiry 
        },
        success: function(res) {
            if(res.trim() === "success") {
                M.toast({html: 'Item Added!', classes: 'green rounded'});
                location.reload();
            } else {
                Swal.fire("Error", res, "error");
            }
        },
        error: function(xhr) {
            Swal.fire("System Error", xhr.status + " " + xhr.statusText, "error");
        }
    });
}

function deleteItem(itemId) {
    Swal.fire({
        title: "Are you sure?",
        text: "This item will be permanently removed from medical inventory.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/InventoryController.php",
                type: "POST",
                data: { action: "delete", id: itemId },
                success: function(res) {
                    if (res.trim() === "success") {
                        Swal.fire("Deleted!", "Item has been removed.", "success")
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire("Error", "Could not delete: " + res, "error");
                    }
                }
            });
        }
    });
}

function editItem(itemId) {
    $('#formEditStock')[0].reset(); 
    $('#edit_item_id').val(itemId);
    var instance = M.Modal.getInstance(document.getElementById('modalEditStock'));
    instance.open();
    setTimeout(() => { $('#edit_stock_qty').focus(); }, 300);
}

function saveStockUpdate() {
    const itemId = $('#edit_item_id').val();
    const newQty = $('#edit_stock_qty').val();

    if (!newQty || newQty < 0) {
        Swal.fire("Incomplete", "Please enter a valid stock amount.", "warning");
        return;
    }

    $.ajax({
        url: "../controllers/InventoryController.php",
        type: "POST",
        data: { 
            action: "adjust", 
            id: itemId,       
            newQty: newQty    
        },
        success: function(res) {
            if(res.trim() === "success") {
                $('#modalEditStock').modal('close');
                M.toast({html: 'Stock Updated Successfully', classes: 'blue rounded'});
                location.reload();
            } else {
                Swal.fire("Error", "Update failed: " + res, "error");
            }
        },
        error: function(xhr) {
            Swal.fire("System Error", xhr.status + " " + xhr.statusText, "error");
        }
    });
}

function redirectFunc(redirectID) {
    const paths = { 1: "LoginPage.php", 2: "DashboardPage.php", 3: "RegistrationPage.php" };
    if (paths[redirectID]) { window.location.href = paths[redirectID]; }
}

function logoutFunc() {
    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { action: "logout" },
        success: function () { redirectFunc(1); }
    });
}