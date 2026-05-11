<?php
session_start();
include 'includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, activity_type, description) VALUES ($uid, 'logout', 'User keluar dari sistem')");
}

session_destroy();
header("Location: index.php");
exit();
