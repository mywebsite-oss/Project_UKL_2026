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

$edit_data = null;
if (isset($_GET['edit_user'])) {
    $eid = $_GET['edit_user'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$eid"));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user_id'])) {
    $id = $_POST['edit_user_id'];
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (!empty($pass)) {
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET username='$user', password='$pass_hash', role='$role', status_aktif='$status' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET username='$user', role='$role', status_aktif='$status' WHERE id=$id");
    }
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['hapus'])) {
    if ($_GET['hapus'] != $uid) {
        mysqli_query($conn, "DELETE FROM users WHERE id=" . $_GET['hapus']);
    }
    header("Location: dashboard.php");
    exit();
}

// Filter & Sort Logic
$filter_role = $_GET['role'] ?? 'all';
$sort_active = $_GET['sort_active'] ?? 'off';
$sort_time = $_GET['sort_time'] ?? 'newest';

$query = "SELECT * FROM users WHERE 1";

if ($filter_role == 'admin') {
    $query .= " AND role='admin'";
} elseif ($filter_role == 'user') {
    $query .= " AND role='user'";
}

$order_clauses = [];

if ($sort_active == 'on') {
    // Aktif (bukan admin) di atas, tidak aktif/jarang aktif di bawah
    $order_clauses[] = "(role != 'admin' AND status_aktif = 1) DESC";
}

if ($sort_time == 'oldest') {
    $order_clauses[] = "id ASC";
} else {
    $order_clauses[] = "id DESC";
}

if (!empty($order_clauses)) {
    $query .= " ORDER BY " . implode(", ", $order_clauses);
}

$users = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar navbar-dashboard">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href='dashboard.php'>Admin Panel</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php" class="active">Dasbor</a>
            <a href="history.php">History</a>
        </div>
        <div class="navbar-user">
            <span><?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <?php renderNotifications($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
            <a href="javascript:void(0)" onclick="openAccountDrawer()">Akun</a>
        </div>
    </div>
    <div class="container-wide">
        <div class="admin-main-content">
            <?php if ($edit_data): ?>
                <!-- Form Edit (Always visible if editing) -->
                <div class="edit-box">
                    <h3>Edit User: <?= $edit_data['username']; ?></h3>
                    <form method="POST">
                        <input type="hidden" name="edit_user_id" value="<?= $edit_data['id']; ?>">
                        
                        <label>Username:</label>
                        <input type="text" name="username" value="<?= $edit_data['username']; ?>" placeholder="Username" required>
                        
                        <label>Password Baru (Kosongkan jika tidak diubah):</label>
                        <input type="password" name="password" placeholder="Password Baru">
                        
                        <label>Role:</label>
                        <select name="role">
                            <option value="user" <?= $edit_data['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?= $edit_data['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        
                        <label>Status:</label>
                        <select name="status">
                            <option value="1" <?= $edit_data['status_aktif'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?= $edit_data['status_aktif'] == 0 ? 'selected' : ''; ?>>Nonaktif</option>
                        </select>
                        
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="dashboard.php" class="btn btn-danger">Batal</a>
                    </form>
                </div>
            <?php endif; ?>

            <h2>Daftar Pengguna</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['username']; ?></td>
                        <td><?= $row['nama_lengkap']; ?></td>
                        <td><?= $row['role']; ?></td>
                        <td><?= $row['status_aktif'] ? 'Aktif' : 'Nonaktif'; ?></td>
                        <td>
                            <a href="?edit_user=<?= $row['id']; ?>&role=<?= $filter_role ?>&sort_active=<?= $sort_active ?>&sort_time=<?= $sort_time ?>" class="btn btn-secondary">Edit</a>
                            <?php if ($row['id'] != $uid) echo "<a href='?hapus=" . $row['id'] . "' class='btn btn-danger' >Hapus</a>"; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="admin-sidebar">
            <div class="filter-group">
                <h4>Filter Role</h4>
                <div class="filter-btn-group">
                    <a href="?role=all&sort_active=<?= $sort_active ?>&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_role == 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="?role=admin&sort_active=<?= $sort_active ?>&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_role == 'admin' ? 'active' : '' ?>">Admin Saja</a>
                    <a href="?role=user&sort_active=<?= $sort_active ?>&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_role == 'user' ? 'active' : '' ?>">User Saja</a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Urutan Keaktifan</h4>
                <div class="filter-btn-group">
                    <a href="?role=<?= $filter_role ?>&sort_active=on&sort_time=<?= $sort_time ?>" class="btn-filter <?= $sort_active == 'on' ? 'active' : '' ?>">User Aktif di Atas</a>
                    <a href="?role=<?= $filter_role ?>&sort_active=off&sort_time=<?= $sort_time ?>" class="btn-filter <?= $sort_active == 'off' ? 'active' : '' ?>">Normal</a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Waktu Pendaftaran</h4>
                <div class="filter-btn-group">
                    <a href="?role=<?= $filter_role ?>&sort_active=<?= $sort_active ?>&sort_time=newest" class="btn-filter <?= $sort_time == 'newest' ? 'active' : '' ?>">Terbaru</a>
                    <a href="?role=<?= $filter_role ?>&sort_active=<?= $sort_active ?>&sort_time=oldest" class="btn-filter <?= $sort_time == 'oldest' ? 'active' : '' ?>">Terlama</a>
                </div>
            </div>
            
            <a href="dashboard.php" class="btn btn-secondary w-100 text-center">Reset Filter</a>
        </div>
    </div>
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/form-toggle.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>