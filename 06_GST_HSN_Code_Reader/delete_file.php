<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete the specific row from the file_data table
    $stmt = $pdo->prepare("DELETE FROM file_data WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo "Data row deleted successfully!";
} else {
    echo "Invalid request.";
}
?>