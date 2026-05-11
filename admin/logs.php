<?php
session_start();
include '../includes/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");

// Tampilkan Log
$logs = mysqli_query($conn, "SELECT al.*, u.nama_lengkap FROM activity_logs al LEFT JOIN users u ON al.user_id=u.id ORDER BY al.created_at DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Activity Logs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href='dashboard.php'>Admin Panel</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php">Dasbor</a>
            <a href="logs.php" class="active">History</a>
        </div>
        <div class="navbar-user">
            <span><?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <a href="../logout.php">Keluar</a>
        </div>
    </div>
    <div class="container">
        <h2>History Login & Logout</h2>
        <table>
            <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Aktivitas</th>
                <th>Keterangan</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($logs)): ?>
                <tr>
                    <td><?= $row['created_at']; ?></td>
                    <td><?= $row['nama_lengkap']; ?></td>
                    <td><?= $row['activity_type']; ?></td>
                    <td><?= $row['description']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>