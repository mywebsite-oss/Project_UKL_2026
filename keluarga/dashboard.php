<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id']; 
$member_id = $_SESSION['member_id'];
$nama = $_SESSION['nama'];

$tasks = mysqli_query($conn, "SELECT * FROM assigned_tasks WHERE assigned_to='$nama' AND user_id=$admin_id ORDER BY tanggal ASC");

if (isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Menunggu Persetujuan' WHERE id=$id AND assigned_to='$nama'");
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Keluarga</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="dashboard.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-user">
            <span><?= $nama; ?></span>
            <a href="../logout.php">Keluar</a>
        </div>
    </div>
    <div class="container">
        <h2>Tugasku</h2>
        <table>
            <tr>
                <th>Tanggal</th>
                <th>Tugas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($tasks)):
                $status = $row['approval_status'] ?? 'Belum Selesai';
            ?>
                <tr>
                    <td><?= $row['tanggal']; ?></td>
                    <td><?= $row['nama_tugas']; ?></td>
                    <td>
                        <?php
                        if ($status == 'Disetujui') echo "<span style='color:green'>✅ Selesai</span>";
                        elseif ($status == 'Menunggu Persetujuan') echo "<span style='color:orange'>⏳ Menunggu</span>";
                        else echo "<span style='color:red'>❌ Belum</span>";
                        ?>
                    </td>
                    <td>
                        <?php if ($status == 'Belum Selesai'): ?>
                            <a href="?selesai=<?= $row['id']; ?>" class="btn btn-primary">Selesai</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>