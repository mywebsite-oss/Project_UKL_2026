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

// Function to get notification count
function getUnreadCount($conn, $uid, $mid, $role) {
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
                        <div class="notif-item unread">
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
                        <div class="notif-item">
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