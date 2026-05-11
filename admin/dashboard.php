<?php
session_start();
include '../includes/koneksi.php';
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

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href='dashboard.php'>Admin Panel</a>
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php" class="active">Dasbor</a>
            <a href="logs.php">History</a>
        </div>
        <div class="navbar-user">
            <span><?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <a href="../logout.php">Keluar</a>
        </div>
    </div>
    </div>
    <div class="container">
        <?php if ($edit_data): ?>
            <div style="background:#fff3cd; padding:20px; border-radius:8px; margin-bottom:20px; border:1px solid #ffeeba;">
                <h3>Edit User: <?= $edit_data['username']; ?></h3>
                <form method="POST">
                    <input type="hidden" name="edit_user_id" value="<?= $edit_data['id']; ?>">
                    <input type="text" name="username" value="<?= $edit_data['username']; ?>" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password Baru (Kosongkan jika tidak diubah)">
                    <select name="role">
                        <option value="user" <?= $edit_data['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?= $edit_data['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
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
                        <a href="?edit_user=<?= $row['id']; ?>" class="btn btn-secondary">Edit</a>
                        <?php if ($row['id'] != $uid) echo "<a href='?hapus=" . $row['id'] . "' class='btn btn-danger' >Hapus</a>"; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>