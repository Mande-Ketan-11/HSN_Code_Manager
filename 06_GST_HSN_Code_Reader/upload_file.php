<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = uniqid() . "_" . $file['name']; // Unique filename
    $filepath = 'uploads/' . $filename; // Path to store file

    // Move the uploaded file to the server
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Save file details into the file_attributes table
        $stmt = $pdo->prepare("INSERT INTO file_attributes (file_name, file_path) VALUES (:file_name, :file_path)");
        $stmt->execute(['file_name' => $file['name'], 'file_path' => $filepath]);
        $fileId = $pdo->lastInsertId(); // Get the ID of the inserted file

        // Read the file content and parse it as CSV
        if (($handle = fopen($filepath, "r")) !== false) {
            $headers = fgetcsv($handle); // Skip the first row (column headers)

            // Insert data into the file_data table
            while (($row = fgetcsv($handle)) !== false) {
                $hsnCode = $row[0] ?? null; // First column: HSN Code
                $description = $row[1] ?? null; // Second column: Description
                $taxRate = $row[2] ?? null; // Third column: Tax Rate

                $stmt = $pdo->prepare("INSERT INTO file_data (file_id, hsn_code, description, tax_rate) 
                                       VALUES (:file_id, :hsn_code, :description, :tax_rate)");
                $stmt->execute([
                    'file_id' => $fileId,
                    'hsn_code' => $hsnCode,
                    'description' => $description,
                    'tax_rate' => $taxRate
                ]);
            }
            fclose($handle);
        }

        echo "File uploaded and data saved successfully!";
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "No file uploaded.";
}
?>