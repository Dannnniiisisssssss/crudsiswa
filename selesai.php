<?php

require "koneksi.php";

$id = $_GET['id'];

$stmt = $conn->prepare(
    "UPDATE tasks
     SET status =
     CASE
        WHEN status = 'progress'
        THEN 'completed'
        ELSE 'progress'
     END
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: daniswara.php");
exit;

?>
