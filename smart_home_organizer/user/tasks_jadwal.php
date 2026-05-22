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
$admin_nama = $_SESSION['nama'];
$view = $_GET['view'] ?? 'all';

// Filter & Sort Parameters
$filter_status = $_GET['status'] ?? 'all';
$sort_time = $_GET['sort_time'] ?? 'oldest';

if (isset($_GET['approve'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Disetujui', status='Selesai' WHERE id=".(int)$_GET['approve']);
    header("Location: tasks_jadwal.php?view=$view&status=$filter_status&sort_time=$sort_time");
    exit();
}
if (isset($_GET['reject'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Belum Selesai', status='Pending' WHERE id=".(int)$_GET['reject']);
    header("Location: tasks_jadwal.php?view=$view&status=$filter_status&sort_time=$sort_time");
    exit();
}
if (isset($_GET['selesai_saya'])) {
    mysqli_query($conn, "UPDATE assigned_tasks SET status='Selesai', approval_status='Disetujui' WHERE id=".(int)$_GET['selesai_saya']);
    header("Location: tasks_jadwal.php?view=saya&status=$filter_status&sort_time=$sort_time");
    exit();
}
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM assigned_tasks WHERE id=" . (int)$_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks_jadwal.php?view=$view&status=$filter_status&sort_time=$sort_time");
    exit();
}

if (isset($_GET['hapus_semua'])) {
    mysqli_query($conn, "DELETE FROM assigned_tasks WHERE user_id=$uid");
    header("Location: tasks_jadwal.php");
    exit();
}

// Build Query
$query = "SELECT * FROM assigned_tasks WHERE user_id=$uid";

if ($view == 'saya') {
    $query .= " AND assigned_to='$admin_nama'";
}

if ($filter_status == 'pending') {
    $query .= " AND status='Pending'";
} elseif ($filter_status == 'selesai') {
    $query .= " AND status='Selesai'";
}

$query .= " ORDER BY tanggal " . ($sort_time == 'newest' ? 'DESC' : 'ASC');
$data = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Tugas Terjadwal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar { top: 80px; }
    </style>
    <script>
        function openConfirmModal(id, imgName) {
            document.getElementById('confirm-img').src = '../assets/image/tasks/' + imgName;
            document.getElementById('btn-approve').href = '?approve=' + id + '&view=<?= $view ?>&status=<?= $filter_status ?>';
            document.getElementById('btn-reject').href = '?reject=' + id + '&view=<?= $view ?>&status=<?= $filter_status ?>';
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }
    </script>
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
    
    <div class="container-wide">
        <div class="admin-main-content">
            <h2>Tugas</h2>

            <div class="nav-tabs flex" style="justify-content: space-between; align-items: center;">
                <div>
                    <a href="tasks.php" class="nav-tab active">Semua Tugas</a>
                    <a href="tasks_anggota.php" class="nav-tab">Pembagian Tugas</a>
                </div>
                <a href="?view=saya" class="btn btn-primary <?= $view == 'saya' ? 'bg-green-success' : '' ?>">Tugas Saya</a>
            </div>
            <div class="sub-tabs">
                <a href="tasks.php" class="sub-tab">Tugas Pribadi</a>
                <a href="tasks_jadwal.php" class="sub-tab active">Tugas Terjadwal</a>
            </div>

            <?php if ($view != 'saya'): ?>
                <div class="mb-20">
                    <a href="?hapus_semua=1" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus SEMUA tugas terjadwal?')">🗑️ Hapus Semua Tugas Terjadwal</a>
                </div>
            <?php endif; ?>

            <h3><?= $view == 'saya' ? 'Daftar Tugas Terjadwal Saya' : 'Daftar Semua Tugas Terjadwal'; ?></h3>
            <table>
                <tr>
                    <th>Tanggal</th>
                    <th>Tugas</th>
                    <th>Ditugaskan Ke</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <?php 
                    $approval = $row['approval_status'] ?? 'Belum Selesai';
                    $status = $row['status'] ?? 'Pending';
                    ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td><?= $row['nama_tugas']; ?></td>
                        <td><?= $row['assigned_to']; ?></td>
                        <td>
                            <?php if ($approval == 'Menunggu Persetujuan'): ?>
                                <span class="text-orange">⏳ Menunggu Konfirmasi</span>
                            <?php else: ?>
                                <span class="<?= $status == 'Selesai' ? 'text-green' : 'text-red'; ?>"><?= $status; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($view == 'saya'): ?>
                                <?php if ($status == 'Pending'): ?>
                                    <a href="?selesai_saya=<?= $row['id']; ?>&view=saya&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>" class="btn btn-primary" title="Selesai">✓</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($approval == 'Menunggu Persetujuan'): ?>
                                    <button onclick="openConfirmModal(<?= $row['id']; ?>, '<?= $row['bukti_foto']; ?>')" class="btn btn-secondary bg-green-success">Konfirmasi</button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="?hapus=<?= $row['id']; ?>&view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($data) == 0): ?>
                    <tr><td colspan="5" class="text-center">Tidak ada tugas terjadwal ditemukan.</td></tr>
                <?php endif; ?>
            </table>
            <?php if ($view == 'saya'): ?>
                <div class="mt-15">
                    <a href="tasks_jadwal.php" class="btn btn-secondary">← Kembali ke Semua</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="admin-sidebar">
            <div class="filter-group">
                <h4>Status Tugas</h4>
                <div class="filter-btn-group">
                    <a href="?view=<?= $view ?>&status=all&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_status == 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="?view=<?= $view ?>&status=pending&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_status == 'pending' ? 'active' : '' ?>">Belum Selesai</a>
                    <a href="?view=<?= $view ?>&status=selesai&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_status == 'selesai' ? 'active' : '' ?>">Selesai</a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Urutan Waktu</h4>
                <div class="filter-btn-group">
                    <a href="?view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=oldest" class="btn-filter <?= $sort_time == 'oldest' ? 'active' : '' ?>">Terdekat (Segera)</a>
                    <a href="?view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=newest" class="btn-filter <?= $sort_time == 'newest' ? 'active' : '' ?>">Terjauh (Mendatang)</a>
                </div>
            </div>
            
            <a href="tasks_jadwal.php?view=<?= $view ?>" class="btn btn-secondary w-100 text-center">Reset Filter</a>
        </div>
    </div>

    <!-- CONFIRM MODAL (ADMIN) -->
    <div id="confirmModal" class="modal hidden">
        <div class="modal-content">
            <h3>Konfirmasi Penyelesaian Tugas</h3>
            <p>Berikut adalah bukti foto yang dikirim oleh anggota keluarga:</p>
            <img id="confirm-img" src="" class="modal-img-preview" alt="Bukti Foto">
            
            <div class="flex gap-10" style="justify-content: center;">
                <a id="btn-approve" href="#" class="btn btn-primary bg-green-success">Terima</a>
                <a id="btn-reject" href="#" class="btn btn-danger">Tolak</a>
                <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">Tutup</button>
            </div>
        </div>
    </div>

    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>