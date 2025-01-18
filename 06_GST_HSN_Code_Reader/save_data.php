<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; // Row ID (if editing)
    $hsnCode = $_POST['hsn_code']; // HSN Code
    $description = $_POST['description']; // Description
    $taxRate = $_POST['tax_rate']; // Tax Rate

    if ($id) {
        // Update existing data
        $stmt = $pdo->prepare("UPDATE file_data 
                               SET hsn_code = :hsn_code, description = :description, tax_rate = :tax_rate 
                               WHERE id = :id");
        $stmt->execute([
            'hsn_code' => $hsnCode,
            'description' => $description,
            'tax_rate' => $taxRate,
            'id' => $id
        ]);
        echo "Data updated successfully!";
    } else {
        // Add new data (requires file_id)
        $fileId = $_POST['file_id'];
        $stmt = $pdo->prepare("INSERT INTO file_data (file_id, hsn_code, description, tax_rate) 
                               VALUES (:file_id, :hsn_code, :description, :tax_rate)");
        $stmt->execute([
            'file_id' => $fileId,
            'hsn_code' => $hsnCode,
            'description' => $description,
            'tax_rate' => $taxRate
        ]);
        echo "Data added successfully!";
    }
} else {
    echo "Invalid request.";
}
?>