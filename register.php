<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "db_escape_room";

    $conn = mysqli_connect($db_host, $db_user, $db_pass);
    if ($conn) {
        mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db_name");
        mysqli_select_db($conn, $db_name);
        
        $table_query = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($conn, $table_query);

        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Konfirmasi password tidak cocok!";
        } else {
            $check_query = "SELECT * FROM users WHERE username = '$username'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Username sudah digunakan!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
                
                if (mysqli_query($conn, $insert_query)) {
                    $success = "Registrasi berhasil! Silakan login.";
                } else {
                    $error = "Gagal mendaftarkan akun.";
                }
            }
        }
        mysqli_close($conn);
    } else {
        $error = "Koneksi database Laragon gagal.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Escape Room</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-box">
        <h2>REGISTER</h2>
        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert" style="background-color: #d4edda; color: #155724;"><?= htmlspecialchars($success); ?></div>
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
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="register" class="btn">Daftar</button>
        </form>
        
        <div class="text-center">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>
