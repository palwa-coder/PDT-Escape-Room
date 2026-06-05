<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['latest_booking'])) {
    header("Location: dashboard.php");
    exit;
}

$booking = $_SESSION['latest_booking'];

$rooms = [
    'aquatic' => 'Aquatic Theme',
    'space' => 'Space Theme',
    'farmland' => 'Farmland Theme',
    'horror' => 'Horror Theme',
    'cyberpunk' => 'Cyberpunk Theme',
    'desert' => 'Desert Theme',
    'steampunk' => 'Steampunk Theme',
];
$room_title = isset($rooms[$booking['theme']]) ? $rooms[$booking['theme']] : $booking['theme'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Berhasil - Escape Room</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-box" style="max-width: 500px; text-align: center; border-color: #2b2b2b; background: #ffffff;">
        <div style="font-size: 50px; margin-bottom: 10px;"></div>
        <h2>PEMESANAN BERHASIL!</h2>
        <p style="color: #666; font-size: 14px; margin-bottom: 25px;">Tunjukkan kode berikut kepada petugas di lokasi Escape Room.</p>
        
        <div style="background: #fffdf9; border: 3px dashed #2b2b2b; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
            <span style="display: block; font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Kode Tiket Anda</span>
            <span style="display: block; font-size: 32px; font-weight: 900; color: #2b2b2b; letter-spacing: 2px; font-family: monospace;"><?= htmlspecialchars($booking['booking_code']); ?></span>
        </div>

        <div style="text-align: left; background: #fdf8f5; border: 3px solid #2b2b2b; border-radius: 10px; padding: 20px; margin-bottom: 30px; font-size: 14px;">
            <div style="margin-bottom: 10px;">
                <strong style="color: #666;">Tema:</strong> <?= htmlspecialchars($room_title); ?>
            </div>
            <div style="margin-bottom: 10px;">
                <strong style="color: #666;">Jam:</strong> <?= htmlspecialchars($booking['booking_time']); ?> WIB
            </div>
            <div>
                <strong style="color: #666;">Paket:</strong> <?= htmlspecialchars($booking['package']); ?> Package
            </div>
        </div>

        <a href="dashboard.php" class="btn" style="background-color: #ffcad4; text-decoration: none;">Kembali ke Dashboard</a>
    </div>
</body>
</html>
