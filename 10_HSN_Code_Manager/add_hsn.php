<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: admin_login.php");
        exit();
    }
    include 'db_connection.php';

    $success = "";
    $error = "";

    // Logout logic
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_name = trim($_POST['product_name']);
        $hsn_code = trim($_POST['hsn_code']);
        $category = trim($_POST['category']);
        $gst_rate = trim($_POST['gst_rate']);

        if (!empty($hsn_code) && !empty($product_name) && !empty($category) && !empty($gst_rate)) {
            $query = "INSERT INTO hsn_codes (hsn_code, product_name, category, gst_rate) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $hsn_code, $product_name, $category, $gst_rate);
            if ($stmt->execute()) {
                $success = "✅ HSN Code added successfully!";
            } else {
                $error = "❌ Failed to add HSN Code!";
            }
            $stmt->close();
        } else {
            $error = "⚠️ All fields are required!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add HSN Code</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        
        <!-- Admin Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin_dashboard.php">🔧 Admin Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">🏠 Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="view_hsn.php">📂 All HSN Codes</a></li>
                        <li class="nav-item"><a class="nav-link btn text-light ms-2" href="?logout=true">⚙️ Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Add HSN Code Form -->
        <div class="container mt-5">
            <div class="card shadow-lg p-4">
                <h2 class="text-center text-primary fw-bold">➕ Add New HSN Code</h2>
                <?php if ($success) { ?>
                    <div class='alert alert-success text-center'><?= $success; ?></div>
                    <script>
                        setTimeout(function() {
                            document.querySelector('.alert-success').style.display = 'none';
                            window.location.href = 'view_hsn.php'; // Redirect after success
                        }, 3000); // 3 seconds delay
                    </script>
                <?php } ?>
                <?php if ($error) { ?>
                    <div class='alert alert-danger text-center'><?= $error; ?></div>
                    <script>
                        setTimeout(function() {
                            document.querySelector('.alert-danger').style.display = 'none';
                        }, 5000); // Hide error alert after 5 seconds
                    </script>
                <?php } ?>
                <form action="" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="product_name" class="form-label fw-bold">Product Name:</label>
                        <input type="text" name="product_name" id="product_name" class="form-control border-primary" required>
                    </div>
                    <div class="mb-3">
                        <label for="hsn_code" class="form-label fw-bold">HSN Code:</label>
                        <input type="text" name="hsn_code" id="hsn_code" class="form-control border-primary" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label fw-bold">Category:</label>
                        <input type="text" name="category" id="category" class="form-control border-primary" required>
                    </div>
                    <div class="mb-3">
                        <label for="gst_rate" class="form-label fw-bold">GST Rate (%):</label>
                        <select name="gst_rate" id="gst_rate" class="form-select border-primary" required>
                            <option value="" selected disabled>-- Select GST Rate --</option>
                            <option value="0">0% - Essential Goods</option>
                            <option value="5">5% - Packaged Food, Transport</option>
                            <option value="12">12% - Processed Food, Mobile Phones</option>
                            <option value="18">18% - Electronics, Restaurants</option>
                            <option value="28">28% - Luxury Items, Cars</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary fw-bold">✅ Submit</button>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>