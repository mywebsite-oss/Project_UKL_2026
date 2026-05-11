<?php
session_start();
include '../includes/koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];
$msg = "";

if (isset($_GET['jalan'])) {
    $m_res = mysqli_query($conn, "SELECT * FROM family_members WHERE user_id=$uid");
    $t_res = mysqli_query($conn, "SELECT * FROM task_pool WHERE user_id=$uid");
    $members = [];
    while ($r = mysqli_fetch_assoc($m_res)) $members[] = $r;
    $tasks = [];
    while ($r = mysqli_fetch_assoc($t_res)) $tasks[] = $r;

    if (count($members) > 0 && count($tasks) > 0) {
        for ($i = 0; $i < 7; $i++) {
            $tgl = date('Y-m-d', strtotime("+$i days"));
            mysqli_query($conn, "DELETE FROM assigned_tasks WHERE user_id=$uid AND tanggal='$tgl' AND is_random=1");
        }
        $idx = 0;
        for ($day = 0; $day < 7; $day++) {
            $tgl = date('Y-m-d', strtotime("+$day days"));
            foreach ($tasks as $t) {
                $member = $members[$idx % count($members)];
                mysqli_query($conn, "INSERT INTO assigned_tasks (user_id, assigned_to, nama_tugas, tanggal, is_random) VALUES ($uid, '{$member['nama']}', '{$t['nama_tugas']}', '$tgl', 1)");
                $idx++;
            }
        }
        $msg = "✅ Berhasil! Cek di 'Tugas Terjadwal'.";
    } else {
        $msg = "❌ Anggota atau Pool tugas masih kosong!";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pengacak Tugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="dashboardd.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboardd.php">Dasbor</a>
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
            <a href="tasks.php" style="padding:10px 20px; text-decoration:none; color:#666; font-weight:bold;">Semua Tugas</a>
            <a href="tasks_anggota.php" style="padding:10px 20px; text-decoration:none; color:#2563eb; font-weight:bold; border-bottom:2px solid #2563eb;">Pembagian Tugas</a>
        </div>
        <div style="margin-bottom:20px;">
            <a href="tasks_anggota.php" style="padding:5px 10px; text-decoration:none; color:#666;">Anggota Keluarga</a>
            <a href="tasks_pool.php" style="padding:5px 10px; text-decoration:none; color:#666;">Pool Tugas</a>
            <a href="tasks_acak.php" style="padding:5px 10px; text-decoration:none; color:#2563eb; font-weight:bold; border-bottom:2px solid #2563eb;">Pengacak</a>
        </div>

        <div style="background:#dcfce7; padding:40px; text-align:center; border-radius:8px; border:1px solid #86efac;">
            <h3>Acak Tugas untuk 7 Hari</h3>
            <p>Sistem akan membagi tugas dari Pool secara adil ke semua anggota keluarga.</p>
            <?php if ($msg) echo "<p style='font-weight:bold; margin:15px 0;'>$msg</p>"; ?>
            <br>
            <a href="?jalan" class="btn btn-primary" style="font-size:18px; padding:15px 40px;">ACAK SEKARANG</a>
        </div>
    </div>
</body>

</html>