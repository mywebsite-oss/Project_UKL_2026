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
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $foto_name = 'default.png';
    $upload_err = false;

    // Handle Member Photo Upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $max_size = 2 * 1024 * 1024;
        if ($_FILES['foto']['size'] > $max_size) {
            echo "<script>alert('Ukuran foto terlalu besar! Maksimal 2MB.');</script>";
            $upload_err = true;
        } else {
            $upload_dir = '../assets/image/users/';
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
        }
    }

    $cek = mysqli_query($conn, "SELECT id FROM family_members WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah dipakai!');</script>";
    } elseif (!$upload_err) {
        mysqli_query($conn, "INSERT INTO family_members (user_id, username, password, nama, role_dalam_keluarga, foto_profil) VALUES ($uid, '$username', '$password', '$nama', '$role', '$foto_name')");
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

        <!-- Tombol Tambah & Form Tambah (Hidden by default) -->
        <button id="btn-form-tambah" class="btn btn-primary mb-20" onclick="toggleForm('form-tambah')">+ Tambah Anggota</button>

        <form id="form-tambah" method="POST" enctype="multipart/form-data" class="form-box hidden">
            <h3>Tambah Anggota Keluarga Baru</h3>
            <label>Username Login:</label>
            <input type="text" name="username" placeholder="Username Login" required>
            
            <label>Password Login:</label>
            <input type="password" name="password" placeholder="Password Login" required>
            
            <label>Nama Lengkap:</label>
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            
            <label>Peran (mis: Ibu, Anak Pertama):</label>
            <input type="text" name="role" placeholder="Peran (mis: Ibu)" required>

            <label>Foto Profil (Opsional):</label>
            <input type="file" name="foto" accept="image/*">
            <small style="display:block; margin-bottom:15px; color:#666;">Max 2MB. Format: JPG, PNG, GIF</small>
            
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Anggota</button>
            <button type="button" class="btn btn-danger" onclick="toggleForm('form-tambah')">Batal</button>
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
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/form-toggle.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>