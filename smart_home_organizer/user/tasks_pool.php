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

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    mysqli_query($conn, "INSERT INTO task_pool (user_id, nama_tugas) VALUES ($uid, '$nama')");
    header("Location: tasks_pool.php");
    exit();
}
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM task_pool WHERE id=" . $_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks_pool.php");
    exit();
}
if (isset($_GET['hapus_semua'])) {
    mysqli_query($conn, "DELETE FROM task_pool WHERE user_id=$uid");
    header("Location: tasks_pool.php");
    exit();
}
$data = mysqli_query($conn, "SELECT * FROM task_pool WHERE user_id=$uid");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pool Tugas</title>
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
        <h2>Pool Tugas</h2>

        <div class="nav-tabs">
            <a href="tasks.php" class="nav-tab">Semua Tugas</a>
            <a href="tasks_anggota.php" class="nav-tab active">Pembagian Tugas</a>
        </div>
        <div class="sub-tabs">
            <a href="tasks_anggota.php" class="sub-tab">Anggota Keluarga</a>
            <a href="tasks_pool.php" class="sub-tab active">Pool Tugas</a>
            <a href="tasks_acak.php" class="sub-tab">Pengacak</a>
        </div>

        <!-- Tombol Tambah & Form Tambah (Hidden by default) -->
        <div class="flex gap-10 mb-20">
            <button id="btn-form-tambah" class="btn btn-primary" onclick="toggleForm('form-tambah')">+ Tambah Pool Tugas</button>
            <a href="?hapus_semua=1" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus SELURUH daftar di Pool Tugas?')">🗑️ Hapus Semua Pool</a>
        </div>

        <form id="form-tambah" method="POST" class="form-box hidden">
            <h3>Tambah Tugas ke Pool</h3>
            <label>Nama Tugas Rutin:</label>
            <input type="text" name="nama" placeholder="Nama Tugas (mis: Cuci Piring)" required>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan ke Pool</button>
            <button type="button" class="btn btn-danger" onclick="toggleForm('form-tambah')">Batal</button>
        </form>

        <table>
            <tr>
                <th>Nama Tugas</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?= $row['nama_tugas']; ?></td>
                    <td><a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/form-toggle.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>