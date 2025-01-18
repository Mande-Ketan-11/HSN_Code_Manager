<?php
    require 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HSN Code Reader</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body class="bg-dark text-light">
        <div class="container py-5">
            <h1 class="text-center text-warning mb-4">HSN Code Reader</h1>

            <!-- File Upload Section -->
            <section id="upload-section" class="mb-5">
                <div class="card bg-secondary text-light">
                    <div class="card-body">
                        <h2 class="card-title">Upload HSN File</h2>
                        <form id="upload-form" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="file" class="form-label">Choose File (XLS, XLSX):</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                            </div>
                            <button type="submit" name="upload" class="btn btn-warning w-100">Upload</button>
                        </form>
                        <div id="upload-status" class="mt-3"></div>
                    </div>
                </div>
            </section>

            <!-- Data Management Section -->
            <section id="data-section" class="mb-5">
                <h2 class="text-warning">Manage HSN Data</h2>
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>HSN Code</th>
                                <th>Description</th>
                                <th>Tax Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="data-table">
                            <!-- Data rows will be dynamically loaded using JavaScript -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Add/Edit Modal -->
            <div class="modal fade" id="data-modal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="dataModalLabel">Add/Edit HSN Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="data-form">
                                <input type="hidden" id="data-id">
                                <div class="mb-3">
                                    <label for="hsn-code" class="form-label">HSN Code</label>
                                    <input type="text" id="hsn-code" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" class="form-control" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="tax-rate" class="form-label">Tax Rate</label>
                                    <input type="number" step="0.01" id="tax-rate" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS & Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="script.js"></script>
    </body>
</html>