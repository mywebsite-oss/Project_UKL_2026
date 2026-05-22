<?php
session_start();
include '../includes/koneksi.php';
include_once '../includes/notifications.php';
include_once '../includes/account_drawer.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];
$jml_item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as c FROM items WHERE user_id=$uid"))['c'];
$jml_tugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as c FROM tasks WHERE user_id=$uid AND status='Pending'"))['c'];
$jml_tugas_mem = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as c FROM assigned_tasks WHERE user_id=$uid AND status='Pending'"))['c'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>dashboardd User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar navbar-dashboard">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="dashboardd.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboardd.php" class="active">Dasbor</a>
            <a href="items.php">Barang</a>
            <a href="tasks.php">Tugas</a>
        </div>
        <div class="navbar-user">
            <span>Halo, <?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <?php renderNotifications($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
            <a href="javascript:void(0)" onclick="openAccountDrawer()">Akun</a>
        </div>
    </div>
    <div class="container">
        <h2>Dashboard</h2>
        <div class="dashboard-cards">
            <div class="card card-blue">
                <h3><?php echo $jml_item; ?></h3>
                <p>Total Barang</p><a href="items.php" class="btn btn-primary">Kelola</a>
            </div>
            <div class="card card-yellow">
                <h3><?php echo $jml_tugas; ?></h3>
                <p>Tugas Pending<br>(Pribadi)</p><a href="tasks.php" class="btn btn-primary">Kelola</a>
            </div>
            <div class="card card-green">
                <h3><?php echo $jml_tugas_mem; ?></h3>
                <p>Tugas Pending<br>(Terjadwal)</p><a href="tasks_jadwal.php" class="btn btn-primary">Kelola</a>
            </div>
        </div>
        <h3>Aktivitas Terbaru</h3>
        <table>
            <tr>
                <th>Aktivitas</th>
                <th>Waktu</th>
            </tr>
            <?php
            $logs = mysqli_query($conn, "SELECT * FROM activity_logs WHERE user_id=$uid ORDER BY created_at DESC LIMIT 5");
            while ($row = mysqli_fetch_assoc($logs)): ?>
                <tr>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo date('d M H:i', strtotime($row['created_at'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>