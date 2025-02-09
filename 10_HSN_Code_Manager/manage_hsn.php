<?php
    session_start();
    include 'db_connection.php';

    // Logout logic
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    }

    // Delete logic
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        $deleteQuery = "DELETE FROM hsn_codes WHERE id = '$id'";
        if (!mysqli_query($conn, $deleteQuery)) {
            echo "<div class='alert alert-danger fixed-top' style='margin-top: 70px; width: 400px; left: 50%; transform: translateX(-50%);'>Error deleting record: " . mysqli_error($conn) . "</div>";
        } else {
            echo "<div class='alert alert-success fixed-top' style='margin-top: 70px; width: 400px; left: 50%; transform: translateX(-50%);'>Record deleted successfully.</div>";
        }
        exit(); // Stop further execution for AJAX requests
    }

    // Edit logic (modified to be used with AJAX)
    if (isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $hsn_code = mysqli_real_escape_string($conn, $_POST['hsn_code']);
        $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $gst_rate = mysqli_real_escape_string($conn, $_POST['gst_rate']);

        $updateQuery = "UPDATE hsn_codes SET hsn_code='$hsn_code', product_name='$product_name', category='$category', gst_rate='$gst_rate' WHERE id='$id'";

        if (!mysqli_query($conn, $updateQuery)) {
            echo "<div class='alert alert-danger fixed-top' style='margin-top: 70px; width: 400px; left: 50%; transform: translateX(-50%);'>Error updating record: " . mysqli_error($conn) . "</div>";
        } else {
            echo "<div class='alert alert-success fixed-top' style='margin-top: 70px; width: 400px; left: 50%; transform: translateX(-50%);'>Record updated successfully.</div>";
        }
        exit(); //Important: stop further execution for AJAX requests
    }

    // Fetch unique categories and GST rates (from view_hsn.php)
    $categoryQuery = "SELECT DISTINCT category FROM hsn_codes ORDER BY category ASC";
    $categoryResult = mysqli_query($conn, $categoryQuery);

    $gstQuery = "SELECT DISTINCT gst_rate FROM hsn_codes ORDER BY gst_rate ASC";
    $gstResult = mysqli_query($conn, $gstQuery);

    // Pagination setup
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Search and filter handling (from view_hsn.php)
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
    $category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : "";
    $gstRate = isset($_GET['gst_rate']) ? mysqli_real_escape_string($conn, $_GET['gst_rate']) : "";

    // Build query with filters (from view_hsn.php)
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

    // Serial number logic
    $serialNumber = $offset + 1;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage HSN Codes</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
            integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            /* Dark theme for modals */
            .modal-content {
                background-color: #343a40;
                color: white;
                border: 1px solid #6c757d;
            }

            .modal-header {
                border-bottom: 1px solid #6c757d;
            }

            .modal-footer {
                border-top: 1px solid #6c757d;
            }
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
                        <li class="nav-item"><a class="nav-link" href="export.php">🔻Export</a></li>
                        <li class="nav-item"><a class="nav-link btn text-light ms-2" href="?logout=true">⚙️ Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
            <div class="card shadow-lg p-4">
                <h2 class="text-center text-warning fw-bold">✏️ Manage HSN Codes</h2> <!-- Search and Filter from view_hsn.php -->
                <div class="d-flex justify-content-end gap-3 mb-3">
                    <button class="border-0 bg-transparent" id="searchBtn" style="font-size: 1.5rem;"> 🔍 </button>
                    <button class="border-0 bg-transparent" id="filterBtn" style="font-size: 1.5rem;"> 📜 </button>
                </div>

                <!-- Search Box -->
                <div id="searchBox" class="d-none mb-3">
                    <div class="input-group">
                        <input type="text" id="search" class="form-control" placeholder="🔍 Search HSN Code or Product"
                            value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-warning" onclick="applyFilters()">🔍</button>
                    </div>
                </div>

                <!-- Filter Box -->
                <div id="filterBox" class="d-none mb-3">
                    <div class="input-group">
                        <select id="category" class="form-select">
                            <option value="">🗂 All Categories</option>
                            <?php
                            mysqli_data_seek($categoryResult, 0); // Reset pointer
                            while ($row = mysqli_fetch_assoc($categoryResult)) { ?>
                                <option value="<?= htmlspecialchars($row['category']) ?>"
                                    <?= ($category == $row['category']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['category']) ?>
                                </option>
                            <?php } ?>
                        </select>

                        <select id="gst_rate" class="form-select">
                            <option value="">💰 All GST Rates</option>
                            <?php
                            mysqli_data_seek($gstResult, 0); // Reset pointer
                            while ($row = mysqli_fetch_assoc($gstResult)) { ?>
                                <option value="<?= htmlspecialchars($row['gst_rate']) ?>"
                                    <?= ($gstRate == $row['gst_rate']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['gst_rate']) ?>%
                                </option>
                            <?php } ?>
                        </select>

                        <button class="btn btn-warning" onclick="applyFilters()">Apply</button>
                    </div>
                </div>

                <table class="table table-dark table-hover mt-4">
                    <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>HSN Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>GST Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td>
                                    <?= str_pad($serialNumber++, 2, '0', STR_PAD_LEFT); ?>
                                </td>
                                <td>
                                    <?= $row['hsn_code']; ?>
                                </td>
                                <td>
                                    <?= $row['product_name']; ?>
                                </td>
                                <td>
                                    <?= $row['category']; ?>
                                </td>
                                <td>
                                    <?= $row['gst_rate']; ?>%
                                </td>
                                <td>
                                    <button class="edit-btn border-0 bg-transparent" data-id="<?= $row['id']; ?>"
                                        data-hsn="<?= $row['hsn_code']; ?>" data-name="<?= $row['product_name']; ?>"
                                        data-category="<?= $row['category']; ?>" data-gst="<?= $row['gst_rate']; ?>"
                                        style="font-size: 1.5rem;">
                                        ✏️
                                    </button>
                                    <button class="delete-btn border-0 bg-transparent" data-id="<?= $row['id']; ?>"
                                        style="font-size: 1.5rem;">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table> <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link"
                                    href="?page=<?= $i; ?>&search=<?= htmlspecialchars($search) ?>&category=<?= htmlspecialchars($category) ?>&gst_rate=<?= htmlspecialchars($gstRate) ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit HSN Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Edit Form will be injected here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1"
            aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this HSN code?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // JavaScript for Search and Filter (from view_hsn.php)
            document.getElementById('searchBtn').addEventListener('click', function () {
                document.getElementById('searchBox').classList.toggle('d-none');
            });

            document.getElementById('filterBtn').addEventListener('click', function () {
                document.getElementById('filterBox').classList.toggle('d-none');
            });

            function applyFilters() {
                let search = document.getElementById('search').value;
                let category = document.getElementById('category').value;
                let gstRate = document.getElementById('gst_rate').value;
                let params = new URLSearchParams({
                    search,
                    category,
                    gst_rate: gstRate
                }).toString();
                window.location.href = 'manage_hsn.php?' + params; // Corrected URL
            }

            // JavaScript for Edit Modal
            $(document).on("click", ".edit-btn", function () {
                let id = $(this).data("id");
                let hsn = $(this).data("hsn");
                let name = $(this).data("name");
                let category = $(this).data("category");
                let gst = $(this).data("gst");

                let form = `
                    <form id='editForm'>
                        <input type='hidden' name='edit_id' value='${id}'>
                        <div class="mb-3">
                            <label for="hsn_code" class="form-label">HSN Code</label>
                            <input type='text' class='form-control' id='hsn_code' name='hsn_code' value='${hsn}' required>
                        </div>
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type='text' class='form-control' id='product_name' name='product_name' value='${name}' required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type='text' class='form-control' id='category' name='category' value='${category}' required>
                        </div>
                        <div class="mb-3">
                            <label for="gst_rate" class="form-label">GST Rate</label>
                            <input type='text' class='form-control' id='gst_rate' name='gst_rate' value='${gst}' required>
                        </div>
                    </form>
                `;

                $("#editModal .modal-body").html(form); // Inject the form into the modal body
                $('#editModal').modal('show'); // Show the modal
            });

            // Handle save changes inside the modal
            $(document).on("click", "#saveChanges", function () {
                $.ajax({
                    type: "POST",
                    url: "manage_hsn.php", // The same page
                    data: $("#editForm").serialize(),
                    success: function (response) {
                        $('#editModal').modal('hide');
                        // Display alert after successful update
                        showAlert('Record updated successfully.', 'success');
                        setTimeout(function () {
                            location.reload(); // Reload the page after the alert disappears
                        }, 1000);
                    },
                    error: function (error) {
                        showAlert('Error updating record: ' + error, 'danger');
                    }
                });
            });

            // JavaScript for Delete Confirmation Modal
            let deleteId;
            $(document).on("click", ".delete-btn", function () {
                deleteId = $(this).data("id");
                $('#deleteConfirmationModal').modal('show');
            });

            $(document).on("click", "#confirmDelete", function () {
                $.ajax({
                    type: "POST",
                    url: "manage_hsn.php",
                    data: {
                        delete_id: deleteId
                    },
                    success: function (response) {
                        $('#deleteConfirmationModal').modal('hide');
                        showAlert('Record deleted successfully.', 'success');
                        setTimeout(function () {
                            location.reload(); // Reload the page after the alert disappears
                        }, 1000);
                    },
                    error: function (error) {
                        showAlert('Error deleting record: ' + error, 'danger');
                    }
                });
            });

            // Function to show alerts
            function showAlert(message, type) {
                let alertDiv = `<div class='alert alert-${type} fixed-top' style='margin-top: 70px; width: 400px; left: 50%; transform: translateX(-50%);'>${message}</div>`;
                $('body').append(alertDiv);  // Append alert to the body

                setTimeout(function () {
                    $('.alert.fixed-top').remove();  // Remove the alert after 4 seconds
                }, 1000);
            }
        </script>
    </body>
</html>