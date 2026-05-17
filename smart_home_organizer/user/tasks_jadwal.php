<?php
session_start();
include '../includes/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];

if (isset($_GET['approve'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Disetujui', status='Selesai' WHERE id=".$_GET['approve']);
    header("Location: tasks_jadwal.php");
}
if (isset($_GET['reject'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Belum Selesai' WHERE id=".$_GET['reject']);
    header("Location: tasks_jadwal.php");
}
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM assigned_tasks WHERE id=" . $_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks_jadwal.php");
    exit();
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

        <div class="nav-tabs">
            <a href="tasks.php" class="nav-tab active">Semua Tugas</a>
            <a href="tasks_anggota.php" class="nav-tab">Pembagian Tugas</a>
        </div>
        <div class="sub-tabs">
            <a href="tasks.php" class="sub-tab">Tugas Manual</a>
            <a href="tasks_jadwal.php" class="sub-tab active">Tugas Terjadwal</a>
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
                        <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>