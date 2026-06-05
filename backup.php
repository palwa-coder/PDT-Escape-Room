<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Jakarta');

$date = date('Y-m-d_H-i-s');
$backup_dir = __DIR__ . '/storage/backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}
$backupFile = $backup_dir . "escaperoom_backup_$date.sql";

$mysqldump_path = "C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe";
$db_user = 'root';
$db_pass = '';
$db_name = 'db_escape_room';

$command = "\"$mysqldump_path\" -u $db_user " . ($db_pass ? "-p$db_pass " : "") . "$db_name --result-file=\"$backupFile\" 2>&1";

exec($command, $output, $return_var);

if ($return_var === 0 && filesize($backupFile) > 0) {
    echo "Backup sukses! File disimpan di: " . $backupFile;
} else {
    echo "Backup gagal. Detail Error:\n" . implode("\n", $output);
}
?>