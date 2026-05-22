<?php
session_start();
include 'includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$u' AND status_aktif=1");
    if (mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        if (password_verify($p, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['foto'] = $row['foto_profil'];
            
            mysqli_query($conn, "INSERT INTO activity_logs (user_id, activity_type, description) VALUES ({$row['id']}, 'login', 'User {$row['username']} berhasil masuk.')");
            
            if ($row['role'] == 'admin') header("Location: admin/dashboard.php");
            else header("Location: user/dashboardd.php");
            exit();
        }
    }
    
    // Cek Family Member
    $q2 = mysqli_query($conn, "SELECT fm.*, u.id as admin_id FROM family_members fm JOIN users u ON fm.user_id=u.id WHERE fm.username='$u'");
    if (mysqli_num_rows($q2) > 0) {
        $row2 = mysqli_fetch_assoc($q2);
        if (password_verify($p, $row2['password'])) {
            $_SESSION['member_id'] = $row2['id'];
            $_SESSION['user_id'] = $row2['admin_id']; // ID Admin rumah tangganya
            $_SESSION['nama'] = $row2['nama'];
            $_SESSION['role'] = 'member';
            $_SESSION['foto'] = $row2['foto_profil'];

            mysqli_query($conn, "INSERT INTO activity_logs (user_id, activity_type, description) VALUES ({$row2['admin_id']}, 'login', 'Anggota Keluarga {$row2['nama']} berhasil masuk.')");

            header("Location: keluarga/dashboard.php");
            exit();
        }
    }
    $err = "Username atau password salah / akun nonaktif!";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login - Smart Home Organizer</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="navbar-brand">
            <img src="assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="index.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-menu">
            <a href="index.php">Home</a>
            <a href="about.php">Tentang</a>
            <a href="help.php">Bantuan</a>
        </div>
        <div class="navbar-user">
            <a href="index.php" class="btn btn-primary">Home</a>
        </div>
    </div>

    <div class="container" style="max-width:400px; margin-top:50px;">
        <h2 style="text-align:center;">Masuk</h2>
        <?php if (isset($err)) echo "<div class='alert'>$err</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-primary" style="width:100%;">Masuk</button>
        </form>
        <p style="text-align:center; margin-top:15px;">Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>
    <script src="assets/js/navbar.js"></script>
</body>

</html>