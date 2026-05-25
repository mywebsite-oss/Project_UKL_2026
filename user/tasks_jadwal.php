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
    $id = (int)$_GET['reject'];
    $t_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_tugas, assigned_to FROM assigned_tasks WHERE id=$id AND user_id=$uid"));
    if ($t_info) {
        $task_name = $t_info['nama_tugas'];
        $assigned_to = $t_info['assigned_to'];
        $m_q = mysqli_query($conn, "SELECT id FROM family_members WHERE nama='" . mysqli_real_escape_string($conn, $assigned_to) . "' AND user_id=$uid");
        if (mysqli_num_rows($m_q) > 0) {
            $m_data = mysqli_fetch_assoc($m_q);
            $mid = $m_data['id'];
            $notif_title = "Persetujuan Ditolak";
            $notif_msg = "Permintaan persetujuan untuk tugas terjadwal: \"$task_name\" ditolak oleh Kepala Keluarga. Silakan periksa kembali pekerjaan Anda.";
            mysqli_query($conn, "INSERT INTO notifications (member_id, title, message) VALUES ($mid, '$notif_title', '$notif_msg')");
        }
    }
    mysqli_query($conn, "UPDATE assigned_tasks SET approval_status='Belum Selesai', status='Pending', bukti_foto=NULL WHERE id=$id");
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

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 10;

// Auto-pagination logic for id_focus
$id_focus = isset($_GET['id_focus']) ? (int)$_GET['id_focus'] : 0;
if ($id_focus > 0) {
    $q_all = mysqli_query($conn, $query);
    $all_ids = [];
    while ($r = mysqli_fetch_assoc($q_all)) {
        $all_ids[] = (int)$r['id'];
    }
    $pos = array_search($id_focus, $all_ids);
    if ($pos !== false) {
        $page = ceil(($pos + 1) / $limit);
    }
}

$offset = ($page - 1) * $limit;

$total_query = str_replace("SELECT *", "SELECT COUNT(*)", $query);
$total_records = mysqli_fetch_array(mysqli_query($conn, $total_query))[0];
$total_pages = ceil($total_records / $limit);

$query .= " LIMIT $limit OFFSET $offset";
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
                    <tr class="<?= $row['id'] == $id_focus ? 'row-highlight' : '' ?>">
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
                                    <?php 
                                    $today = date('Y-m-d');
                                    $task_date = $row['tanggal'];
                                    if ($task_date == $today): ?>
                                        <a href="?selesai_saya=<?= $row['id']; ?>&view=saya&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>" class="btn btn-primary" title="Selesai">✓</a>
                                    <?php elseif ($task_date < $today): ?>
                                        <a href="?selesai_saya=<?= $row['id']; ?>&view=saya&status=<?= $filter_status ?>&sort_time=<?= $sort_time ?>" class="btn btn-danger" title="Selesai">✓</a>
                                    <?php else: ?>
                                        <a href="javascript:void(0)" class="btn btn-secondary" style="opacity: 0.5; pointer-events: none; cursor: not-allowed;" title="Selesai">✓</a>
                                    <?php endif; ?>
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
            
            <?php if ($view == 'saya'): ?>
                <div class="mt-15">
                    <a href="tasks_jadwal.php" class="btn btn-secondary">← Kembali ke Semua</a>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px; flex: 1; position: sticky; top: 20px;">
            <div class="admin-sidebar" style="position: static; width: auto; margin: 0;">
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

            <div class="admin-sidebar" style="position: static; width: auto; margin: 0;" id="notif-tugas-container">
                <h4 style="margin-top: 0; margin-bottom: 10px; font-size: 16px; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Notif Tugas</h4>
                <?php
                // Fetch all pending approval tasks
                $q_approvals = mysqli_query($conn, "SELECT * FROM assigned_tasks WHERE user_id=$uid AND approval_status='Menunggu Persetujuan' ORDER BY tanggal ASC");
                $approvals = [];
                while ($app = mysqli_fetch_assoc($q_approvals)) {
                    $approvals[] = $app;
                }
                $total_apps = count($approvals);
                ?>
                
                <?php if ($total_apps > 0): ?>
                    <div class="approval-slides">
                        <?php 
                        $slide_index = 0;
                        for ($i = 0; $i < $total_apps; $i += 3): 
                            $slide_index++;
                        ?>
                            <div class="approval-slide" id="app-slide-<?= $slide_index ?>" style="display: <?= $slide_index == 1 ? 'block' : 'none' ?>;">
                                <?php for ($j = $i; $j < min($i + 3, $total_apps); $j++): 
                                    $app_item = $approvals[$j];
                                ?>
                                    <div style="font-size: 12px; padding: 8px; border-bottom: 1px solid #f1f5f9; background: #fffbeb; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #d97706; text-align: left;">
                                        <strong style="color: #b45309;"><?= htmlspecialchars($app_item['assigned_to']) ?></strong> meminta persetujuan tugas: 
                                        <div style="margin: 4px 0; color: #1e293b; font-weight: bold;"><?= htmlspecialchars($app_item['nama_tugas']) ?></div>
                                        <a href="?id_focus=<?= $app_item['id'] ?>&view=all&status=all" class="btn btn-primary btn-sm" style="padding: 2px 6px; font-size: 10px; line-height: 1; margin-top: 4px; display: inline-block;">Lihat</a>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($total_apps > 3): ?>
                        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; margin-top: 8px;">
                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 6px; font-size: 10px; line-height: 1;" onclick="prevAppSlide()"><</button>
                            <span id="app-slide-indicator" style="font-size: 11px; color: #64748b;">1 / <?= $slide_index ?></span>
                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 6px; font-size: 10px; line-height: 1;" onclick="nextAppSlide()">></button>
                        </div>
                        <script>
                            let currentAppSlide = 1;
                            const totalAppSlides = <?= $slide_index ?>;
                            function showAppSlide(n) {
                                for(let i=1; i<=totalAppSlides; i++) {
                                    document.getElementById('app-slide-'+i).style.display = (i === n) ? 'block' : 'none';
                                }
                                document.getElementById('app-slide-indicator').textContent = n + ' / ' + totalAppSlides;
                            }
                            function nextAppSlide() {
                                if (currentAppSlide < totalAppSlides) {
                                    currentAppSlide++;
                                    showAppSlide(currentAppSlide);
                                }
                            }
                            function prevAppSlide() {
                                if (currentAppSlide > 1) {
                                    currentAppSlide--;
                                    showAppSlide(currentAppSlide);
                                }
                            }
                        </script>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="font-size: 12px; color: #64748b; padding: 10px 0; margin: 0; text-align: center;">Tidak ada permintaan persetujuan.</p>
                <?php endif; ?>
            </div>
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