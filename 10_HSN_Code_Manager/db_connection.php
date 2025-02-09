<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "hsn_code_db";

    $conn = new mysqli($host, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->query("CREATE DATABASE IF NOT EXISTS $database");
    $conn->select_db($database);

    $conn->query("CREATE TABLE IF NOT EXISTS hsn_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_name VARCHAR(255) UNIQUE NOT NULL,
        hsn_code INT NOT NULL,
        category VARCHAR(100) NOT NULL,
        gst_rate DECIMAL(5,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->set_charset("utf8");
?>