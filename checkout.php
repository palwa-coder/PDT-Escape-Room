<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "db_escape_room";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    exit("Koneksi database gagal.");
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    theme VARCHAR(50) NOT NULL,
    booking_time TIME NOT NULL,
    package VARCHAR(50) NOT NULL,
    booking_code VARCHAR(8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$error = "";
$theme = "";
$booking_time = "";
$package = "";

if (isset($_POST['review']) || isset($_POST['pay'])) {
    $theme = mysqli_real_escape_string($conn, $_POST['theme']);
    $booking_time = mysqli_real_escape_string($conn, $_POST['booking_time']);
    $package = mysqli_real_escape_string($conn, $_POST['package']);
} else {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['pay'])) {
    $user_id = $_SESSION['user_id'];
    
    mysqli_begin_transaction($conn);
    
    $lock_query = "SELECT id FROM bookings WHERE theme = '$theme' AND booking_time = '$booking_time' FOR UPDATE";
    $lock_result = mysqli_query($conn, $lock_query);
    
    if (mysqli_num_rows($lock_result) > 0) {
        mysqli_rollback($conn);
        $error = "Slot waktu untuk tema ini sudah terpesan! Silakan pilih jam lain.";
    } else {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $booking_code = "";
        for ($i = 0; $i < 8; $i++) {
            $booking_code .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        $insert_query = "INSERT INTO bookings (user_id, theme, booking_time, package, booking_code) VALUES ('$user_id', '$theme', '$booking_time', '$package', '$booking_code')";
        $insert_result = mysqli_query($conn, $insert_query);
        
        if ($insert_result) {
            mysqli_commit($conn);
            $_SESSION['latest_booking'] = [
                'theme' => $theme,
                'booking_time' => $booking_time,
                'package' => $package,
                'booking_code' => $booking_code
            ];
            mysqli_close($conn);
            header("Location: success.php");
            exit;
        } else {
            mysqli_rollback($conn);
            $error = "Terjadi kesalahan sistem saat memproses pembayaran.";
        }
    }
}

$rooms = [
    'aquatic' => 'Aquatic Theme',
    'space' => 'Space Theme',
    'farmland' => 'Farmland Theme',
    'horror' => 'Horror Theme',
    'cyberpunk' => 'Cyberpunk Theme',
    'desert' => 'Desert Theme',
    'steampunk' => 'Steampunk Theme',
];
$room_title = isset($rooms[$theme]) ? $rooms[$theme] : $theme;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Escape Room</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">🔑 ESCAPE ROOM</div>
        <div>
            <a href="dashboard.php" style="text-decoration: none; font-weight: 700;">BATAL</a>
        </div>
    </div>

    <div class="auth-box" style="max-width: 500px; margin: 20px auto;">
        <h2>KONFIRMASI PEMBAYARAN</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div style="background: #fffdf9; border: 3px solid #2b2b2b; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
            <div style="margin-bottom: 12px; font-size: 14px;">
                <strong style="display:block; text-transform:uppercase; color:#666; font-size:11px; letter-spacing:0.5px;">Tema Ruangan</strong>
                <span style="font-size:18px; font-weight:800; color:#2b2b2b;"><?= htmlspecialchars($room_title); ?></span>
            </div>
            <div style="margin-bottom: 12px; font-size: 14px;">
                <strong style="display:block; text-transform:uppercase; color:#666; font-size:11px; letter-spacing:0.5px;">Jam Kedatangan</strong>
                <span style="font-size:16px; font-weight:700; color:#2b2b2b;"><?= htmlspecialchars($booking_time); ?> WIB</span>
            </div>
            <div style="margin-bottom: 5px; font-size: 14px;">
                <strong style="display:block; text-transform:uppercase; color:#666; font-size:11px; letter-spacing:0.5px;">Paket Pilihan</strong>
                <span style="font-size:16px; font-weight:700; color:#2b2b2b;"><?= htmlspecialchars($package); ?> Package</span>
            </div>
        </div>

        <form action="" method="POST">
            <input type="hidden" name="theme" value="<?= htmlspecialchars($theme); ?>">
            <input type="hidden" name="booking_time" value="<?= htmlspecialchars($booking_time); ?>">
            <input type="hidden" name="package" value="<?= htmlspecialchars($package); ?>">
            <button type="submit" name="pay" class="btn" style="background-color: #ffcad4;">Bayar Sekarang</button>
        </form>
    </div>
</body>
</html>
