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

// Sort & Filter Parameters
$sort_time = $_GET['sort_time'] ?? 'newest';
$sort_prio = $_GET['sort_prio'] ?? 'off';
$filter_status = $_GET['status'] ?? 'all';

// Approve / Reject Logic
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'done') {
        mysqli_query($conn, "UPDATE tasks SET status='Selesai', approval_status='Disetujui' WHERE id=$id AND user_id=$uid");
    } elseif ($action == 'approve') {
        mysqli_query($conn, "UPDATE tasks SET status='Selesai', approval_status='Disetujui' WHERE id=$id AND user_id=$uid");
    } elseif ($action == 'reject') {
        // When rejected, reset bukti_foto? User didn't specify, but usually good to keep for history or delete. 
        // Let's keep it but reset status.
        mysqli_query($conn, "UPDATE tasks SET status='Pending', approval_status='Belum Selesai' WHERE id=$id AND user_id=$uid");
    }
    header("Location: tasks.php?view=$view&sort_time=$sort_time&sort_prio=$sort_prio&status=$filter_status");
    exit();
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $prio = $_POST['prio'];
    $jadwal = $_POST['jadwal'];
    
    $assigned = "";
    if ($_POST['assigned_to'] == "manual") {
        $assigned = mysqli_real_escape_string($conn, $_POST['assigned_manual']);
    } else {
        $assigned = mysqli_real_escape_string($conn, $_POST['assigned_to']);
    }
    
    mysqli_query($conn, "INSERT INTO tasks (user_id, nama_tugas, prioritas, jadwal, assigned_to, status, approval_status) VALUES ($uid, '$nama', '$prio', '$jadwal', '$assigned', 'Pending', 'Belum Selesai')");
    
    // Trigger Notification for Member (if exists in family_members)
    $mem_q = mysqli_query($conn, "SELECT id FROM family_members WHERE nama='$assigned' AND user_id=$uid");
    if (mysqli_num_rows($mem_q) > 0) {
        $mem_data = mysqli_fetch_assoc($mem_q);
        $mid = $mem_data['id'];
        $notif_title = "Tugas Pribadi Baru";
        $notif_msg = "Admin memberikan tugas baru: $nama. Silakan cek di Dashboard Anda.";
        mysqli_query($conn, "INSERT INTO notifications (member_id, title, message) VALUES ($mid, '$notif_title', '$notif_msg')");
    }

    header("Location: tasks.php");
    exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tasks WHERE id=" . (int)$_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks.php?view=$view&sort_time=$sort_time&sort_prio=$sort_prio&status=$filter_status");
    exit();
}

if (isset($_GET['hapus_semua'])) {
    mysqli_query($conn, "DELETE FROM tasks WHERE user_id=$uid");
    header("Location: tasks.php");
    exit();
}

// Build dynamic query
$query = "SELECT * FROM tasks WHERE user_id=$uid";

if ($view == 'saya') {
    $query .= " AND assigned_to='$admin_nama'";
}

if ($filter_status == 'pending') {
    $query .= " AND status='Pending'";
} elseif ($filter_status == 'selesai') {
    $query .= " AND status='Selesai'";
}

$order_clauses = [];
if ($sort_prio == 'on') {
    $order_clauses[] = "CASE WHEN prioritas='Tinggi' THEN 1 WHEN prioritas='Sedang' THEN 2 ELSE 3 END ASC";
}

if ($sort_time == 'oldest') {
    $order_clauses[] = "jadwal ASC";
} else {
    $order_clauses[] = "jadwal DESC";
}

$query .= " ORDER BY " . implode(", ", $order_clauses);
$data = mysqli_query($conn, $query);

