<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "db_escape_room";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    exit("Koneksi database gagal");
}
?>