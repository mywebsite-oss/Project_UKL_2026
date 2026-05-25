<?php
include_once 'koneksi.php';

// Logic for handling AJAX/GET requests
if (isset($_GET['action'])) {
    session_start();
    $uid = $_SESSION['user_id'] ?? null;
    $mid = $_SESSION['member_id'] ?? null;
    $role = $_SESSION['role'] ?? null;

    if ($_GET['action'] == 'mark_read') {
        if ($role == 'member') {
            mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE member_id=$mid");
        } else {
            mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id=$uid");
        }
        echo "success";
        exit();
    }

    if ($_GET['action'] == 'delete_all') {
        $type = $_GET['type']; // 'read' or 'unread'
        $is_read = ($type == 'read') ? 1 : 0;
        if ($role == 'member') {
            mysqli_query($conn, "DELETE FROM notifications WHERE member_id=$mid AND is_read=$is_read");
        } else {
            mysqli_query($conn, "DELETE FROM notifications WHERE user_id=$uid AND is_read=$is_read");
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

function checkOverdueTasks($conn) {
    $today = date('Y-m-d');

    // 1. Check personal tasks (tasks)
    $q_tasks = mysqli_query($conn, "SELECT * FROM tasks WHERE status='Pending' AND jadwal < '$today' AND notified_overdue = 0");
    if ($q_tasks) {
        while ($t = mysqli_fetch_assoc($q_tasks)) {
            $tid = $t['id'];
            $user_id = $t['user_id'];
            $nama_tugas = mysqli_real_escape_string($conn, $t['nama_tugas']);
            $assigned_to = $t['assigned_to'];
            $jadwal = $t['jadwal'];

            $u_q = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE id=$user_id");
            $u_data = mysqli_fetch_assoc($u_q);
            $user_nama = $u_data['nama_lengkap'] ?? '';

            if ($assigned_to == $user_nama) {
                $title = "Tugas Terlambat!";
                $msg = "Tugas pribadi Anda: \"$nama_tugas\" telah melewati batas waktu (Jadwal: " . date('d M Y', strtotime($jadwal)) . "). Segera selesaikan!";
                mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, is_late) VALUES ($user_id, '$title', '$msg', 1)");
            } else {
                $m_q = mysqli_query($conn, "SELECT id FROM family_members WHERE nama='" . mysqli_real_escape_string($conn, $assigned_to) . "' AND user_id=$user_id");
                if (mysqli_num_rows($m_q) > 0) {
                    $m_data = mysqli_fetch_assoc($m_q);
                    $member_id = $m_data['id'];

                    $title = "Tugas Terlambat!";
                    $msg = "Tugas pribadi Anda: \"$nama_tugas\" telah melewati batas waktu (Jadwal: " . date('d M Y', strtotime($jadwal)) . "). Segera selesaikan!";
                    mysqli_query($conn, "INSERT INTO notifications (member_id, title, message, is_late) VALUES ($member_id, '$title', '$msg', 1)");

                    $title_user = "Anggota Telat Tugas";
                    $msg_user = "Member $assigned_to belum menyelesaikan tugas pribadi: \"$nama_tugas\" (Jadwal: " . date('d M Y', strtotime($jadwal)) . ").";
                    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, is_late) VALUES ($user_id, '$title_user', '$msg_user', 1)");
                }
            }
            mysqli_query($conn, "UPDATE tasks SET notified_overdue = 1 WHERE id = $tid");
        }
    }

    // 2. Check scheduled tasks (assigned_tasks)
    $q_assigned = mysqli_query($conn, "SELECT * FROM assigned_tasks WHERE status='Pending' AND tanggal < '$today' AND notified_overdue = 0");
    if ($q_assigned) {
        while ($t = mysqli_fetch_assoc($q_assigned)) {
            $tid = $t['id'];
            $user_id = $t['user_id'];
            $nama_tugas = mysqli_real_escape_string($conn, $t['nama_tugas']);
            $assigned_to = $t['assigned_to'];
            $tanggal = $t['tanggal'];

            $u_q = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE id=$user_id");
            $u_data = mysqli_fetch_assoc($u_q);
            $user_nama = $u_data['nama_lengkap'] ?? '';

            if ($assigned_to == $user_nama) {
                $title = "Tugas Terlambat!";
                $msg = "Tugas terjadwal Anda: \"$nama_tugas\" telah melewati batas waktu (Tanggal: " . date('d M Y', strtotime($tanggal)) . "). Segera selesaikan!";
                mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, is_late) VALUES ($user_id, '$title', '$msg', 1)");
            } else {
                $m_q = mysqli_query($conn, "SELECT id FROM family_members WHERE nama='" . mysqli_real_escape_string($conn, $assigned_to) . "' AND user_id=$user_id");
                if (mysqli_num_rows($m_q) > 0) {
                    $m_data = mysqli_fetch_assoc($m_q);
                    $member_id = $m_data['id'];

                    $title = "Tugas Terlambat!";
                    $msg = "Tugas terjadwal Anda: \"$nama_tugas\" telah melewati batas waktu (Tanggal: " . date('d M Y', strtotime($tanggal)) . "). Segera selesaikan!";
                    mysqli_query($conn, "INSERT INTO notifications (member_id, title, message, is_late) VALUES ($member_id, '$title', '$msg', 1)");

                    $title_user = "Anggota Telat Tugas";
                    $msg_user = "Member $assigned_to belum menyelesaikan tugas terjadwal: \"$nama_tugas\" (Tanggal: " . date('d M Y', strtotime($tanggal)) . ").";
                    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, is_late) VALUES ($user_id, '$title_user', '$msg_user', 1)");
                }
            }
            mysqli_query($conn, "UPDATE assigned_tasks SET notified_overdue = 1 WHERE id = $tid");
        }
    }
}

