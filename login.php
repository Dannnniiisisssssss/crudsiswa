<?php

session_start();

require "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, username, password FROM users WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if ($password === $user["password"]) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

            header("Location: daniswara.php");
            exit;

        } else {
            $error = "Password salah.";
        }

    } else {
        $error = "Username tidak ditemukan.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login — Daniswara</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<main>

    <div class="form-container">

        <h2>Daniswara.</h2>

        <p style="color:#a89880; margin-bottom:25px;">
            Login untuk mengakses Task Organizer
        </p>

        <?php if ($error != ""): ?>

            <p style="color:#c97070; margin-bottom:15px;">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label>Username</label>

            <input
                type="text"
                name="username"
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                required
            >

            <div class="form-buttons">

                <button type="submit" class="btn">
                    Login
                </button>

            </div>

        </form>

    </div>

</main>

</body>

</html>
