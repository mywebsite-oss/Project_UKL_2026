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
    $m_res = mysqli_query($conn, "SELECT nama, max_tasks_per_day, max_tasks_per_week FROM family_members WHERE user_id=$uid");
    $members_list = [];
    while ($r = mysqli_fetch_assoc($m_res)) {
        $members_list[] = [
            'nama' => $r['nama'],
            'max_tasks_per_day' => (int)$r['max_tasks_per_day'],
            'max_tasks_per_week' => (int)$r['max_tasks_per_week']
        ];
    }
    
    // Check if admin wants to be included
    if (isset($_GET['include_admin']) && $_GET['jalan'] == '1') {
        $admin_limits_q = mysqli_query($conn, "SELECT max_tasks_per_day, max_tasks_per_week FROM users WHERE id=$uid");
        $admin_limits = mysqli_fetch_assoc($admin_limits_q);
        $members_list[] = [
            'nama' => $admin_nama,
            'max_tasks_per_day' => (int)($admin_limits['max_tasks_per_day'] ?? 2),
            'max_tasks_per_week' => (int)($admin_limits['max_tasks_per_week'] ?? 10)
        ];
    }
    
    $t_res = mysqli_query($conn, "SELECT * FROM task_pool WHERE user_id=$uid");
    $tasks = [];
    while ($r = mysqli_fetch_assoc($t_res)) {
        $tasks[] = $r;
    }

    if (count($members_list) > 0 && count($tasks) > 0) {
        for ($i = 0; $i < 7; $i++) {
            $tgl = date('Y-m-d', strtotime("+$i days"));
            mysqli_query($conn, "DELETE FROM assigned_tasks WHERE user_id=$uid AND tanggal='$tgl' AND is_random=1");
        }
        
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+6 days'));
        
        $weekly_workload = [];
        $daily_manual_workload = [];
        $daily_random_count = [];
        
        foreach ($members_list as $m) {
            $m_name = mysqli_real_escape_string($conn, $m['nama']);
            
            // Baseline weekly workload (from tasks table)
            $q_week = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id=$uid AND assigned_to='$m_name' AND jadwal BETWEEN '$start_date' AND '$end_date' AND status != 'Dibatalkan'");
            $r_week = mysqli_fetch_assoc($q_week);
            $weekly_workload[$m['nama']] = (int)$r_week['total'];
            
            // Baseline daily manual workloads
            $daily_manual_workload[$m['nama']] = [];
            for ($day = 0; $day < 7; $day++) {
                $tgl = date('Y-m-d', strtotime("+$day days"));
                $q_day = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id=$uid AND assigned_to='$m_name' AND jadwal='$tgl' AND status != 'Dibatalkan'");
                $r_day = mysqli_fetch_assoc($q_day);
                $daily_manual_workload[$m['nama']][$tgl] = (int)$r_day['total'];
            }
        }
        
        $skipped_tasks_count = 0;
        
        for ($day = 0; $day < 7; $day++) {
            $tgl = date('Y-m-d', strtotime("+$day days"));
            
            // Shuffle tasks for this day to be fair
            $day_tasks = $tasks;
            shuffle($day_tasks);
            
            foreach ($day_tasks as $t) {
                // Find eligible members
                $eligible = [];
                foreach ($members_list as $m) {
                    $name = $m['nama'];
                    $curr_daily = $daily_manual_workload[$name][$tgl] + ($daily_random_count[$name][$tgl] ?? 0);
                    $curr_weekly = $weekly_workload[$name];
                    
                    if ($curr_daily < $m['max_tasks_per_day'] && $curr_weekly < $m['max_tasks_per_week']) {
                        $eligible[] = [
                            'member' => $m,
                            'curr_daily' => $curr_daily,
                            'curr_weekly' => $curr_weekly
                        ];
                    }
                }
                
                if (!empty($eligible)) {
                    // Sort by daily workload asc, then weekly workload asc, with random tie-breaker
                    usort($eligible, function($a, $b) {
                        if ($a['curr_daily'] === $b['curr_daily']) {
                            if ($a['curr_weekly'] === $b['curr_weekly']) {
                                return rand(-1, 1);
                            }
                            return $a['curr_weekly'] <=> $b['curr_weekly'];
                        }
                        return $a['curr_daily'] <=> $b['curr_daily'];
                    });
                    
                    $chosen = $eligible[0]['member'];
                    $chosen_name = $chosen['nama'];
                    
                    // Insert
                    $task_name_escaped = mysqli_real_escape_string($conn, $t['nama_tugas']);
                    mysqli_query($conn, "INSERT INTO assigned_tasks (user_id, assigned_to, nama_tugas, tanggal, is_random) VALUES ($uid, '$chosen_name', '$task_name_escaped', '$tgl', 1)");
                    
                    // Update cache
                    $daily_random_count[$chosen_name][$tgl] = ($daily_random_count[$chosen_name][$tgl] ?? 0) + 1;
                    $weekly_workload[$chosen_name]++;
                } else {
                    $skipped_tasks_count++;
                }
            }
        }
        
        $msg = "✅ Berhasil mengacak tugas! Cek di 'Tugas Terjadwal'.";
        if ($skipped_tasks_count > 0) {
            $msg .= "<br><span style='color: orange;'>⚠️ Catatan: Sebanyak <b>$skipped_tasks_count tugas</b> dilewati karena anggota keluarga telah mencapai batas beban kerja harian/mingguan mereka.</span>";
        }
        
        // Trigger Notification for ALL Members
        $notif_title = "Jadwal Tugas Rutin Baru";
        $notif_msg = "Tugas rutin baru untuk 7 hari ke depan telah diacak dengan batas beban kerja. Silakan cek jadwal Anda!";
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
            <p>Sistem akan membagi tugas dari Pool secara adil ke semua anggota keluarga berdasarkan batas beban kerja harian dan mingguan masing-masing.</p>
            
            <?php
            // Fetch limits for display
            $admin_limits_q = mysqli_query($conn, "SELECT max_tasks_per_day, max_tasks_per_week FROM users WHERE id=$uid");
            $admin_limits = mysqli_fetch_assoc($admin_limits_q);
            $m_limits_res = mysqli_query($conn, "SELECT nama, max_tasks_per_day, max_tasks_per_week FROM family_members WHERE user_id=$uid");
            ?>
            <div style="margin: 20px auto; text-align: left; max-width: 500px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #ddd;">
                <h4 style="margin-top: 0; color: #333;">📊 Batas Kapasitas Tugas Anggota:</h4>
                <ul style="padding-left: 20px; margin-bottom: 0; line-height: 1.6; color: #555;">
                    <li><b><?= $admin_nama; ?> (Anda - Kepala Keluarga):</b> Max <?= $admin_limits['max_tasks_per_day'] ?? 2; ?> tugas/hari, <?= $admin_limits['max_tasks_per_week'] ?? 10; ?> tugas/minggu</li>
                    <?php while ($m_limit = mysqli_fetch_assoc($m_limits_res)): ?>
                        <li><b><?= $m_limit['nama']; ?>:</b> Max <?= $m_limit['max_tasks_per_day']; ?> tugas/hari, <?= $m_limit['max_tasks_per_week']; ?> tugas/minggu</li>
                    <?php endwhile; ?>
                </ul>
                <small style="color: #888; display: block; margin-top: 10px;">*Anda dapat mengubah batas kapasitas tugas anggota keluarga di tab 'Anggota Keluarga'.</small>
            </div>
            
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