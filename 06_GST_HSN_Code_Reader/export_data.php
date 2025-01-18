<?php
require 'db_connection.php';

// Check if a specific file ID is provided for export
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

// Generate CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="hsn_data.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['HSN Code', 'Description', 'Tax Rate']); // Write CSV headers

// Write rows to CSV
foreach ($data as $row) {
    fputcsv($output, [$row['hsn_code'], $row['description'], $row['tax_rate']]);
}

fclose($output);
?>
