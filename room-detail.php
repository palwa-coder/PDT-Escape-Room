<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$theme = isset($_GET['theme']) ? $_GET['theme'] : '';

$rooms = [
    'aquatic' => ['title' => 'Aquatic Theme', 'desc' => 'Jelajahi labirin bawah air bertekanan tinggi dan kapal karam legendaris.'],
    'space' => ['title' => 'Space Theme', 'desc' => 'Selesaikan malfungsi kokpit pesawat sebelum pasokan oksigen Anda habis.'],
    'farmland' => ['title' => 'Farmland Theme', 'desc' => 'Ungkap konspirasi aneh di dalam lumbung tua milik petani misterius.'],
    'horror' => ['title' => 'Horror Theme', 'desc' => 'Uji nyali dan logika Anda di dalam ruangan gelap penuh kejutan mistis.'],
    'cyberpunk' => ['title' => 'Cyberpunk Theme', 'desc' => 'Retas gerbang utama server kota distopia di bawah tekanan waktu.'],
    'desert' => ['title' => 'Desert Theme', 'desc' => 'Cari jalan keluar dari jebakan makam kuno firaun di tengah gurun.'],
    'steampunk' => ['title' => 'Steampunk Theme', 'desc' => 'Sinkronisasikan pipa uap dan roda gigi raksasa untuk membuka pintu segel.'],
];

if (!array_key_exists($theme, $rooms)) {
    header("Location: dashboard.php");
    exit;
}

$room = $rooms[$theme];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ruangan - Escape Room</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">ESCAPE ROOM</div>
        <div>
            <a href="dashboard.php" style="margin-right: 15px; text-decoration: none; font-weight: 700;">KEMBALI</a>
            <a href="logout.php" style="color: #e63946; text-decoration: none; font-weight: 700;">LOGOUT</a>
        </div>
    </div>

    <div class="auth-box" style="max-width: 600px; margin: 20px auto;">
        <h2><?= htmlspecialchars($room['title']); ?></h2>
        <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px;"><?= htmlspecialchars($room['desc']); ?></p>
        
        <form action="checkout.php" method="POST">
            <input type="hidden" name="theme" value="<?= htmlspecialchars($theme); ?>">
            
            <div class="form-group">
                <label for="booking_time">Pilih Jam Kedatangan (Hari Ini)</label>
                <input type="time" name="booking_time" id="booking_time" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label style="margin-bottom: 12px;">Pilih Paket Barang</label>
                
                <label style="display: flex; align-items: center; background: #fff; border: 3px solid #2b2b2b; border-radius: 10px; padding: 12px; margin-bottom: 10px; cursor: pointer; font-weight: normal; text-transform: none;">
                    <input type="radio" name="package" value="Basic" checked style="margin-right: 12px; transform: scale(1.2);">
                    <div>
                        <strong style="display: block; font-size: 15px;">Basic Package</strong>
                        <span style="font-size: 13px; color: #555;">20 breakable items</span>
                    </div>
                </label>

                <label style="display: flex; align-items: center; background: #fff; border: 3px solid #2b2b2b; border-radius: 10px; padding: 12px; margin-bottom: 10px; cursor: pointer; font-weight: normal; text-transform: none;">
                    <input type="radio" name="package" value="Plus" style="margin-right: 12px; transform: scale(1.2);">
                    <div>
                        <strong style="display: block; font-size: 15px;">Plus Package</strong>
                        <span style="font-size: 13px; color: #555;">40 breakable items</span>
                    </div>
                </label>

                <label style="display: flex; align-items: center; background: #fff; border: 3px solid #2b2b2b; border-radius: 10px; padding: 12px; margin-bottom: 10px; cursor: pointer; font-weight: normal; text-transform: none;">
                    <input type="radio" name="package" value="Super Fun" style="margin-right: 12px; transform: scale(1.2);">
                    <div>
                        <strong style="display: block; font-size: 15px;">Super Fun Package</strong>
                        <span style="font-size: 13px; color: #555;">70 breakable items</span>
                    </div>
                </label>

                <label style="display: flex; align-items: center; background: #fff; border: 3px solid #2b2b2b; border-radius: 10px; padding: 12px; margin-bottom: 20px; cursor: pointer; font-weight: normal; text-transform: none;">
                    <input type="radio" name="package" value="Ultra Fun" style="margin-right: 12px; transform: scale(1.2);">
                    <div>
                        <strong style="display: block; font-size: 15px;">Ultra Fun Package</strong>
                        <span style="font-size: 13px; color: #555;">100 breakable items</span>
                    </div>
                </label>
            </div>
            
            <button type="submit" name="review" class="btn">Lanjut ke Pembayaran</button>
        </form>
    </div>
</body>
</html>
