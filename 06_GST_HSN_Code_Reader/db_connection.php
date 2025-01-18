<?php
    // Database connection and setup
    $host = 'localhost';
    $dbname = 'hsn_project';
    $user = 'root'; // Replace with your MySQL username
    $password = ''; // Replace with your MySQL password

    try {
        // Create connection to MySQL
        $pdo = new PDO("mysql:host=$host", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");

        // Create table for file attributes
        $pdo->exec("CREATE TABLE IF NOT EXISTS file_attributes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            upload_time DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create table for file data
        $pdo->exec("CREATE TABLE IF NOT EXISTS file_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id INT NOT NULL, -- Foreign key linking to file_attributes
            hsn_code VARCHAR(50),
            description TEXT,
            tax_rate DECIMAL(5, 2),
            FOREIGN KEY (file_id) REFERENCES file_attributes(id) ON DELETE CASCADE
        )");

        // Ensure the uploads folder exists
        $uploadFolder = __DIR__ . '/uploads';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0777, true);
        }
    } catch (PDOException $e) {
        die("Database setup failed: " . $e->getMessage());
    }
?>