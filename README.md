# 🔑 Escape Room Booking

Sistem pemesanan sederhana untuk Escape Room, dibangun dengan PHP Native dan MySQL. Platform ini mengelola pemesanan Escape Room sambil mengimplementasikan konsep basis data tingkat lanjut: fragmentasi data, database views, SQL joins, set operations, transactions, deadlock management, functions, triggers, serta backup otomatis dan task scheduler.

---

## 📌 Backend & SQL

Logika pemrosesan kritis dipindahkan langsung ke lapisan database via stored routines. Pendekatan ini menjamin konsistensi data, efisiensi query, dan isolasi konkurensi yang dibutuhkan dalam lingkungan multi-user.

---

### 1. Fragmentasi Horizontal & Router Transaksi

Data pemesanan dipecah secara horizontal ke dua tabel fragmen berdasarkan tema ruangan, menyimulasikan arsitektur data terdistribusi. Pemecahan ini ditangani otomatis oleh stored procedure dengan kendali penuh atas blok transaksi.

```sql
CREATE TABLE bookings_fragment_1 (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT         NOT NULL,
    theme        VARCHAR(50) NOT NULL,
    booking_time TIME        NOT NULL,
    package      VARCHAR(50) NOT NULL,
    booking_code VARCHAR(8)  NOT NULL,
    created_at   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings_fragment_2 (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT         NOT NULL,
    theme        VARCHAR(50) NOT NULL,
    booking_time TIME        NOT NULL,
    package      VARCHAR(50) NOT NULL,
    booking_code VARCHAR(8)  NOT NULL,
    created_at   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
);
```

---

### 2. Transaction (Commit & Rollback) & Deadlock Management

Setiap operasi pemesanan mengunci baris jadwal terkait via `FOR UPDATE` untuk mencegah race condition. Transaksi dibungkus `START TRANSACTION` dan `COMMIT`. Jika terjadi kegagalan, handler `SQLEXCEPTION` memicu `ROLLBACK` secara otomatis. Batas waktu kunci dibatasi 5 detik untuk menghindari deadlock permanen.

```sql
DELIMITER $$

CREATE PROCEDURE process_secure_booking(
    IN  p_user_id        INT,
    IN  p_theme          VARCHAR(50),
    IN  p_time           TIME,
    IN  p_package        VARCHAR(50),
    OUT p_generated_code VARCHAR(8)
)
BEGIN
    DECLARE v_code VARCHAR(8);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SET v_code           = generate_random_code();
    SET p_generated_code = v_code;

    START TRANSACTION;

    IF p_theme IN ('aquatic', 'space', 'cyberpunk', 'steampunk') THEN
        SELECT id FROM bookings_fragment_1
        WHERE theme = p_theme AND booking_time = p_time
        FOR UPDATE;

        INSERT INTO bookings_fragment_1 (user_id, theme, booking_time, package, booking_code)
        VALUES (p_user_id, p_theme, p_time, p_package, v_code);
    ELSE
        SELECT id FROM bookings_fragment_2
        WHERE theme = p_theme AND booking_time = p_time
        FOR UPDATE;

        INSERT INTO bookings_fragment_2 (user_id, theme, booking_time, package, booking_code)
        VALUES (p_user_id, p_theme, p_time, p_package, v_code);
    END IF;

    COMMIT;
END$$

DELIMITER ;
```

---

### 3. Rekonstruksi Data (Set Operations & SQL Joins)

Meski data tersimpan terpisah, database menyatukannya kembali lewat `UNION ALL` ke dalam sebuah view. View tersebut lalu di-join dengan tabel `users` untuk memetakan nama pengguna secara real-time.

```sql
CREATE VIEW view_all_bookings AS
    SELECT id, user_id, theme, booking_time, package, booking_code, created_at
    FROM bookings_fragment_1
    UNION ALL
    SELECT id, user_id, theme, booking_time, package, booking_code, created_at
    FROM bookings_fragment_2;

CREATE VIEW view_booking_details AS
    SELECT b.id, u.username, b.theme, b.booking_time, b.package, b.booking_code, b.created_at
    FROM view_all_bookings b
    INNER JOIN users u ON b.user_id = u.id;
```

---

### 4. Generator Kode Tiket (SQL Function)

Sebelum data ditulis ke tabel, database menghasilkan kode tiket unik 8 karakter alfanumerik secara acak menggunakan `FUNCTION`.

```sql
DELIMITER $$

CREATE FUNCTION generate_random_code()
RETURNS VARCHAR(8)
DETERMINISTIC
BEGIN
    DECLARE chars_str VARCHAR(62);
    DECLARE res_str   VARCHAR(8);
    DECLARE i         INT;

    SET chars_str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    SET res_str   = '';
    SET i         = 1;

    WHILE i <= 8 DO
        SET res_str = CONCAT(res_str, SUBSTRING(chars_str, FLOOR(1 + RAND() * 62), 1));
        SET i       = i + 1;
    END WHILE;

    RETURN res_str;
END$$

DELIMITER ;
```

---

### 5. Log Transaksi Otomatis (SQL Triggers)

Setiap data masuk ke tabel fragmen, trigger `AFTER INSERT` otomatis mencatat aktivitasnya ke tabel audit tanpa perlu kode tambahan di sisi aplikasi PHP.

```sql
DELIMITER $$

CREATE TRIGGER after_fragment1_insert
AFTER INSERT ON bookings_fragment_1
FOR EACH ROW
BEGIN
    INSERT INTO booking_audit (booking_code, action_type)
    VALUES (NEW.booking_code, 'INSERT_FRAGMENT_1');
END$$

DELIMITER ;
```

---

## 💾 Backup Otomatis

Sistem dilengkapi skrip backup menggunakan `mysqldump` yang dijadwalkan lewat task scheduler OS. File backup disimpan di `storage/backups/` dengan nama berbasis timestamp.

Backup bisa dipicu terjadwal maupun manual dengan menjalankan `backup.php`.

---

### 📄 backup.php

```php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Jakarta');

$date       = date('Y-m-d_H-i-s');
$backup_dir = __DIR__ . '/storage/backups/';

if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$backupFile     = $backup_dir . "escaperoom_backup_$date.sql";
$mysqldump_path = "C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe";
$db_user        = 'root';
$db_pass        = '';
$db_name        = 'db_escape_room';

$command = "\"$mysqldump_path\" -u $db_user "
         . ($db_pass ? "-p$db_pass " : "")
         . "$db_name --result-file=\"$backupFile\" 2>&1";

exec($command, $output, $return_var);

if ($return_var === 0 && filesize($backupFile) > 0) {
    echo "Backup sukses! File disimpan di: " . $backupFile;
} else {
    echo "Backup gagal. Detail error:\n" . implode("\n", $output);
}
```

---