<?php
require 'db_connection.php';

// Check if a specific file ID is provided for filtering
$fileId = $_GET['file_id'] ?? null;

if ($fileId) {
    // Fetch data for the specified file
    $stmt = $pdo->prepare("SELECT * FROM file_data WHERE file_id = :file_id");
    $stmt->execute(['file_id' => $fileId]);
} else {
    // Fetch all data
    $stmt = $pdo->query("SELECT * FROM file_data");
}

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>