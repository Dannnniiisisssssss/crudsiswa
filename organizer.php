<?php
require "koneksi.php";

$id = $_GET['id'];

$stmt = $conn->prepare(
    "SELECT * FROM tasks WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    die("Tugas tidak ditemukan.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];

    $photoName = $task['photo'];


    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $extension = strtolower(
            pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed)) {
            die("Format foto tidak diperbolehkan.");
        }

        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            die("Ukuran foto maksimal 5 MB.");
        }


        if (!empty($task['photo']) &&
            file_exists("uploads/" . $task['photo'])) {

            unlink("uploads/" . $task['photo']);
        }


        $photoName = uniqid('foto_', true) . '.' . $extension;

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            "uploads/" . $photoName
        );
    }


    $stmt = $conn->prepare(
        "UPDATE tasks
         SET title=?, description=?, category=?, priority=?, photo=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "sssssi",
        $title,
        $description,
        $category,
        $priority,
        $photoName,
        $id
    );

    $stmt->execute();

    header("Location: daniswara.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Edit Tugas</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<main>

<div class="form-container">

<h2>Edit Tugas</h2>

<form method="POST" enctype="multipart/form-data">

<label>Judul Tugas</label>

<input
    type="text"
    name="title"
    value="<?= htmlspecialchars($task['title']) ?>"
    required
>


<label>Deskripsi</label>

<textarea name="description"><?= htmlspecialchars($task['description']) ?></textarea>


<label>Kategori</label>

<select name="category">

<option value="Work" <?= $task['category']=='Work'?'selected':'' ?>>
Work
</option>

<option value="Personal" <?= $task['category']=='Personal'?'selected':'' ?>>
Personal
</option>

<option value="Creative" <?= $task['category']=='Creative'?'selected':'' ?>>
Creative
</option>

<option value="Idea" <?= $task['category']=='Idea'?'selected':'' ?>>
Idea
</option>

</select>


<label>Prioritas</label>

<select name="priority">

<option value="low" <?= $task['priority']=='low'?'selected':'' ?>>
Low
</option>

<option value="medium" <?= $task['priority']=='medium'?'selected':'' ?>>
Medium
</option>

<option value="high" <?= $task['priority']=='high'?'selected':'' ?>>
Urgent
</option>

</select>


<label>Ganti Foto</label>

<input
    type="file"
    name="photo"
    accept="image/*"
>


<?php if (!empty($task['photo'])): ?>

<p class="current-photo">
    Foto saat ini:
</p>

<img
    src="uploads/<?= htmlspecialchars($task['photo']) ?>"
    class="preview-photo"
    alt="Foto tugas"
>

<?php endif; ?>


<div class="form-buttons">

<a href="daniswara.php" class="btn-cancel">
    Batal
</a>

<button type="submit" class="btn">
    Update
</button>

</div>

</form>

</div>

</main>

</body>

</html>
