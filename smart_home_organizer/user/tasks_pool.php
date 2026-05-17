<?php
session_start();
include '../includes/koneksi.php';
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
$data = mysqli_query($conn, "SELECT * FROM task_pool WHERE user_id=$uid");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pool Tugas</title>
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

        <div class="nav-tabs">
            <a href="tasks.php" class="nav-tab">Semua Tugas</a>
            <a href="tasks_anggota.php" class="nav-tab active">Pembagian Tugas</a>
        </div>
        <div class="sub-tabs">
            <a href="tasks_anggota.php" class="sub-tab">Anggota Keluarga</a>
            <a href="tasks_pool.php" class="sub-tab active">Pool Tugas</a>
            <a href="tasks_acak.php" class="sub-tab">Pengacak</a>
        </div>

        <form method="POST" class="form-pool">
            <input type="text" name="nama" placeholder="Nama Tugas untuk Diacak" required class="input-wide">
            <button type="submit" name="simpan" class="btn btn-primary">Tambah</button>
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
</body>

</html>