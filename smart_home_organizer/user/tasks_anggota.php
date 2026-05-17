<?php
session_start();
include '../includes/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];

if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $cek = mysqli_query($conn, "SELECT id FROM family_members WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah dipakai!');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO family_members (user_id, username, password, nama, role_dalam_keluarga) VALUES ($uid, '$username', '$password', '$nama', '$role')");
        header("Location: tasks_anggota.php");
        exit();
    }
}

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM family_members WHERE id=" . $_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks_anggota.php");
    exit();
}

$data = mysqli_query($conn, "SELECT * FROM family_members WHERE user_id=$uid");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Anggota Keluarga</title>
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
        <h2>Anggota Keluarga</h2>

        <div class="nav-tabs">
            <a href="tasks.php" class="nav-tab">Semua Tugas</a>
            <a href="tasks_anggota.php" class="nav-tab active">Pembagian Tugas</a>
        </div>
        <div class="sub-tabs">
            <a href="tasks_anggota.php" class="sub-tab active">Anggota Keluarga</a>
            <a href="tasks_pool.php" class="sub-tab">Pool Tugas</a>
            <a href="tasks_acak.php" class="sub-tab">Pengacak</a>
        </div>

        <form method="POST" class="form-warning">
            <input type="text" name="username" placeholder="Username Login" required class="input-quarter">
            <input type="password" name="password" placeholder="Password Login" required class="input-quarter">
            <input type="text" name="nama" placeholder="Nama Lengkap" required class="input-quarter">
            <input type="text" name="role" placeholder="Peran (mis: Ibu)" required class="input-quarter">
            <button type="submit" name="simpan" class="btn btn-primary">Tambah</button>
        </form>

        <table>
            <tr>
                <th>Username</th>
                <th>Nama</th>
                <th>Peran</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><b><?= $row['username']; ?></b></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['role_dalam_keluarga']; ?></td>
                    <td><a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>