// Get family members for dropdown
$members = mysqli_query($conn, "SELECT nama FROM family_members WHERE user_id=$uid ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tugas Pribadi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        function checkAssigned(val) {
            const manualInput = document.getElementById('manual-input-group');
            if (val === 'manual') {
                manualInput.classList.remove('hidden');
                document.getElementsByName('assigned_manual')[0].required = true;
            } else {
                manualInput.classList.add('hidden');
                document.getElementsByName('assigned_manual')[0].required = false;
            }
        }

        function openConfirmModal(id, imgName) {
            document.getElementById('confirm-img').src = '../assets/image/tasks/' + imgName;
            document.getElementById('btn-approve').href = '?action=approve&id=' + id + '&view=<?= $view ?>&status=<?= $filter_status ?>';
            document.getElementById('btn-reject').href = '?action=reject&id=' + id + '&view=<?= $view ?>&status=<?= $filter_status ?>';
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
                <a href="tasks.php" class="sub-tab active">Tugas Pribadi</a>
                <a href="tasks_jadwal.php" class="sub-tab">Tugas Terjadwal</a>
            </div>

            <?php if ($view != 'saya'): ?>
                <div class="flex gap-10 mb-20">
                    <button id="btn-form-tambah" class="btn btn-primary" onclick="toggleForm('form-tambah')">+ Tambah Tugas Pribadi</button>
                    <a href="?hapus_semua=1" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus SEMUA tugas pribadi?')">🗑️ Hapus Semua</a>
                </div>

                <form id="form-tambah" method="POST" class="form-box hidden">
                    <h3>Tambah Tugas Baru</h3>
                    <label>Nama Tugas:</label>
                    <input type="text" name="nama" placeholder="Nama Tugas" required>
                    
                    <label>Prioritas:</label>
                    <select name="prio">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                    
                    <label>Jadwal Pelaksanaan:</label>
                    <input type="date" name="jadwal" required>
                    
                    <label>Ditugaskan Ke:</label>
                    <select name="assigned_to" required onchange="checkAssigned(this.value)">
                        <option value="">-- Pilih Anggota Keluarga --</option>
                        <option value="<?= $_SESSION['nama']; ?>">Saya Sendiri (<?= $_SESSION['nama']; ?>)</option>
                        <?php 
                        mysqli_data_seek($members, 0);
                        while($m = mysqli_fetch_assoc($members)): 
                        ?>
                            <option value="<?= $m['nama']; ?>"><?= $m['nama']; ?></option>
                        <?php endwhile; ?>
                        <option value="manual">-- Input Manual (Belum Punya Akun) --</option>
                    </select>

                    <div id="manual-input-group" class="hidden mt-15">
                        <label>Nama Orang (Input Manual):</label>
                        <input type="text" name="assigned_manual" placeholder="Masukkan nama orang...">
                    </div>
                    
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Tugas</button>
                    <button type="button" class="btn btn-danger" onclick="toggleForm('form-tambah')">Batal</button>
                </form>
            <?php else: ?>
                <div class="mb-20">
                    <a href="tasks.php" class="btn btn-secondary">← Kembali ke Semua Tugas</a>
                </div>
            <?php endif; ?>

            <h3><?= $view == 'saya' ? 'Daftar Tugas Saya' : 'Daftar Semua Tugas Pribadi'; ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Tugas</th>
                        <th>Prioritas</th>
                        <th>Jadwal</th>
                        <th>Ditugaskan Ke</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <?php 
                            $status = $row['status'];
                            $approval = $row['approval_status'];
                        ?>
                        <tr>
                            <td><?= $row['nama_tugas']; ?></td>
                            <td><?= $row['prioritas']; ?></td>
                            <td><?= date('d M Y', strtotime($row['jadwal'])); ?></td>
                            <td><?= $row['assigned_to']; ?></td>
                            <td>
                                <?php if ($approval == 'Menunggu Persetujuan'): ?>
                                    <span class="text-orange">⏳ Menunggu Konfirmasi</span>
                                <?php else: ?>
                                    <span class="<?= $status == 'Selesai' ? 'text-green' : 'text-red'; ?>">
                                        <?= $status; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($approval == 'Menunggu Persetujuan'): ?>
                                    <button onclick="openConfirmModal(<?= $row['id']; ?>, '<?= $row['bukti_foto']; ?>')" class="btn btn-secondary bg-green-success">Konfirmasi</button>
                                <?php elseif ($status == 'Pending'): ?>
                                    <a href="?action=done&id=<?= $row['id']; ?>&view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>&sort_prio=<?= $sort_prio ?>" class="btn btn-primary" title="Tandai Selesai">✓</a>
                                <?php endif; ?>

                                <a href="?hapus=<?= $row['id']; ?>&view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>&sort_prio=<?= $sort_prio ?>" class="btn btn-danger">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($data) == 0): ?>
                        <tr><td colspan="6" class="text-center">Tidak ada tugas ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-sidebar">
            <div class="filter-group">
                <h4>Status Tugas</h4>
                <div class="filter-btn-group">
                    <a href="?view=<?= $view ?>&status=all&sort_time=<?= $sort_time ?>&sort_prio=<?= $sort_prio ?>" class="btn-filter <?= $filter_status == 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="?view=<?= $view ?>&status=pending&sort_time=<?= $sort_time ?>&sort_prio=<?= $sort_prio ?>" class="btn-filter <?= $filter_status == 'pending' ? 'active' : '' ?>">Belum Selesai</a>
                    <a href="?view=<?= $view ?>&status=selesai&sort_time=<?= $sort_time ?>&sort_prio=<?= $sort_prio ?>" class="btn-filter <?= $filter_status == 'selesai' ? 'active' : '' ?>">Selesai</a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Urutan Jadwal</h4>
                <div class="filter-btn-group">
                    <a href="?view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=newest&sort_prio=<?= $sort_prio ?>" class="btn-filter <?= $sort_time == 'newest' ? 'active' : '' ?>">Terbaru (Jadwal Jauh)</a>
                    <a href="?view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=oldest&sort_prio=<?= $sort_prio ?>" class="btn-filter <?= $sort_time == 'oldest' ? 'active' : '' ?>">Terlama (Jadwal Dekat)</a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Prioritas</h4>
                <div class="filter-btn-group">
                    <a href="?view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>&sort_prio=on" class="btn-filter <?= $sort_prio == 'on' ? 'active' : '' ?>">Prioritas Tinggi di Atas</a>
                    <a href="?view=<?= $view ?>&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>&sort_prio=off" class="btn-filter <?= $sort_prio == 'off' ? 'active' : '' ?>">Normal</a>
                </div>
            </div>
            
            <a href="tasks.php?view=<?= $view ?>" class="btn btn-secondary w-100 text-center">Reset Filter</a>
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
    <script src="../assets/js/form-toggle.js"></script>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>