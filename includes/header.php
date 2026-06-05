<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escape Room Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">🔑 ESCAPE ROOM</div>
        <div>
            <?php if (isset($_SESSION['username'])): ?>
                <span style="margin-right: 15px; font-weight: 700;">Halo, <?= htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" style="color: #e63946; text-decoration: none;">LOGOUT</a>
            <?php else: ?>
                <a href="login.php" style="text-decoration: none; font-weight: 700; margin-right: 15px;">LOGIN</a>
                <a href="register.php" style="text-decoration: none; font-weight: 700;">REGISTER</a>
            <?php endif; ?>
        </div>
    </div>