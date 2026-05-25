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

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM family_members WHERE id=$edit_id AND user_id=$uid"));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['simpan']) || isset($_POST['ubah']))) {
    $edit_id = isset($_POST['edit_id']) && !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $max_tasks_per_day = isset($_POST['max_tasks_per_day']) ? (int)$_POST['max_tasks_per_day'] : 2;
    $max_tasks_per_week = isset($_POST['max_tasks_per_week']) ? (int)$_POST['max_tasks_per_week'] : 10;
    
    // Check if username is already taken
    if ($edit_id) {
        $cek = mysqli_query($conn, "SELECT id FROM family_members WHERE username='$username' AND id != $edit_id");
    } else {
        $cek = mysqli_query($conn, "SELECT id FROM family_members WHERE username='$username'");
    }
    $cek_users = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($cek) > 0 || mysqli_num_rows($cek_users) > 0) {
        echo "<script>alert('Username sudah dipakai!');</script>";
    } else {
        $upload_err = false;
        $foto_name_val = null;
        
        // Handle Photo Upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $max_size = 2 * 1024 * 1024;
            if ($_FILES['foto']['size'] > $max_size) {
                echo "<script>alert('Ukuran foto terlalu besar! Maksimal 2MB.');</script>";
                $upload_err = true;
            } else {
                $upload_dir = '../assets/image/users/';
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = uniqid() . '.' . $ext;
                
                if ($edit_id) {
                    // Get old photo to delete it
                    $old_q = mysqli_query($conn, "SELECT foto_profil FROM family_members WHERE id=$edit_id AND user_id=$uid");
                    if (mysqli_num_rows($old_q) > 0) {
                        $old_row = mysqli_fetch_assoc($old_q);
                        $old_foto = $old_row['foto_profil'];
                        if ($old_foto != 'default.png' && file_exists($upload_dir . $old_foto)) {
                            @unlink($upload_dir . $old_foto);
                        }
                    }
                }
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name)) {
                    $foto_name_val = $foto_name;
                } else {
                    $upload_err = true;
                }
            }
        }
        
        if (!$upload_err) {
            if ($edit_id) {
                $password_sql = "";
                if (!empty($_POST['password'])) {
                    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $password_sql = ", password='$hashed'";
                }
                
                $foto_update_sql = $foto_name_val ? ", foto_profil='$foto_name_val'" : "";
                mysqli_query($conn, "UPDATE family_members SET username='$username' $password_sql, nama='$nama', role_dalam_keluarga='$role', max_tasks_per_day=$max_tasks_per_day, max_tasks_per_week=$max_tasks_per_week $foto_update_sql WHERE id=$edit_id AND user_id=$uid");
            } else {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $foto_name_val = $foto_name_val ? $foto_name_val : 'default.png';
                mysqli_query($conn, "INSERT INTO family_members (user_id, username, password, nama, role_dalam_keluarga, foto_profil, max_tasks_per_day, max_tasks_per_week) VALUES ($uid, '$username', '$password', '$nama', '$role', '$foto_name_val', $max_tasks_per_day, $max_tasks_per_week)");
            }
            header("Location: tasks_anggota.php");
            exit();
        }
    }
}

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM family_members WHERE id=" . (int)$_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks_anggota.php");
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) FROM family_members WHERE user_id=$uid";
$total_records = mysqli_fetch_array(mysqli_query($conn, $total_query))[0];
$total_pages = ceil($total_records / $limit);

$data = mysqli_query($conn, "SELECT * FROM family_members WHERE user_id=$uid LIMIT $limit OFFSET $offset");
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

        <?php if ($edit_data): ?>
            <!-- Form Edit (Always visible if editing) -->
            <form method="POST" enctype="multipart/form-data" class="form-box">
                <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? ''; ?>">
                <h3>Edit Anggota Keluarga</h3>
                
                <label>Username Login:</label>
                <input type="text" name="username" placeholder="Username Login" value="<?= htmlspecialchars($edit_data['username'] ?? ''); ?>" required>
                
                <label>Password Baru (Kosongkan jika tidak ingin diubah):</label>
                <input type="password" name="password" placeholder="Password Baru">
                
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" placeholder="Nama Lengkap" value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>" required>
                
                <label>Peran (mis: Ibu, Anak Pertama):</label>
                <input type="text" name="role" placeholder="Peran (mis: Ibu)" value="<?= htmlspecialchars($edit_data['role_dalam_keluarga'] ?? ''); ?>" required>

                <label>Batas Tugas Harian (Maks):</label>
                <input type="number" name="max_tasks_per_day" placeholder="Batas Harian" min="1" value="<?= htmlspecialchars($edit_data['max_tasks_per_day'] ?? '2'); ?>" required>
                
                <label>Batas Tugas Mingguan (Maks):</label>
                <input type="number" name="max_tasks_per_week" placeholder="Batas Mingguan" min="1" value="<?= htmlspecialchars($edit_data['max_tasks_per_week'] ?? '10'); ?>" required>

                <label>Foto Profil Baru (Opsional):</label>
                <input type="file" name="foto" accept="image/*">
                <small style="display:block; margin-bottom:15px; color:#666;">Max 2MB. Format: JPG, PNG, GIF</small>
                
                <button type="submit" name="ubah" class="btn btn-secondary">Update Anggota</button>
                <a href="tasks_anggota.php" class="btn btn-danger">Batal Edit</a>
            </form>
        <?php else: ?>
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

                <label>Batas Tugas Harian (Maks):</label>
                <input type="number" name="max_tasks_per_day" placeholder="Batas Harian (default: 2)" min="1" value="2" required>
                
                <label>Batas Tugas Mingguan (Maks):</label>
                <input type="number" name="max_tasks_per_week" placeholder="Batas Mingguan (default: 10)" min="1" value="10" required>

                <label>Foto Profil (Opsional):</label>
                <input type="file" name="foto" accept="image/*">
                <small style="display:block; margin-bottom:15px; color:#666;">Max 2MB. Format: JPG, PNG, GIF</small>
                
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Anggota</button>
                <button type="button" class="btn btn-danger" onclick="toggleForm('form-tambah')">Batal</button>
            </form>
        <?php endif; ?>

        <table>
            <tr>
                <th>Username</th>
                <th>Nama</th>
                <th>Peran</th>
                <th>Batas Harian</th>
                <th>Batas Mingguan</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><b><?= $row['username']; ?></b></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['role_dalam_keluarga']; ?></td>
                    <td><?= $row['max_tasks_per_day']; ?> tugas</td>
                    <td><?= $row['max_tasks_per_week']; ?> tugas</td>
                    <td>
                        <a href="?edit=<?= $row['id']; ?>" class="btn btn-secondary" style="margin-right: 5px;">Edit</a>
                        <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <?php if ($total_pages > 1): ?>
            <?php
            if (!function_exists('getPaginationUrl')) {
                function getPaginationUrl($page_num) {
                    $params = $_GET;
                    $params['page'] = $page_num;
                    return '?' . http_build_query($params);
                }
            }
            ?>
            <div class="pagination" style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; margin-top: 15px; margin-bottom: 10px;">
                <?php if ($page > 1): ?>
                    <a href="<?= getPaginationUrl($page - 1); ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px; line-height: 1;"><</a>
                <?php endif; ?>
                <span style="font-size: 12px; color: #555; background: #eee; padding: 4px 8px; border-radius: 4px;"><?= $page; ?> / <?= $total_pages; ?></span>
                <?php if ($page < $total_pages): ?>
                    <a href="<?= getPaginationUrl($page + 1); ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px; line-height: 1;">></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/form-toggle.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>