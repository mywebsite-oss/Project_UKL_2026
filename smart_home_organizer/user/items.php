<?php
session_start();
include '../includes/koneksi.php';
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
    <div class="navbar">
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
            <a href="../logout.php">Keluar</a>
        </div>
    </div>
    <div class="container">
        <h2><?= $edit_data ? 'Edit Barang' : 'Tambah Barang'; ?></h2>
        
        <form method="GET" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Cari nama barang atau lokasi..." value="<?= htmlspecialchars($search); ?>" style="flex: 1; margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if (!empty($search)): ?>
                <a href="items.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <form method="POST" class="form-box">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? ''; ?>">
            <input type="text" name="nama" placeholder="Nama Barang" value="<?= $edit_data['nama_barang'] ?? ''; ?>" required class="input-inline">
            <input type="text" name="lokasi" placeholder="Lokasi (mis: Dapur)" value="<?= $edit_data['lokasi_detail'] ?? ''; ?>" required class="input-inline">
            <button type="submit" class="btn <?= $edit_data ? 'btn-secondary' : 'btn-primary'; ?>">
                <?= $edit_data ? 'Update Barang' : 'Tambah Barang'; ?>
            </button>
            <?php if ($edit_data): ?> <a href="items.php" class="btn btn-danger">Batal Edit</a> <?php endif; ?>
        </form>

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
</body>

</html>