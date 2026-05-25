<?php
session_start();
include '../includes/koneksi.php';
include_once '../includes/notifications.php';
include_once '../includes/account_drawer.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];

// Filter Logic
$filter_user = $_GET['user_id'] ?? 'all';
$filter_range = $_GET['range'] ?? 'all';
$filter_type = $_GET['type'] ?? 'all';

$query = "SELECT al.*, u.nama_lengkap FROM activity_logs al LEFT JOIN users u ON al.user_id=u.id WHERE 1";

// User Filter
if ($filter_user != 'all') {
    $uid_filter = mysqli_real_escape_string($conn, $filter_user);
    $query .= " AND al.user_id = '$uid_filter'";
}

// Activity Type Filter
if ($filter_type == 'login') {
    $query .= " AND al.activity_type = 'login'";
} elseif ($filter_type == 'logout') {
    $query .= " AND al.activity_type = 'logout'";
}

// Time Range Filter
if ($filter_range != 'all') {
    switch ($filter_range) {
        case '1day': 
            $query .= " AND DATE(al.created_at) = CURDATE()"; 
            break;
        case '1week': 
            $query .= " AND al.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)"; 
            break;
        case '1month': 
            $query .= " AND al.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)"; 
            break;
        case '1year': 
            $query .= " AND al.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)"; 
            break;
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 15;
$offset = ($page - 1) * $limit;

$total_query = str_replace("SELECT al.*, u.nama_lengkap", "SELECT COUNT(*)", $query);
$total_records = mysqli_fetch_array(mysqli_query($conn, $total_query))[0];
$total_pages = ceil($total_records / $limit);

$query .= " ORDER BY al.created_at DESC LIMIT $limit OFFSET $offset";
$logs = mysqli_query($conn, $query);

// Get list of users for dropdown
$user_list = mysqli_query($conn, "SELECT id, nama_lengkap, role FROM users ORDER BY nama_lengkap ASC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Activity Logs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar navbar-dashboard">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href='dashboard.php'>Admin Panel</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php">Dasbor</a>
            <a href="history.php" class="active">History</a>
        </div>
        <div class="navbar-user">
            <span><?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <?php renderNotifications($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
            <a href="javascript:void(0)" onclick="openAccountDrawer()">Akun</a>
        </div>
    </div>

    <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
        <!-- USER & TYPE FILTER DROPDOWN (Top Area) -->
        <div class="form-box w-100" style="margin-bottom: 20px; box-sizing: border-box;">
            <form method="GET" class="flex gap-10" style="width: 100%; align-items: center;">
                <input type="hidden" name="range" value="<?= $filter_range ?>">
                
                <div style="flex: 2;">
                    <label class="font-12" style="font-weight:bold; display:block; margin-bottom:5px;">Pilih Pengguna:</label>
                    <select name="user_id" style="margin:0; width:100%;">
                        <option value="all" <?= $filter_user == 'all' ? 'selected' : '' ?>>Semua Pengguna</option>
                        <?php 
                        mysqli_data_seek($user_list, 0);
                        while($u = mysqli_fetch_assoc($user_list)): 
                        ?>
                            <option value="<?= $u['id'] ?>" <?= $filter_user == $u['id'] ? 'selected' : '' ?>>
                                <?= $u['nama_lengkap'] ?> (<?= ucfirst($u['role']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="flex: 1;">
                    <label class="font-12" style="font-weight:bold; display:block; margin-bottom:5px;">Tipe Aktivitas:</label>
                    <select name="type" style="margin:0; width:100%;">
                        <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>Keduanya</option>
                        <option value="login" <?= $filter_type == 'login' ? 'selected' : '' ?>>Login</option>
                        <option value="logout" <?= $filter_type == 'logout' ? 'selected' : '' ?>>Logout</option>
                    </select>
                </div>

                <div style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($filter_user != 'all' || $filter_range != 'all' || $filter_type != 'all'): ?>
                        <a href="history.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-layout">
            <div class="admin-main-content">
                <h2>History Login & Logout</h2>
                <table>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Keterangan</th>
                    </tr>
                    <?php if (mysqli_num_rows($logs) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($logs)): ?>
                            <tr>
                                <td><?= date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                <td><?= $row['nama_lengkap'] ?? 'Unknown'; ?></td>
                                <td><?= strtoupper($row['activity_type']); ?></td>
                                <td><?= $row['description']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data history untuk filter ini.</td>
                        </tr>
                    <?php endif; ?>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <?php
                    if (!function_exists('getPaginationUrl')) {
                        function getPaginationUrl($page_num) {
                            $params = $_GET;
                            $params['page'] = $page_num;
                            return '?' . http_build_query($params);
                        }
                    }
                    ?>
                    <div class="pagination" style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; margin-top: 15px; margin-bottom: 10px;">
                        <?php if ($page > 1): ?>
                            <a href="<?= getPaginationUrl($page - 1); ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px; line-height: 1;"><</a>
                        <?php endif; ?>
                        <span style="font-size: 12px; color: #555; background: #eee; padding: 4px 8px; border-radius: 4px;"><?= $page; ?> / <?= $total_pages; ?></span>
                        <?php if ($page < $total_pages): ?>
                            <a href="<?= getPaginationUrl($page + 1); ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px; line-height: 1;">></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="admin-sidebar">
                <div class="filter-group">
                    <h4>Rentang Waktu</h4>
                    <div class="filter-btn-group">
                        <a href="?user_id=<?= $filter_user ?>&range=1day" class="btn-filter <?= $filter_range == '1day' ? 'active' : '' ?>">Hari Ini (1 Hari)</a>
                        <a href="?user_id=<?= $filter_user ?>&range=1week" class="btn-filter <?= $filter_range == '1week' ? 'active' : '' ?>">1 Pekan Terakhir</a>
                        <a href="?user_id=<?= $filter_user ?>&range=1month" class="btn-filter <?= $filter_range == '1month' ? 'active' : '' ?>">1 Bulan Terakhir</a>
                        <a href="?user_id=<?= $filter_user ?>&range=1year" class="btn-filter <?= $filter_range == '1year' ? 'active' : '' ?>">1 Tahun Terakhir</a>
                        <a href="?user_id=<?= $filter_user ?>&range=all" class="btn-filter <?= $filter_range == 'all' ? 'active' : '' ?>">Semua Waktu</a>
                    </div>
                </div>

                <div class="mt-15">
                    <p class="font-12" style="color:#64748b;">Menampilkan riwayat aktivitas login dan logout sistem.</p>
                </div>
            </div>
        </div>
    </div>
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>