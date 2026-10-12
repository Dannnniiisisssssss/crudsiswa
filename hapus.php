<?php

require "koneksi.php";

$id = $_GET['id'] ?? 0;


/* Ambil nama foto */
$stmt = $conn->prepare(
    "SELECT photo FROM tasks WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$task = $result->fetch_assoc();


/* Hapus data dari database */
$stmt = $conn->prepare(
    "DELETE FROM tasks WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();


/* Hapus file foto */
if ($task && !empty($task['photo'])) {

    $file = "uploads/" . $task['photo'];

    if (file_exists($file)) {
        unlink($file);
    }
}


header("Location: daniswara.php");
exit;

?>
