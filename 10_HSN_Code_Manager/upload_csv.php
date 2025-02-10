<?php
    session_start();
    include 'db_connection.php';
    
    // Logout logic
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    }
    
    // Create directory if it doesn't exist
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Initialize variables
    $uploadSuccess = false;
    $duplicateFound = false;  // New variable to track duplicates
    $duplicateProductName = ''; // Store the duplicate product name
    
    // Handle file upload
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
        $timestamp = time();
        $randomKey = bin2hex(random_bytes(5));
        $filename = $uploadDir . $timestamp . "_" . $randomKey . ".csv";
    
        if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $filename)) {
            $uploadSuccess = true; // Show green tick
    
            // Open and process CSV
            if (($handle = fopen($filename, "r")) !== FALSE) {
                fgetcsv($handle); // Skip header row
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $product_name = trim($data[0]);
                    $hsn_code = trim($data[1]);
                    $category = trim($data[2]);
                    $gst_rate = trim($data[3]);
    
                    // Check for duplicates based on product_name
                    $checkQuery = "SELECT * FROM hsn_codes WHERE product_name = ?";
                    $stmtCheck = $conn->prepare($checkQuery);
                    $stmtCheck->bind_param("s", $product_name);  // Use product_name for checking
                    $stmtCheck->execute();
                    $resultCheck = $stmtCheck->get_result();
    
                    if ($resultCheck->num_rows == 0) {
                        // No duplicate found, proceed with insertion
                        $insertQuery = "INSERT INTO hsn_codes (product_name, hsn_code, category, gst_rate) VALUES (?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($insertQuery);
                        $stmtInsert->bind_param("ssss", $product_name, $hsn_code, $category, $gst_rate);
                        $stmtInsert->execute();
                    } else {
                        // Duplicate found, set the flag and store the product name
                        $duplicateFound = true;
                        $duplicateProductName = $product_name;
                        break; // Exit the loop as we found a duplicate
                    }
                }
                fclose($handle);
    
                if (!$duplicateFound) {
                    // If no duplicates were found, show success message and redirect
                    echo "<script>
                        setTimeout(function() {
                            document.getElementById('success-alert').style.display = 'block';
                        }, 1000);
                        setTimeout(function() {
                            window.location.href = 'view_hsn.php';
                        }, 2000);
                    </script>";
                } else {
                    // If duplicates were found, show warning message
                    echo "<script>
                        setTimeout(function() {
                            document.getElementById('duplicate-alert').style.display = 'block';
                        }, 1000);
                    </script>";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Upload CSV</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin_dashboard.php">🔧 Admin Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">🏡 Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="view_hsn.php">📂 View Data</a></li>
                        <li class="nav-item"><a class="nav-link btn text-light ms-2" href="?logout=true">⚙️ Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="success-alert" class="alert alert-success text-center" style="display: none; position: fixed; bottom: 20px; width: 100%;">
            ✅ File uploaded & data imported successfully! Redirecting...
        </div>

        <!-- Duplicate alert message -->
        <div id="duplicate-alert" class="alert alert-danger text-center" style="display: none; position: fixed; bottom: 20px; width: 100%;">
            ⚠️ Duplicate product name found: <?php echo htmlspecialchars($duplicateProductName); ?>.  Data not imported.
        </div>

        <div class="upload-container">
            <div class="upload-box">
                <h2 class="text-center text-warning fw-bold">📂 Upload CSV File</h2>
                <?php if ($uploadSuccess): ?>
                    <div class="success-icon">✅</div>
                <?php endif; ?>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="file-upload" class="custom-file-upload">📁 Choose CSV File</label>
                    <input type="file" id="file-upload" name="csv_file" accept=".csv" required>
                    <button type="submit" class="upload-btn">🚀 Upload & Import</button>
                </form>
            </div>
        </div>
    </body>
</html>
