<?php
include 'includes/koneksi.php';
$upload_dir = 'assets/image/users/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $n = mysqli_real_escape_string($conn, $_POST['nama']);
    $r = mysqli_real_escape_string($conn, $_POST['rumah']);

    $foto_p = 'default.png';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $foto_p = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $foto_p;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                $err = "Gagal upload foto!";
            }
        } else {
            $err = "Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan!";
        }
    }

    if (!isset($err)) {
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$u'");
        if (mysqli_num_rows($cek) > 0) {
            $err = "Username sudah dipakai!";
        } else {
            mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, nama_rumah_tangga, foto_profil) VALUES ('$u','$p','$n','$r','$foto_p')");
            header("Location: login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Daftar</title>
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
    <div class="container" style="max-width:450px; margin-top:50px;">
        <h2 style="text-align:center;">Daftar Akun</h2>
        <?php if (isset($err)) echo "<div class='alert'>$err</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="text" name="rumah" placeholder="Nama Rumah Tangga" required>
            <input type="password" name="password" placeholder="Password" required>

            <div style="margin:15px 0;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Foto Profil (Opsional):</label>
                <input type="file" name="foto" accept="image/*" style="padding:8px;">
                <small style="color:#666; font-size:12px;">Max 2MB. Format: JPG, PNG, GIF</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Daftar</button>
        </form>

        <p style="text-align:center; margin-top:15px;">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</body>

</html>