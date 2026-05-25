<?php
include_once 'koneksi.php';

// Handle Profile Update Logic
if (isset($_POST['update_profile'])) {
    session_start();
    $role = $_SESSION['role'];
    $uid = $_SESSION['user_id'];
    $mid = $_SESSION['member_id'] ?? null;
    
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password = $_POST['password'];
    
    $upload_dir = '../assets/image/users/';
    $foto_name = $_POST['old_foto'];

    // Handle Photo Upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $max_size = 2 * 1024 * 1024;
        if ($_FILES['foto']['size'] <= $max_size) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
            $_SESSION['foto'] = $foto_name;
        }
    }

    if ($role == 'member') {
        $sql = "UPDATE family_members SET username='$username', nama='$nama', foto_profil='$foto_name'";
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password='$hashed'";
        }
        $sql .= " WHERE id=$mid";
        mysqli_query($conn, $sql);
        $_SESSION['nama'] = $nama;
    } else {
        $rumah = mysqli_real_escape_string($conn, $_POST['rumah']);
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', nama_rumah_tangga='$rumah', foto_profil='$foto_name'";
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password='$hashed'";
        }
        $sql .= " WHERE id=$uid";
        mysqli_query($conn, $sql);
        $_SESSION['nama'] = $nama;
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

function renderAccountDrawer($conn, $uid, $mid, $role) {
    if ($role == 'member') {
        $q = mysqli_query($conn, "SELECT * FROM family_members WHERE id=$mid");
        $data = mysqli_fetch_assoc($q);
        $display_nama = $data['nama'];
        $display_extra = $data['role_dalam_keluarga'];
    } else {
        $q = mysqli_query($conn, "SELECT * FROM users WHERE id=$uid");
        $data = mysqli_fetch_assoc($q);
        $display_nama = $data['nama_lengkap'];
        $display_extra = $data['nama_rumah_tangga'];
    }
    ?>
    <div id="drawerOverlay" class="drawer-overlay" onclick="closeAccountDrawer()"></div>
    <div id="accountDrawer" class="drawer">
        <div class="drawer-header">
            <h3>Profil Akun</h3>
            <span class="drawer-close" onclick="closeAccountDrawer()">&times;</span>
        </div>

        <div class="drawer-profile-preview">
            <img src="../assets/image/users/<?= $data['foto_profil'] ?? 'default.png'; ?>" alt="Profile">
            <h4><?= $display_nama ?></h4>
            <p style="color:#64748b; font-size:14px;"><?= ($role == 'member' ? 'Anggota: ' : 'Rumah: ') . $display_extra ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_foto" value="<?= $data['foto_profil'] ?>">
            
            <label>Username:</label>
            <input type="text" name="username" value="<?= $data['username'] ?>" required>

            <label>Nama Lengkap:</label>
            <input type="text" name="nama" value="<?= ($role == 'member' ? $data['nama'] : $data['nama_lengkap']) ?>" required>

            <?php if ($role != 'member'): ?>
                <label>Nama Rumah Tangga:</label>
                <input type="text" name="rumah" value="<?= $data['nama_rumah_tangga'] ?>" required>
            <?php endif; ?>

            <label>Ganti Foto Profil (Opsional):</label>
            <input type="file" name="foto" accept="image/*">
            <small style="display:block; margin-bottom:15px; color:#666;">Max 2MB. Kosongkan jika tidak diubah.</small>

            <label>Password Baru:</label>
            <input type="password" name="password" placeholder="Isi untuk mengganti password">
            <small style="display:block; margin-bottom:15px; color:#666;">Kosongkan jika tidak ingin mengubah password.</small>

            <div class="flex gap-10" style="margin-top: 20px;">
                <button type="submit" name="update_profile" class="btn btn-primary flex-1">Simpan Perubahan</button>
                <a href="../logout.php" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Keluar</a>
            </div>
        </form>
    </div>

    <script>
        function openAccountDrawer() {
            document.getElementById('drawerOverlay').style.display = 'block';
            setTimeout(() => {
                document.getElementById('accountDrawer').classList.add('open');
            }, 10);
        }

        function closeAccountDrawer() {
            document.getElementById('accountDrawer').classList.remove('open');
            setTimeout(() => {
                document.getElementById('drawerOverlay').style.display = 'none';
            }, 300);
        }
    </script>
    <?php
}
?>