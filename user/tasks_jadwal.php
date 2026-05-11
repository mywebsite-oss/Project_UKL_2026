<?php
session_start();
include '../includes/koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];

// Logika Approval Admin Keluarga
if (isset($_GET['approve'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Disetujui', status='Selesai' WHERE id=".$_GET['approve']);
    header("Location: tasks_jadwal.php");
}
if (isset($_GET['reject'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Belum Selesai' WHERE id=".$_GET['reject']);
    header("Location: tasks_jadwal.php");
}
$data = mysqli_query($conn, "SELECT * FROM assigned_tasks WHERE user_id=$uid ORDER BY tanggal ASC");

?>
<!DOCTYPE html>
<html>

<head>
    <title>Tugas Terjadwal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="dashboardd.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php">Dasbor</a>
            <a href="items.php">Barang</a>
            <a href="tasks.php" class="active">Tugas</a>
        </div>
        <div class="navbar-user">
            <span>Halo, <?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <a href="../logout.php">Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <h2>Tugas</h2>
        <!-- MENU -->
        <div style="border-bottom:2px solid #eee; margin-bottom:15px;">
            <a href="tasks.php" style="padding:10px 20px; text-decoration:none; color:#2563eb; font-weight:bold; border-bottom:2px solid #2563eb;">Semua Tugas</a>
            <a href="tasks_anggota.php" style="padding:10px 20px; text-decoration:none; color:#666; font-weight:bold;">Pembagian Tugas</a>
        </div>
        <div style="margin-bottom:20px;">
            <a href="tasks.php" style="padding:5px 10px; text-decoration:none; color:#666;">Tugas Manual</a>
            <a href="tasks_jadwal.php" style="padding:5px 10px; text-decoration:none; color:#2563eb; font-weight:bold; border-bottom:2px solid #2563eb;">Tugas Terjadwal</a>
        </div>

        <table>
            <tr>
                <th>Tanggal</th>
                <th>Tugas</th>
                <th>Ditugaskan Ke</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <?php 
                $approval = $row['approval_status'] ?? 'Belum Selesai';
                $status = $row['status'] ?? 'Pending';
                ?>
                <tr>
                    <td><?= $row['tanggal']; ?></td>
                    <td><?= $row['nama_tugas']; ?></td>
                    <td><?= $row['assigned_to']; ?></td>
                    <td style="color:<?= $status == 'Selesai' ? 'green' : 'red'; ?>"><?= $status; ?></td>
                    <td>
                        <?php if ($approval == 'Menunggu Persetujuan'): ?>
                            <a href="?approve=<?= $row['id']; ?>" class="btn btn-secondary" style="background:#28a745;">Setujui</a>
                            <a href="?reject=<?= $row['id']; ?>" class="btn btn-danger">Tolak</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>