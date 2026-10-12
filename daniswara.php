<?php
require "koneksi.php";

$search = $_GET['search'] ?? '';

if ($search != '') {
    $stmt = $conn->prepare(
        "SELECT * FROM tasks
         WHERE title LIKE ? OR description LIKE ?
         ORDER BY id DESC"
    );

    $keyword = "%" . $search . "%";
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();

    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
}

$total = $conn->query(
    "SELECT COUNT(*) AS jumlah FROM tasks"
)->fetch_assoc()['jumlah'];

$progress = $conn->query(
    "SELECT COUNT(*) AS jumlah FROM tasks WHERE status='progress'"
)->fetch_assoc()['jumlah'];

$completed = $conn->query(
    "SELECT COUNT(*) AS jumlah FROM tasks WHERE status='completed'"
)->fetch_assoc()['jumlah'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daniswara — Task Organizer</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>

    <div>
        <h1>Daniswara.</h1>
        <p>Visual Task Organizer</p>
    </div>

    <a href="tambah.php" class="btn">
        + Tambah Tugas
    </a>

</header>

<main>

<section class="hero">

    <small>PRODUCTIVITY HUB</small>

    <h2>
        Atur <span>inspirasi</span>
        dan rencanamu.
    </h2>

    <p>
        Kelola semua tugas dengan lebih teratur.
    </p>

</section>


<section class="stats">

    <div class="stat">
        <strong><?= $total ?></strong>
        <span>Total Tugas</span>
    </div>

    <div class="stat">
        <strong><?= $progress ?></strong>
        <span>Proses</span>
    </div>

    <div class="stat">
        <strong><?= $completed ?></strong>
        <span>Selesai</span>
    </div>

</section>


<form class="search" method="GET">

    <input
        type="text"
        name="search"
        placeholder="Cari tugas..."
        value="<?= htmlspecialchars($search) ?>"
    >

    <button type="submit">
        Cari
    </button>

</form>


<section class="grid">

<?php if ($result->num_rows == 0): ?>

    <div class="empty">
        Belum ada tugas.
    </div>

<?php endif; ?>


<?php while ($task = $result->fetch_assoc()): ?>

<article class="card">

    <div class="priority <?= $task['priority'] ?>"></div>


    <?php if (!empty($task['photo'])): ?>

        <img
            src="uploads/<?= htmlspecialchars($task['photo']) ?>"
            class="task-photo"
            alt="Foto tugas"
        >

    <?php endif; ?>


    <div class="card-body">

        <span class="tag">
            <?= htmlspecialchars($task['category']) ?>
        </span>

        <h3 class="<?= $task['status'] == 'completed' ? 'done' : '' ?>">
            <?= htmlspecialchars($task['title']) ?>
        </h3>

        <p>
            <?= htmlspecialchars(
                $task['description'] ?: 'Tanpa deskripsi.'
            ) ?>
        </p>

    </div>


    <div class="footer">

        <small>
            <?= date('d M Y', strtotime($task['created_at'])) ?>
        </small>


        <div class="actions">

            <?php if ($task['status'] == 'progress'): ?>

                <a
                    href="selesai.php?id=<?= $task['id'] ?>"
                    title="Tandai selesai"
                >
                    ✓
                </a>

            <?php else: ?>

                <a
                    href="selesai.php?id=<?= $task['id'] ?>"
                    title="Kembalikan ke proses"
                >
                    ↺
                </a>

            <?php endif; ?>


            <a
                href="edit.php?id=<?= $task['id'] ?>"
                title="Edit"
            >
                ✎
            </a>


            <?php if (!empty($task['photo'])): ?>

                <a
                    href="download.php?id=<?= $task['id'] ?>"
                    title="Download foto"
                >
                    ↓
                </a>

            <?php endif; ?>


            <a
                href="hapus.php?id=<?= $task['id'] ?>"
                title="Hapus"
                onclick="return confirm('Yakin ingin menghapus tugas ini?')"
            >
                ✕
            </a>

        </div>

    </div>

</article>

<?php endwhile; ?>

</section>

</main>

</body>
</html>
