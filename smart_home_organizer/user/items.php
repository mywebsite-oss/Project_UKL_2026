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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $edit_id = $_POST['edit_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $lok = mysqli_real_escape_string($conn, $_POST['lokasi']);

    if ($edit_id) {
        mysqli_query($conn, "UPDATE items SET nama_barang='$nama', lokasi_detail='$lok' WHERE id=$edit_id AND user_id=$uid");
    } else {
        mysqli_query($conn, "INSERT INTO items (user_id, nama_barang, lokasi_detail) VALUES ($uid, '$nama', '$lok')");
    }
    header("Location: items.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM items WHERE id=" . $_GET['hapus'] . " AND user_id=$uid");
    header("Location: items.php");
    exit();
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE id=$edit_id AND user_id=$uid"));
}

$query_items = "SELECT * FROM items WHERE user_id=$uid";
if (!empty($search)) {
    $query_items .= " AND (nama_barang LIKE '%$search%' OR lokasi_detail LIKE '%$search%')";
}
$query_items .= " ORDER BY id DESC";
$items = mysqli_query($conn, $query_items);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Barang</title>
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
            <a href="items.php" class="active">Barang</a>
            <a href="tasks.php">Tugas</a>
        </div>
        <div class="navbar-user">
            <span>Halo, <?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <?php renderNotifications($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
            <a href="javascript:void(0)" onclick="openAccountDrawer()">Akun</a>
        </div>
    </div>
    <div class="container">
        <h2>Barang</h2>
        
        <form method="GET" class="search-container">
            <input type="text" name="search" placeholder="Cari nama barang atau lokasi..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if (!empty($search)): ?>
                <a href="items.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <?php if ($edit_data): ?>
            <!-- Form Edit (Always visible if editing) -->
            <form method="POST" class="form-box">
                <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? ''; ?>">
                <h3>Edit Barang</h3>
                <label>Nama Barang:</label>
                <input type="text" name="nama" placeholder="Nama Barang" value="<?= $edit_data['nama_barang'] ?? ''; ?>" required>
                
                <label>Lokasi Detail (mis: Dapur, Gudang):</label>
                <input type="text" name="lokasi" placeholder="Lokasi (mis: Dapur)" value="<?= $edit_data['lokasi_detail'] ?? ''; ?>" required>
                
                <button type="submit" class="btn btn-secondary">Update Barang</button>
                <a href="items.php" class="btn btn-danger">Batal Edit</a>
            </form>
        <?php else: ?>
            <!-- Tombol Tambah & Form Tambah (Hidden by default) -->
            <div class="flex gap-10 mb-20">
                <button id="btn-form-tambah" class="btn btn-primary" onclick="toggleForm('form-tambah')">+ Tambah Barang</button>
                <a href="?hapus_semua=1" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus SEMUA barang?')">🗑️ Hapus Semua</a>
            </div>

            <form id="form-tambah" method="POST" class="form-box hidden">
                <input type="hidden" name="edit_id" value="">
                <h3>Tambah Barang Baru</h3>
                <label>Nama Barang:</label>
                <input type="text" name="nama" placeholder="Nama Barang" required>
                
                <label>Lokasi Detail (mis: Dapur, Gudang):</label>
                <input type="text" name="lokasi" placeholder="Lokasi (mis: Dapur)" required>
                
                <button type="submit" class="btn btn-primary">Simpan Barang</button>
                <button type="button" class="btn btn-danger" onclick="toggleForm('form-tambah')">Batal</button>
            </form>
        <?php endif; ?>

        <table>
            <tr>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($items)): ?>
                <tr>
                    <td><?= $row['nama_barang']; ?></td>
                    <td><?= $row['lokasi_detail']; ?></td>
                    <td>
                        <a href="?edit=<?= $row['id']; ?>" class="btn btn-secondary">Edit</a>
                        <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/form-toggle.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>