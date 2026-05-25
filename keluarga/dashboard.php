<?php
session_start();
include '../includes/koneksi.php';
include_once '../includes/notifications.php';
include_once '../includes/account_drawer.php';

if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$member_id = $_SESSION['member_id'];
$nama = $_SESSION['nama'];

$tab = $_GET['tab'] ?? 'terjadwal';
$filter_status = $_GET['status'] ?? 'all';
$sort_time = $_GET['sort_time'] ?? 'oldest';

// Handle Upload Bukti Foto
if (isset($_POST['upload_bukti'])) {
    $task_id = (int)$_POST['task_id'];
    $task_type = $_POST['task_type']; // 'terjadwal' or 'pribadi'
    
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES['bukti']['size'] > $max_size) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.'); window.location='dashboard.php?tab=$task_type';</script>";
            exit();
        }

        $upload_dir = '../assets/image/tasks/';
        
        // Pastikan folder ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $foto_bukti = uniqid() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $foto_bukti)) {
            $task_name = "";
            if ($task_type == 'terjadwal') {
                $t_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_tugas FROM assigned_tasks WHERE id=$task_id"));
                $task_name = $t_info['nama_tugas'];
                mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Menunggu Persetujuan', bukti_foto='$foto_bukti' WHERE id=$task_id AND assigned_to='$nama'");
            } else {
                $t_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_tugas FROM tasks WHERE id=$task_id"));
                $task_name = $t_info['nama_tugas'];
                mysqli_query($conn, "UPDATE tasks SET approval_status='Menunggu Persetujuan', bukti_foto='$foto_bukti' WHERE id=$task_id AND assigned_to='$nama'");
            }
            
            // Trigger Notification for Admin
            $notif_title = "Permintaan Persetujuan";
            $notif_msg = "Member Anda $nama Meminta Persetujuan Penyelesaian Tugas $task_type: $task_name";
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message) VALUES ($admin_id, '$notif_title', '$notif_msg')");

            header("Location: dashboard.php?tab=$task_type&status=$filter_status&sort_time=$sort_time");
            exit();
        } else {
            echo "<script>alert('Gagal mengunggah gambar ke server.');</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan pada file yang diunggah.');</script>";
    }
}

// Build Queries
$order_sql = ($sort_time == 'newest') ? "DESC" : "ASC";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if ($tab == 'terjadwal') {
    $query = "SELECT * FROM assigned_tasks WHERE assigned_to='$nama' AND user_id=$admin_id";
    if ($filter_status == 'pending') $query .= " AND (approval_status IS NULL OR approval_status='Belum Selesai')";
    elseif ($filter_status == 'selesai') $query .= " AND approval_status='Disetujui'";
    $query .= " ORDER BY tanggal $order_sql";
} else {
    $query = "SELECT * FROM tasks WHERE assigned_to='$nama' AND user_id=$admin_id";
    if ($filter_status == 'pending') $query .= " AND approval_status='Belum Selesai'";
    elseif ($filter_status == 'selesai') $query .= " AND approval_status='Disetujui'";
    $query .= " ORDER BY jadwal $order_sql";
}

$total_query = str_replace("SELECT *", "SELECT COUNT(*)", $query);
$total_records = mysqli_fetch_array(mysqli_query($conn, $total_query))[0];
$total_pages = ceil($total_records / $limit);

$query .= " LIMIT $limit OFFSET $offset";
$data_tasks = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Keluarga</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        function openUploadModal(id, type) {
            document.getElementById('task_id').value = id;
            document.getElementById('task_type').value = type;
            document.getElementById('uploadModal').classList.remove('hidden');
        }
        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }
    </script>
</head>

