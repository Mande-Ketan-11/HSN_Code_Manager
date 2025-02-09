<?php
    include 'db_connection.php';
    session_start();

    $success = "";
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $confirm_password = trim($_POST["confirm_password"]);

        if (!empty($username) && !empty($password) && !empty($confirm_password)) {
            if ($password !== $confirm_password) {
                $error = "⚠️ Passwords do not match!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $check_query = "SELECT id FROM admin_users WHERE username = ?";
                $stmt_check = $conn->prepare($check_query);
                $stmt_check->bind_param("s", $username);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    $error = "❌ Username already exists!";
                } else {
                    $insert_query = "INSERT INTO admin_users (username, password) VALUES (?, ?)";
                    $stmt = $conn->prepare($insert_query);
                    $stmt->bind_param("ss", $username, $hashed_password);
                    
                    if ($stmt->execute()) {
                        $success = "✅ Registration successful! <a href='admin_login.php'>Login Here</a>";
                    } else {
                        $error = "❌ Registration failed. Try again.";
                    }
                    $stmt->close();
                }
                $stmt_check->close();
            }
        } else {
            $error = "⚠️ Please fill in all fields!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Registration - PHP HSN Code Manager</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">HSN Code Manager</a>
            </div>
        </nav>

        <!-- Registration Form -->
        <div class="container register-container">
            <div class="register-box">
                <h2 class="text-center">Admin Registration</h2>
                <?php if ($error): ?>
                    <p class="text-danger text-center" id="error-message"><?= $error ?></p>
                    <script>
                        setTimeout(function() {
                            let errorMsg = document.getElementById('error-message');
                            if (errorMsg) {
                                errorMsg.style.display = 'none';
                            }
                        }, 5000); // Hides error message after 5 seconds
                    </script>
                <?php endif; ?>

                <?php if ($success): ?>
                    <p class="text-success text-center" id="success-message"><?= $success ?></p>
                    <script>
                        setTimeout(function() {
                            let successMsg = document.getElementById('success-message');
                            if (successMsg) {
                                successMsg.style.display = 'none';
                                window.location.href = 'admin_login.php'; // Redirect after success
                            }
                        }, 3000); // Hides success message after 3 seconds and redirects
                    </script>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="admin_login.php">Login here</a></p>
            </div>
        </div>
    </body>
</html>