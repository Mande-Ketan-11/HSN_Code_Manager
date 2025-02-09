<?php
    include 'db_connection.php';
    session_start();

    // Redirect to login if not authenticated
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: admin_login.php");
        exit();
    }

    $error = "";
    $success = "";

    // Handle Add HSN Code
    if (isset($_POST['add_hsn'])) {
        $hsn_code = trim($_POST['hsn_code']);
        $description = trim($_POST['description']);
        $tax_rate = trim($_POST['tax_rate']);
        
        if (!empty($hsn_code) && !empty($description) && !empty($tax_rate)) {
            $query = "INSERT INTO hsn_codes (hsn_code, description, tax_rate) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $hsn_code, $description, $tax_rate);
            if ($stmt->execute()) {
                $success = "✅ HSN Code added successfully!";
            } else {
                $error = "❌ Error adding HSN Code!";
            }
            $stmt->close();
        } else {
            $error = "⚠️ All fields are required!";
        }
    }

    // Handle Delete HSN Code
    if (isset($_GET['delete_id'])) {
        $id = $_GET['delete_id'];
        $query = "DELETE FROM hsn_codes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "✅ HSN Code deleted successfully!";
        } else {
            $error = "❌ Error deleting HSN Code!";
        }
        $stmt->close();
    }

    // Fetch all HSN Codes
    $query = "SELECT * FROM hsn_codes ORDER BY id DESC";
    $result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage HSN Codes</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <h2>📌 Manage HSN Codes</h2>
            <?php if ($error) echo "<p class='alert alert-danger'>$error</p>"; ?>
            <?php if ($success) echo "<p class='alert alert-success'>$success</p>"; ?>

            <form method="POST" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="hsn_code" class="form-control" placeholder="HSN Code" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="description" class="form-control" placeholder="Description" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="tax_rate" class="form-control" placeholder="Tax Rate (%)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_hsn" class="btn btn-primary">➕ Add</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>HSN Code</th>
                        <th>Description</th>
                        <th>Tax Rate (%)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['hsn_code']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['tax_rate']; ?>%</td>
                        <td>
                            <a href="admin_hsn_manage.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">❌ Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </body>
</html>