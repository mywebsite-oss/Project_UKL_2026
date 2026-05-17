<?php
session_start();
include '../includes/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}
$uid = $_SESSION['user_id'];

if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    mysqli_query($conn, "UPDATE tasks SET status = IF(status='Pending', 'Selesai', 'Pending') WHERE id=$id AND user_id=$uid");
    header("Location: tasks.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $prio = $_POST['prio'];
    $jadwal = $_POST['jadwal'];
    $assigned = mysqli_real_escape_string($conn, $_POST['assigned_to']);
    mysqli_query($conn, "INSERT INTO tasks (user_id, nama_tugas, prioritas, jadwal, assigned_to, status) VALUES ($uid, '$nama', '$prio', '$jadwal', '$assigned', 'Pending')");
    header("Location: tasks.php");
    exit();
}
if (isset($_GET['hapus'])) {
    mysqli_query($conn, "DELETE FROM tasks WHERE id=" . $_GET['hapus'] . " AND user_id=$uid");
    header("Location: tasks.php");
    exit();
}
$data = mysqli_query($conn, "SELECT * FROM tasks WHERE user_id=$uid ORDER BY jadwal DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tugas Manual</title>
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
            <a href="items.php">Barang</a>
            <a href="tasks.php" class="active">Tugas</a>
        </div>
        <div class="navbar-user">
            <span>Halo, <?= $_SESSION['nama']; ?></span>
            <img src="../assets/image/users/<?= $_SESSION['foto'] ?? 'default.png'; ?>" class="profile-pic">
            <a href="../logout.php">Keluar</a>
        </div>
    </div>
    <div class="container">
        <h2>Tugas</h2>
        <div class="nav-tabs">
            <a href="tasks.php" class="nav-tab active">Semua Tugas</a>
            <a href="tasks_anggota.php" class="nav-tab">Pembagian Tugas</a>
        </div>
        <div class="sub-tabs">
            <a href="tasks.php" class="sub-tab active">Tugas Manual</a>
            <a href="tasks_jadwal.php" class="sub-tab">Tugas Terjadwal</a>
        </div>

        <form method="POST" class="form-task">
            <input type="text" name="nama" placeholder="Nama Tugas" required class="input-task-inline">
            <select name="prio" class="input-task-inline">
                <option value="Rendah">Rendah</option>
                <option value="Sedang">Sedang</option>
                <option value="Tinggi">Tinggi</option>
            </select>
            <input type="date" name="jadwal" required class="input-task-inline">
            <input type="text" name="assigned_to" placeholder="Ditugaskan Ke (mis: Ibu)" required class="input-task-inline">
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </form>

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
                    <tr>
                        <td><?= $row['nama_tugas']; ?></td>
                        <td><?= $row['prioritas']; ?></td>
                        <td><?= $row['jadwal']; ?></td>
                        <td><?= $row['assigned_to']; ?></td>
                        <td>
                            <span style="color: <?= $row['status'] == 'Selesai' ? 'green' : 'red'; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Pending'): ?>
                                <a href="?toggle_status=<?= $row['id']; ?>" class="btn btn-primary">✓</a>
                            <?php endif; ?>

                            <a href="?hapus=<?= $row['id']; ?>"
                                class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>