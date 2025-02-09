<?php
    session_start();
    include 'db_connection.php';

    // 🔐 Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: admin_login.php");
        exit();
    }

    // Logout logic
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    }

    // Fetch total HSN records
    $query = "SELECT COUNT(*) AS total FROM hsn_codes";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $total_hsn_codes = $row['total'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard | PHP HSN Code Manager</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Admin Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">🏠 Home</a></li>
                        
                        <!-- Logout Button in Navbar -->
                        <li class="nav-item">
                            <form method="POST" class="d-inline">
                            <li class="nav-item"><a class="nav-link btn text-light ms-2" href="?logout=true">⚙️ Logout</a></li>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Dashboard Container -->
        <div class="container mt-5">
            <h2 class="text-center">📊 Admin Dashboard</h2>
            <div class="row mt-4">
                <!-- Total HSN Codes -->
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h4>📂 Total HSN Codes</h4>
                            <h2><?= $total_hsn_codes ?></h2>
                            <a href="view_hsn.php" class="btn btn-info">View All</a>
                        </div>
                    </div>
                </div>

                <!-- Add New HSN -->
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h4>➕ Add New HSN</h4>
                            <a href="add_hsn.php" class="btn btn-primary">Add Now</a>
                        </div>
                    </div>
                </div>

                <!-- Manage HSN Codes -->
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h4>⚙️ Manage HSN Codes</h4> <br>
                            <a href="manage_hsn.php" class="btn btn-warning">View & Edit</a> <br><br>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Upload CSV -->
                <div class="col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h4>📤 Upload CSV</h4>
                            <a href="upload_csv.php" class="btn btn-success">Upload Now</a>
                        </div>
                    </div>
                </div>

                <!-- Export Data -->
                <div class="col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h4>📥 Export Data</h4>
                            <a href="export.php" class="btn btn-info">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>