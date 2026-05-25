<?php
session_start();
include 'includes/koneksi.php';

if (isset($_SESSION['member_id'])) {
    $uid = $_SESSION['user_id'];
    $mem_id = $_SESSION['member_id'];
    $username = $_SESSION['username'];
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, family_id, activity_type, description) VALUES ($uid, $mem_id, 'logout', 'Member $username keluar dari sistem.')");

} elseif (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
    $prefix = ($role == 'user') ? "User " : "";
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, activity_type, description) VALUES ($uid, 'logout', '{$prefix}{$username} keluar dari sistem.')");
}

session_destroy();
header("Location: index.php");
exit();
