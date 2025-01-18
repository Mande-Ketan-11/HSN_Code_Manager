<?php
    require 'db_connection.php';

    if (isset($_GET['id'])) {
        $fileId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM file_data WHERE file_id = :id");
        $stmt->execute(['id' => $fileId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }
?>