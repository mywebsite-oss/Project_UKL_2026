<?php
// $host = "10.0.2.2"; // Host (Windows) kalau mau online
$host = "localhost"; 
$user = "root";
$pass = "root";
$db = "smart_home";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
