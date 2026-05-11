<?php
session_start();
include 'includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$u' AND status_aktif=1");
    $user = mysqli_fetch_assoc($q);

    if (!$user) {
        $q = mysqli_query($conn, "SELECT * FROM family_members WHERE username='$u'");
        $member = mysqli_fetch_assoc($q);
        if ($member && password_verify($p, $member['password'])) {
            $_SESSION['user_id'] = $member['user_id'];
            $_SESSION['member_id'] = $member['id'];
            $_SESSION['nama'] = $member['nama'];
            $_SESSION['role'] = 'member';
            $_SESSION['foto'] = $member['foto_profil'];
            header("Location: keluarga/dashboard.php");
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
                mysqli_query($conn, "INSERT INTO activity_logs (user_id, activity_type, description) VALUES ($uid, 'login', 'User masuk ke sistem')");
            }
            exit();
        }
    }

    if ($user && password_verify($p, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['foto'] = $user['foto_profil'];
        if (isset($_SESSION['user_id'])) {
            $uid = $_SESSION['user_id'];
            mysqli_query($conn, "INSERT INTO activity_logs (user_id, activity_type, description) VALUES ($uid, 'login', 'User masuk ke sistem')");
        }
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboardd.php");
        }
        exit();
    }

    $err = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
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
            <a href="index.php" class="active">Kembali</a>
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
</body>

</html>