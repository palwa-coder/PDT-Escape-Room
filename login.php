<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "db_escape_room";

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        $error = "Koneksi database Laragon gagal.";
    } else {
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Escape Room</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-box">
        <h2>LOG IN</h2>
        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn">Masuk</button>
        </form>
        
        <div class="text-center">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </div>
    </div>
</body>
</html>