<body>
    <div class="navbar navbar-dashboard">
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="dashboard.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-user">
            <span>Halo, <?= $nama; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <?php renderNotifications($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
            <a href="javascript:void(0)" onclick="openAccountDrawer()">Akun</a>
        </div>
    </div>

    <!-- Layout Wrapper -->
    <div class="flex-container">
        
        <!-- CONTAINER DAFTAR TUGASKU -->
        <div class="container main-box">
            <h2>Daftar Tugasku</h2>

            <div class="nav-tabs">
                <a href="?tab=terjadwal&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>" class="nav-tab <?= $tab == 'terjadwal' ? 'active' : ''; ?>">Tugas Terjadwal</a>
                <a href="?tab=pribadi&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>" class="nav-tab <?= $tab == 'pribadi' ? 'active' : ''; ?>">Tugas Pribadi</a>
            </div>

            <?php if ($tab == 'terjadwal'): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Tugas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_tasks) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($data_tasks)):
                                $status = $row['approval_status'] ?? 'Belum Selesai';
                            ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                                    <td><?= $row['nama_tugas']; ?></td>
                                    <td>
                                        <?php
                                        if ($status == 'Disetujui') echo "<span class='text-green'>✅ Selesai</span>";
                                        elseif ($status == 'Menunggu Persetujuan') echo "<span class='text-orange'>⏳ Menunggu</span>";
                                        else echo "<span class='text-red'>❌ Belum</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($status == 'Belum Selesai' || $status == ''): ?>
                                            <?php
                                            $today = date('Y-m-d');
                                            $task_date = $row['tanggal'];
                                            if ($task_date == $today): ?>
                                                <button onclick="openUploadModal(<?= $row['id']; ?>, 'terjadwal')" class="btn btn-primary">Selesai</button>
                                            <?php elseif ($task_date < $today): ?>
                                                <button onclick="openUploadModal(<?= $row['id']; ?>, 'terjadwal')" class="btn btn-danger">Selesai</button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" style="opacity: 0.5; pointer-events: none;" disabled>Selesai</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Tidak ada tugas terjadwal.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Jadwal</th>
                            <th>Nama Tugas</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_tasks) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($data_tasks)):
                                $status = $row['approval_status'] ?? 'Belum Selesai';
                            ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['jadwal'])); ?></td>
                                    <td><?= $row['nama_tugas']; ?></td>
                                    <td><?= $row['prioritas']; ?></td>
                                    <td>
                                        <?php
                                        if ($status == 'Disetujui') echo "<span class='text-green'>✅ Selesai</span>";
                                        elseif ($status == 'Menunggu Persetujuan') echo "<span class='text-orange'>⏳ Menunggu</span>";
                                        else echo "<span class='text-red'>❌ Belum</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($status == 'Belum Selesai'): ?>
                                            <?php
                                            $today = date('Y-m-d');
                                            $task_date = $row['jadwal'];
                                            if ($task_date == $today): ?>
                                                <button onclick="openUploadModal(<?= $row['id']; ?>, 'pribadi')" class="btn btn-primary">Selesai</button>
                                            <?php elseif ($task_date < $today): ?>
                                                <button onclick="openUploadModal(<?= $row['id']; ?>, 'pribadi')" class="btn btn-danger">Selesai</button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" style="opacity: 0.5; pointer-events: none;" disabled>Selesai</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Tidak ada tugas pribadi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

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

        <!-- SIDEBAR PENGURUT -->
        <div class="sticky-sidebar">
            <div class="filter-group">
                <h4>Status Tugas</h4>
                <div class="filter-btn-group">
                    <a href="?tab=<?= $tab ?>&status=all&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_status == 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="?tab=<?= $tab ?>&status=pending&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_status == 'pending' ? 'active' : '' ?>">Belum Selesai</a>
                    <a href="?tab=<?= $tab ?>&status=selesai&sort_time=<?= $sort_time ?>" class="btn-filter <?= $filter_status == 'selesai' ? 'active' : '' ?>">Selesai</a>
                </div>
            </div>

            <div class="filter-group">
                <h4>Urutan Waktu</h4>
                <div class="filter-btn-group">
                    <a href="?tab=<?= $tab ?>&status=<?= $filter_status ?>&sort_time=oldest" class="btn-filter <?= $sort_time == 'oldest' ? 'active' : '' ?>">Terlama (Dekat)</a>
                    <a href="?tab=<?= $tab ?>&status=<?= $filter_status ?>&sort_time=newest" class="btn-filter <?= $sort_time == 'newest' ? 'active' : '' ?>">Terbaru (Jauh)</a>
                </div>
            </div>
            
            <a href="dashboard.php?tab=<?= $tab ?>" class="btn btn-secondary w-100 text-center">Reset Filter</a>
        </div>
    </div>

    <!-- UPLOAD MODAL -->
    <div id="uploadModal" class="modal hidden">
        <div class="modal-content">
            <h3>Kirim Bukti Tugas Selesai</h3>
            <p>Silakan unggah foto sebagai bukti bahwa Anda telah menyelesaikan tugas ini.</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="task_id" id="task_id">
                <input type="hidden" name="task_type" id="task_type">
                
                <div style="margin: 20px 0; text-align: left;">
                    <label style="display:block; font-weight:bold; margin-bottom:10px;">Pilih Gambar:</label>
                    <input type="file" name="bukti" accept="image/*" required>
                </div>
                
                <div class="flex gap-10" style="justify-content: center;">
                    <button type="submit" name="upload_bukti" class="btn btn-primary">Kirim Bukti</button>
                    <button type="button" class="btn btn-danger" onclick="closeUploadModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <?php renderAccountDrawer($conn, $_SESSION['user_id'] ?? null, $_SESSION['member_id'] ?? null, $_SESSION['role']); ?>
    <script src="../assets/js/navbar.js"></script>
</body>

</html>