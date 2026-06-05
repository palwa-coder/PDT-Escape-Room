<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$rooms = [
    ['id' => 'aquatic', 'title' => 'Aquatic Theme', 'img' => 'img/aquatic.png', 'desc' => 'Jelajahi labirin bawah air bertekanan tinggi dan kapal karam legendaris.'],
    ['id' => 'space', 'title' => 'Space Theme', 'img' => 'img/space.png', 'desc' => 'Selesaikan malfungsi kokpit pesawat sebelum pasokan oksigen Anda habis.'],
    ['id' => 'farmland', 'title' => 'Farmland Theme', 'img' => 'img/farmland.png', 'desc' => 'Ungkap konspirasi aneh di dalam lumbung tua milik petani misterius.'],
    ['id' => 'horror', 'title' => 'Horror Theme', 'img' => 'img/horror.png', 'desc' => 'Uji nyali dan logika Anda di dalam ruangan gelap penuh kejutan mistis.'],
    ['id' => 'cyberpunk', 'title' => 'Cyberpunk Theme', 'img' => 'img/cyberpunk.png', 'desc' => 'Retas gerbang utama server kota distopia di bawah tekanan waktu.'],
    ['id' => 'desert', 'title' => 'Desert Theme', 'img' => 'img/desert.png', 'desc' => 'Cari jalan keluar dari jebakan makam kuno firaun di tengah gurun.'],
    ['id' => 'steampunk', 'title' => 'Steampunk Theme', 'img' => 'img/steampunk.png', 'desc' => 'Sinkronisasikan pipa uap dan roda gigi raksasa untuk membuka pintu segel.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Escape Room Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">ESCAPE ROOM</div>
        <div>
            <span style="margin-right: 15px; font-weight: 700;">Halo, <?= htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" style="color: #e63946; text-decoration: none;">LOGOUT</a>
        </div>
    </div>

    <div class="container">
        <h2 style="text-align: left; margin-bottom: 10px;">PILIH TEMA RUANGAN</h2>
        <p style="margin-bottom: 30px; color: #666;">Silakan pilih salah satu dari 7 tema petualangan interaktif terbaik kami.</p>
        
        <div class="grid-rooms">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <img src="<?= $room['img']; ?>" alt="<?= $room['title']; ?>" class="room-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="room-img-placeholder" style="display: none;">
                        <?= $room['title']; ?>
                    </div>
                    <div class="room-info">
                        <div>
                            <h3 class="room-title"><?= $room['title']; ?></h3>
                            <p class="room-desc"><?= $room['desc']; ?></p>
                        </div>
                        <a href="room-detail.php?theme=<?= $room['id']; ?>" class="btn btn-sm">Pesan Ruangan</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