// Function to get notification count
function getUnreadCount($conn, $uid, $mid, $role) {
    checkOverdueTasks($conn);
    if ($role == 'member') {
        $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE member_id=$mid AND is_read=0");
    } else {
        $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE user_id=$uid AND is_read=0");
    }
    $res = mysqli_fetch_assoc($q);
    return $res['total'];
}

// Function to render notification dropdown
function renderNotifications($conn, $uid, $mid, $role) {
    checkOverdueTasks($conn);
    if ($role == 'member') {
        $unread = mysqli_query($conn, "SELECT * FROM notifications WHERE member_id=$mid AND is_read=0 ORDER BY created_at DESC");
        $read = mysqli_query($conn, "SELECT * FROM notifications WHERE member_id=$mid AND is_read=1 ORDER BY created_at DESC");
    } else {
        $unread = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=$uid AND is_read=0 ORDER BY created_at DESC");
        $read = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=$uid AND is_read=1 ORDER BY created_at DESC");
    }
    
    $unread_count = mysqli_num_rows($unread);
    ?>
    <div class="notif-wrapper" id="notif-bell-trigger">
        <span class="notif-bell">🔔</span>
        <?php if ($unread_count > 0): ?>
            <span class="notif-badge"><?= $unread_count ?></span>
        <?php endif; ?>

        <div id="notif-dropdown" class="notif-dropdown hidden" onclick="event.stopPropagation()">
            <div class="notif-header">
                <h4 style="margin:0;">Notifikasi</h4>
            </div>
            <div class="notif-tabs">
                <div class="notif-tab active" id="tab-unread" onclick="switchNotifTab('unread')">Belum Dibaca</div>
                <div class="notif-tab" id="tab-read" onclick="switchNotifTab('read')">Sudah Dibaca</div>
            </div>
            
            <div id="list-unread" class="notif-list">
                <?php if ($unread_count > 0): ?>
                    <?php while($n = mysqli_fetch_assoc($unread)): ?>
                        <div class="notif-item unread <?= ($n['is_late'] ?? 0) == 1 ? 'late' : '' ?>">
                            <h5><?= $n['title'] ?></h5>
                            <p><?= $n['message'] ?></p>
                            <small><?= date('d M, H:i', strtotime($n['created_at'])) ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center" style="padding:20px; color:#94a3b8;">Tidak ada pesan baru.</p>
                <?php endif; ?>
            </div>

            <div id="list-read" class="notif-list hidden">
                <?php if (mysqli_num_rows($read) > 0): ?>
                    <?php while($n = mysqli_fetch_assoc($read)): ?>
                        <div class="notif-item <?= ($n['is_late'] ?? 0) == 1 ? 'late' : '' ?>">
                            <h5><?= $n['title'] ?></h5>
                            <p><?= $n['message'] ?></p>
                            <small><?= date('d M, H:i', strtotime($n['created_at'])) ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center" style="padding:20px; color:#94a3b8;">Belum ada riwayat pesan.</p>
                <?php endif; ?>
            </div>

            <div class="notif-footer">
                <a id="del-unread" href="../includes/notifications.php?action=delete_all&type=unread" class="btn-delete-all" onclick="return confirm('Hapus semua notifikasi belum dibaca?')">🗑️</a>
                <a id="del-read" href="../includes/notifications.php?action=delete_all&type=read" class="btn-delete-all hidden" onclick="return confirm('Hapus semua notifikasi sudah dibaca?')">🗑️</a>
            </div>
        </div>
    </div>

    <script>
        const bell = document.getElementById('notif-bell-trigger');
        const dropdown = document.getElementById('notif-dropdown');
        
        bell.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            
            if (isHidden) {
                // Mark as read when opening
                fetch('../includes/notifications.php?action=mark_read')
                .then(response => response.text())
                .then(data => {
                    // Badge will be removed on next reload, or could be hidden here
                    const badge = document.querySelector('.notif-badge');
                    if(badge) badge.style.display = 'none';
                });
            }
        });

        document.addEventListener('click', () => dropdown.classList.add('hidden'));

        function switchNotifTab(type) {
            const tUnread = document.getElementById('tab-unread');
            const tRead = document.getElementById('tab-read');
            const lUnread = document.getElementById('list-unread');
            const lRead = document.getElementById('list-read');
            const dUnread = document.getElementById('del-unread');
            const dRead = document.getElementById('del-read');

            if (type === 'unread') {
                tUnread.classList.add('active');
                tRead.classList.remove('active');
                lUnread.classList.remove('hidden');
                lRead.classList.add('hidden');
                dUnread.classList.remove('hidden');
                dRead.classList.add('hidden');
            } else {
                tRead.classList.add('active');
                tUnread.classList.remove('active');
                lRead.classList.remove('hidden');
                lUnread.classList.add('hidden');
                dRead.classList.remove('hidden');
                dUnread.classList.add('hidden');
            }
        }
    </script>
    <?php
}
?>