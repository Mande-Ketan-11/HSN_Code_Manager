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
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Filters
    $category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : "";
    $gstRate = isset($_GET['gst_rate']) ? mysqli_real_escape_string($conn, $_GET['gst_rate']) : "";

    $query = "SELECT * FROM hsn_codes WHERE 1";
    if (!empty($category)) {
        $query .= " AND category = '$category'";
    }
    if (!empty($gstRate)) {
        $query .= " AND gst_rate = '$gstRate'";
    }

    // Get total records for pagination
    $totalQuery = "SELECT COUNT(*) AS total FROM ($query) AS filtered";
    $totalResult = mysqli_query($conn, $totalQuery);
    $totalRecords = mysqli_fetch_assoc($totalResult)['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Apply limit and offset
    $query .= " ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);

    // Serial number logic (formatted as 01, 02, etc.)
    $serialNumber = $offset + 1;

    // Fetch all data for export
    $exportQuery = "SELECT * FROM hsn_codes WHERE 1";
    if (!empty($category)) {
        $exportQuery .= " AND category = '$category'";
    }
    if (!empty($gstRate)) {
        $exportQuery .= " AND gst_rate = '$gstRate'";
    }
    $exportQuery .= " ORDER BY created_at ASC";
    $exportResult = mysqli_query($conn, $exportQuery);
    $exportData = mysqli_fetch_all($exportResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Export HSN Codes</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
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
                <h2 class="text-center text-warning fw-bold">📂 Export HSN Codes</h2>

                <div class="mb-3">
                    <label class="form-label">Filter by Category:</label>
                    <select id="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php while ($row = mysqli_fetch_assoc($categoryResult)) { ?>
                            <option value="<?= htmlspecialchars($row['category']) ?>">
                                <?= htmlspecialchars($row['category']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    
                    <label class="form-label mt-2">Filter by GST Rate:</label>
                    <select id="gst_rate" class="form-select">
                        <option value="">All GST Rates</option>
                        <?php while ($row = mysqli_fetch_assoc($gstResult)) { ?>
                            <option value="<?= htmlspecialchars($row['gst_rate']) ?>">
                                <?= htmlspecialchars($row['gst_rate']) ?>%
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="export-buttons d-flex justify-content-center gap-2">
                    <button class="btn btn-warning" onclick="applyFilters()">Apply Filters</button>
                    <button class="btn btn-secondary" onclick="resetFilters()">🔄 Refresh</button>
                    <button class="btn btn-success" onclick="exportToPDF()">📄 Download as PDF</button>
                    <button class="btn btn-primary" onclick="exportToCSV()">📊 Download as Excel(CSV)</button>
                </div>

                <table class="table table-dark table-hover mt-4">
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
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= str_pad($serialNumber++, 2, '0', STR_PAD_LEFT); ?></td>
                            <td><?= $row['hsn_code']; ?></td>
                            <td><?= $row['product_name']; ?></td>
                            <td><?= $row['category']; ?></td>
                            <td><?= $row['gst_rate']; ?>%</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>

        <script>
            function exportToPDF() {
                const { jsPDF } = window.jspdf;
                let doc = new jsPDF();
                doc.text('HSN Code Export', 10, 10);

                let data = <?= json_encode($exportData); ?>;
                let tableData = data.map((row, index) => [
                    String(index + 1).padStart(2, '0'),
                    row.hsn_code,
                    row.product_name,
                    row.category,
                    row.gst_rate + '%'
                ]);

                doc.autoTable({
                    head: [['Serial', 'HSN Code', 'Product Name', 'Category', 'GST Rate']],
                    body: tableData,
                    startY: 20,
                    theme: 'grid'
                });
                // Generate a 6-character unique key (random letters and numbers)
                let uniqueKey = Math.random().toString(36).substring(2, 8).toUpperCase();
                
                // Save the file with the unique key
                doc.save(`HSN_${uniqueKey}.pdf`);
            }

            function exportToCSV() {
                let csv = "Serial,HSN Code,Product Name,Category,GST Rate\n";
                let data = <?= json_encode($exportData); ?>;
                data.forEach((row, index) => {
                    csv += `${String(index + 1).padStart(2, '0')},${row.hsn_code},${row.product_name},${row.category},${row.gst_rate}%\n`;
                });

                let blob = new Blob([csv], { type: 'text/csv' });
                let link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                let uniqueKey = Math.random().toString(36).substring(2, 8).toUpperCase();  // Generate a 6-character unique key
                link.download = `HSN_${uniqueKey}.csv`;
                link.click();
            }

            function applyFilters() {
                let category = document.getElementById('category').value;
                let gstRate = document.getElementById('gst_rate').value;
                let params = new URLSearchParams({ category, gst_rate: gstRate }).toString();
                window.location.href = 'export.php?' + params;
            }

            function resetFilters() {
                window.location.href = 'export.php';
            }
        </script>
    </body>
</html>