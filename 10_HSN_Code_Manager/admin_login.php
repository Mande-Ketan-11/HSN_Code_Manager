<?php
    session_start();
    include 'db_connection.php';

    // ✅ Redirect logged-in admins to dashboard
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header("Location: admin_dashboard.php");
        exit();
    }

    $error = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (!empty($username) && !empty($password)) {
            $query = "SELECT id, password FROM admin_users WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $hashed_password);
                $stmt->fetch();
                
                if (password_verify($password, $hashed_password)) {
                    $_SESSION["admin_id"] = $id;
                    $_SESSION["admin_username"] = $username;
                    $_SESSION["admin_logged_in"] = true;
                    
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $error = "❌ Invalid username or password!";
                }
            } else {
                $error = "❌ Invalid username or password!";
            }
            $stmt->close();
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
        <title>Admin Login - PHP HSN Code Manager</title>
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

        <!-- Login Form -->
        <div class="container login-container">
            <div class="login-box">
                <h2 class="text-center">Admin Login</h2>
                <?php if ($error): ?>
                    <p class="text-danger text-center"><?= $error ?></p>
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
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3">Don't have an account? <a href="admin_register.php">Register here</a></p>
            </div>
        </div>
    </body>
</html>