<?php
    session_start();
    include 'db_connection.php';

    // Logout logic
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    }

    // Fetch unique categories and GST rates
    $categoryQuery = "SELECT DISTINCT category FROM hsn_codes ORDER BY category ASC";
    $categoryResult = mysqli_query($conn, $categoryQuery);

    $gstQuery = "SELECT DISTINCT gst_rate FROM hsn_codes ORDER BY gst_rate ASC";
    $gstResult = mysqli_query($conn, $gstQuery);

    // Pagination setup
    $limit = 10; // Records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Search and filter handling
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
    $category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : "";
    $gstRate = isset($_GET['gst_rate']) ? mysqli_real_escape_string($conn, $_GET['gst_rate']) : "";

    // Build query with filters
    $query = "SELECT * FROM hsn_codes WHERE 1";
    if (!empty($search)) {
        $query .= " AND (hsn_code LIKE '%$search%' OR product_name LIKE '%$search%')";
    }
    if (!empty($category)) {
        $query .= " AND category = '$category'";
    }
    if (!empty($gstRate)) {
        $query .= " AND gst_rate = '$gstRate'";
    }

    $totalQuery = "SELECT COUNT(*) AS total FROM ($query) AS filtered";
    $totalResult = mysqli_query($conn, $totalQuery);
    $totalRecords = mysqli_fetch_assoc($totalResult)['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Apply pagination
    $query .= " ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);

    // Function to generate serial numbers across pages
    function generateSerialNumber($index, $offset) {
        return str_pad($index + 1 + $offset, 2, '0', STR_PAD_LEFT);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View HSN Codes</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            .fade-transition { opacity: 0; transition: opacity 0.5s ease-in-out; }
            .fade-transition.show { opacity: 1; }
        </style>
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
                        <li class="nav-item"><a class="nav-link" href="manage_hsn.php">✏️ Edit Data</a></li>
                        <li class="nav-item"><a class="nav-link btn text-light ms-2" href="?logout=true">⚙️ Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <div class="container mt-4">
            <div class="card shadow-lg p-4">
                <h2 class="text-center text-warning fw-bold">📂 View HSN Codes</h2>

                <div class="d-flex justify-content-end gap-3 mb-3">
                    <button class="border-0 bg-transparent" id="searchBtn" style="font-size: 1.5rem;"> 🔍 </button>
                    <button class="border-0 bg-transparent" id="filterBtn" style="font-size: 1.5rem;"> 📜 </button>
                </div>

                <!-- Search Box -->
                <div id="searchBox" class="d-none mb-3">
                    <div class="input-group">
                        <input type="text" id="search" class="form-control" placeholder="🔍 Search HSN Code or Product">
                        <button class="btn btn-warning" onclick="applyFilters()">🔍</button>
                    </div>
                </div>

                <!-- Filter Box -->
                <div id="filterBox" class="d-none mb-3">
                    <div class="input-group">
                        <select id="category" class="form-select">
                            <option value="">🗂 All Categories</option>
                            <?php while ($row = mysqli_fetch_assoc($categoryResult)) { ?>
                                <option value="<?= htmlspecialchars($row['category']) ?>">
                                    <?= htmlspecialchars($row['category']) ?>
                                </option>
                            <?php } ?>
                        </select>

                        <select id="gst_rate" class="form-select">
                            <option value="">💰 All GST Rates</option>
                            <?php while ($row = mysqli_fetch_assoc($gstResult)) { ?>
                                <option value="<?= htmlspecialchars($row['gst_rate']) ?>">
                                    <?= htmlspecialchars($row['gst_rate']) ?>%
                                </option>
                            <?php } ?>
                        </select>

                        <button class="btn btn-warning" onclick="applyFilters()">Apply</button>
                    </div>
                </div>

                <!-- Data Table -->
                <table class="table table-dark table-hover" id="hsnTable">
                    <thead>
                        <tr>
                            <th>Serial</th>
                            <th>HSN Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>GST Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 0; while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo generateSerialNumber($index++, $offset); ?></td>
                            <td><?php echo $row['hsn_code']; ?></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['gst_rate']; ?>%</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="text-center mt-3">
                    <?php if ($totalPages > 1) { ?>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"> <?php echo $i; ?> </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    <?php } ?>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('searchBtn').addEventListener('click', function() {
                document.getElementById('searchBox').classList.toggle('d-none');
            });

            document.getElementById('filterBtn').addEventListener('click', function() {
                document.getElementById('filterBox').classList.toggle('d-none');
            });

            function applyFilters() {
                let search = document.getElementById('search').value;
                let category = document.getElementById('category').value;
                let gstRate = document.getElementById('gst_rate').value;
                let params = new URLSearchParams({ search, category, gst_rate: gstRate }).toString();
                window.location.href = 'view_hsn.php?' + params;
            }
          
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("pageContent").classList.add("show");
            });
        </script>
    </body>
</html>