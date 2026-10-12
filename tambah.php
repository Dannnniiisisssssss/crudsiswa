<?php
require "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];

    $photoName = NULL;

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

        $photoName = uniqid('foto_', true) . '.' . $extension;

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            "uploads/" . $photoName
        );
    }


    $stmt = $conn->prepare(
        "INSERT INTO tasks
        (title, description, category, priority, photo)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssss",
        $title,
        $description,
        $category,
        $priority,
        $photoName
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

<title>Tambah Tugas</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<main>

<div class="form-container">

<h2>Tambah Tugas Baru</h2>

<form method="POST" enctype="multipart/form-data">

<label>Judul Tugas</label>

<input
    type="text"
    name="title"
    required
    placeholder="Contoh: Praktik Linux"
>


<label>Deskripsi</label>

<textarea
    name="description"
    placeholder="Deskripsi tugas..."
></textarea>


<label>Kategori</label>

<select name="category">

<option value="Work">Work</option>
<option value="Personal">Personal</option>
<option value="Creative">Creative</option>
<option value="Idea">Idea</option>

</select>


<label>Prioritas</label>

<select name="priority">

<option value="low">Low</option>
<option value="medium" selected>Medium</option>
<option value="high">Urgent</option>

</select>


<label>Foto</label>

<input
    type="file"
    name="photo"
    accept="image/*"
>


<div class="form-buttons">

<a href="daniswara.php" class="btn-cancel">
    Batal
</a>

<button type="submit" class="btn">
    Simpan Tugas
</button>

</div>

</form>

</div>

</main>

</body>

</html>
