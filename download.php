<?php

require "koneksi.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare(
    "SELECT photo FROM tasks WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task || empty($task['photo'])) {
    die("Foto tidak ditemukan.");
}

$file = "uploads/" . $task['photo'];

if (!file_exists($file)) {
    die("File foto tidak ditemukan.");
}

$extension = strtolower(
    pathinfo($file, PATHINFO_EXTENSION)
);

$mimeTypes = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp'
];

$mime = $mimeTypes[$extension] ?? 'application/octet-stream';

header("Content-Type: " . $mime);
header(
    'Content-Disposition: attachment; filename="' .
    basename($task['photo']) . '"'
);
header("Content-Length: " . filesize($file));

readfile($file);
exit;

?>
