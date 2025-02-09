<?php
    include 'db_connection.php';

    $search = "";
    $results = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';
        
        if (!empty($search)) {
            $query = "SELECT * FROM hsn_codes WHERE 
                      product_name LIKE ? OR 
                      hsn_code LIKE ? OR 
                      category LIKE ?";
            $stmt = $conn->prepare($query);
            $searchParam = "%" . $search . "%";
            $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $query = "SELECT * FROM hsn_codes"; // Show all data if search is empty
            $results = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
        }
    }    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP HSN Code Manager</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
            <div class="container">
                <a class="navbar-brand" href="#">HSN Code Manager</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about1">About</a></li>

                        <!-- Admin Panel Button Logic -->
                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-success text-light ms-2" href="admin_dashboard.php">Admin Dashboard</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-warning text-dark ms-2" href="admin_login.php">Admin Panel</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav> <br><br><br>

        <!-- Hero Section -->
        <header class="hero-section text-center">
            <div class="container">
                <h3>Welcome to PHP HSN Code Manager</h3>
                <p class="lead">Easily search, manage, and organize HSN codes & GST rates.</p>
            </div>
        </header><br><br><br>

        <!-- Search Section -->
        <div class="container mt-5">
            <h2>🔍 Search HSN Codes</h2>
            <form method="POST" class="d-flex justify-content-center my-3">
                <input type="text" name="search" class="form-control w-50" placeholder="Enter Product Name, HSN Code, or Category" value="<?= htmlspecialchars($search) ?>" required>
                <button type="submit" class="btn btn-primary ms-2">Search</button>
            </form>

            <?php if (!empty($results)): ?>
                <table class="table table-hover table-dark mt-4">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>HSN Code</th>
                            <th>Category</th>
                            <th>GST Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['hsn_code']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['gst_rate']) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p class="text-center text-danger mt-3">❌ No results found.</p>
            <?php endif; ?>
        </div> <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

        <!-- About Section - HSN Codes -->
        <div id="about1" class="container about-section">
            <h2>📜 About HSN Codes</h2>
            <p>HSN (Harmonized System of Nomenclature) is an internationally accepted classification system for goods, developed by the World Customs Organization (WCO). It assigns a unique 6-digit code to each category of goods, ensuring a standardized approach to identifying products globally. Many countries, including India, have extended the code to 8 or 10 digits for further classification.</p>
            <p>This system is used primarily for:</p>
            <ul>
                <li>✅ <strong>International Trade</strong> – Helps in identifying goods consistently across borders.</li>
                <li>✅ <strong>Taxation (GST System in India)</strong> – Ensures proper GST rates are applied based on product classification.</li>
                <li>✅ <strong>Customs Clearance</strong> – Simplifies import/export documentation and processing.</li>
                <li>✅ <strong>Trade Statistics & Analysis</strong> – Governments use HSN codes for tracking goods and trade policies.</li>
            </ul>
            <p>In India, the Goods and Services Tax (GST) system mandates HSN codes for businesses:</p>
            <ul>
                <li>🔹 <strong>Businesses with turnover above ₹5 crore</strong> must use 6-digit HSN codes.</li>
                <li>🔹 <strong>Businesses below ₹5 crore</strong> need to use 4-digit HSN codes.</li>
                <li>🔹 <strong>Exports & Imports follow international 8-10 digit codes.</strong></li>
            </ul>
            <p>By using HSN codes, businesses can reduce errors in tax calculations and improve compliance with GST regulations.</p>
        </div> <br><br><br>

        <!-- About Section - Project -->
        <div id="about2" class="container about-section">
            <h3>🌟 About This Project</h3>
            <p>This <strong>PHP-based HSN Code Manager</strong> is designed to simplify the management of HSN codes and GST rates for businesses, accountants, and tax consultants.</p>
            <h4>🔍 Key Features of the Project</h4>
            <ul>
                <li>✅ <strong>Search Functionality</strong> – Users can search for HSN codes, product names, categories, and GST rates easily.</li>
                <li>✅ <strong>Database Management</strong> – Admins can add, update, or delete HSN code entries using a user-friendly interface.</li>
                <li>✅ <strong>CSV Import & Export</strong> – Bulk upload and export of HSN codes via CSV files, reducing manual work.</li>
                <li>✅ <strong>Admin Panel</strong> – A secure login-protected panel where admins can manage data efficiently.</li>
                <li>✅ <strong>Modern UI with Dark Theme</strong> – Uses Bootstrap 5 for a clean, responsive, and modern look.</li>
                <li>✅ <strong>Fast and Lightweight</strong> – Built with Core PHP and MySQL, ensuring smooth performance.</li>
                <li>✅ <strong>Role-Based Access</strong> – Only authorized admins can modify HSN data, maintaining data integrity.</li>
            </ul>
            <p>This project serves as a <strong>one-stop solution</strong> for businesses to handle HSN code classification, GST calculations, and compliance management efficiently. 🚀</p>
        </div> <br><br><br><br><br><br>

        <!-- Smooth Scroll & Home Button Refresh -->
        <script>
            $(document).ready(function(){
                // Smooth scrolling with navbar margin adjustment
                $("a[href^='#']").click(function(event){
                    event.preventDefault();
                    var target = $($.attr(this, "href"));

                    if (target.length) {
                        var navbarHeight = $(".navbar").outerHeight() + 20; // Adjusting for navbar height + margin
                        $('html, body').animate({
                            scrollTop: target.offset().top - navbarHeight
                        }, 800);
                    }
                });

                // Smooth page refresh on Home button click and scroll to top
                $(".nav-link[href='index.php']").click(function(event){
                    event.preventDefault();
                    $('html, body').animate({ scrollTop: 0 }, 400, function() {
                        $('body').fadeOut(400, function(){
                            location.reload();
                        }).fadeIn(400);
                    });
                });
            });
        </script>
    </body>
</html>