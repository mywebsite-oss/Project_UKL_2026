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
$admin_nama = $_SESSION['nama'];
$msg = "";

if (isset($_GET['jalan'])) {
    $m_res = mysqli_query($conn, "SELECT nama FROM family_members WHERE user_id=$uid");
    $t_res = mysqli_query($conn, "SELECT * FROM task_pool WHERE user_id=$uid");
    
    $members = [];
    while ($r = mysqli_fetch_assoc($m_res)) $members[] = $r['nama'];
    
    // Check if admin wants to be included
    if (isset($_GET['include_admin']) && $_GET['jalan'] == '1') {
        $members[] = $admin_nama;
    }
    
    $tasks = [];
    while ($r = mysqli_fetch_assoc($t_res)) $tasks[] = $r;

    if (count($members) > 0 && count($tasks) > 0) {
        for ($i = 0; $i < 7; $i++) {
            $tgl = date('Y-m-d', strtotime("+$i days"));
            mysqli_query($conn, "DELETE FROM assigned_tasks WHERE user_id=$uid AND tanggal='$tgl' AND is_random=1");
        }
        
        shuffle($members); // Make it truly random as requested earlier
        
        $idx = 0;
        for ($day = 0; $day < 7; $day++) {
            $tgl = date('Y-m-d', strtotime("+$day days"));
            foreach ($tasks as $t) {
                $m_name = $members[$idx % count($members)];
                mysqli_query($conn, "INSERT INTO assigned_tasks (user_id, assigned_to, nama_tugas, tanggal, is_random) VALUES ($uid, '$m_name', '{$t['nama_tugas']}', '$tgl', 1)");
                $idx++;
            }
        }
        $msg = "✅ Berhasil! Cek di 'Tugas Terjadwal'.";
        
        // Trigger Notification for ALL Members
        $notif_title = "Jadwal Tugas Rutin Baru";
        $notif_msg = "Tugas rutin baru untuk 7 hari ke depan telah diacak. Silakan cek jadwal Anda!";
        $m_ids = mysqli_query($conn, "SELECT id FROM family_members WHERE user_id=$uid");
        while($mid_row = mysqli_fetch_assoc($m_ids)) {
            $mid = $mid_row['id'];
            mysqli_query($conn, "INSERT INTO notifications (member_id, title, message) VALUES ($mid, '$notif_title', '$notif_msg')");
        }
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
    <div class="navbar navbar-dashboard">
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
            <?php renderNotifications($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
            <a href="javascript:void(0)" onclick="openAccountDrawer()">Akun</a>
        </div>
    </div>
    <div class="container">
        <h2>Tugas</h2>

        <div class="nav-tabs">
            <a href="tasks.php" class="nav-tab">Semua Tugas</a>
            <a href="tasks_anggota.php" class="nav-tab active">Pembagian Tugas</a>
        </div>
        <div class="sub-tabs">
            <a href="tasks_anggota.php" class="sub-tab">Anggota Keluarga</a>
            <a href="tasks_pool.php" class="sub-tab">Pool Tugas</a>
            <a href="tasks_acak.php" class="sub-tab active">Pengacak</a>
        </div>

        <div class="shuffle-box">
            <h3>Acak Tugas untuk 7 Hari</h3>
            <p>Sistem akan membagi tugas dari Pool secara adil ke semua anggota keluarga.</p>
            
            <form method="GET" style="margin: 20px 0;">
                <input type="hidden" name="jalan" value="1">
                <div style="background: #fff; padding: 15px; border-radius: 8px; display: inline-block; border: 1px solid #ddd;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px; margin: 0;">
                        <input type="checkbox" name="include_admin" value="1" style="width: 20px; height: 20px; margin: 0;">
                        <span>Sertakan Saya (Admin) dalam Pengacakan</span>
                    </label>
                </div>
                <br><br>
                <button type="submit" class="btn btn-primary btn-large">ACAK SEKARANG</button>
            </form>

            <?php if ($msg) echo "<p class='shuffle-msg'>$msg</p>"; ?>
        </div>
    </div>
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>