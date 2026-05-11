<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <title>Pusat Bantuan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- NAVBAR -->
    <div class="navbar">
        <div class="navbar-brand">
            <img src="assets/image/logo.png" alt="Logo" class="logo-img">
            <a href="index.php">Smart Home Organizer</a>
        </div>
        <div class="navbar-menu">
            <a href="index.php">Home</a>
            <a href="about.php">Tentang</a>
            <a href="help.php" class="active">Bantuan</a>
        </div>
        <div class="navbar-user">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Halo, <?= $_SESSION['nama']; ?></span>
                <a href="logout.php">Keluar</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Masuk</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- HERO SECTION -->
    <div class="hero help-hero">
        <div class="container">
            <h1>Bantuan & Panduan</h1>
            <p class="hero-subtitle">Semuanya yang Anda butuhkan agar rumah tangga berjalan lancar.</p>
            <p class="hero-desc">Kami telah menyederhanakan organisasi sehingga Anda dapat fokus pada hal yang penting.</p>
        </div>
    </div>

    <!-- PANDUAN CEPAT -->
    <div class="container" style="padding: 60px 20px;">
        <h2 class="section-title">Panduan Cepat Memulai</h2>

        <div class="guide-cards">
            <div class="guide-card">
                <h3>1. Buat Akun Anda</h3>
                <p>Daftar dalam hitungan detik. Kami hanya butuh beberapa detail untuk menyiapkan ruang pribadi Anda.</p>
            </div>

            <div class="guide-card">
                <h3>2. Tambahkan Barang</h3>
                <p>Atur inventaris ruang Anda dengan mudah. Beri label barang berdasarkan ruangan dan kategori.</p>
            </div>

            <div class="guide-card">
                <h3>3. Ajak Keluarga</h3>
                <p>Bagikan tugas dan tetap sinkron. Tetapkan tugas ke anggota keluarga dan pantau kemajuan bersama.</p>
            </div>
        </div>

        <div class="text-center" style="margin-top: 40px;">
            <p style="font-size: 18px; margin-bottom: 20px;">Masih ada kendala?</p>
            <a href="mailto:uqmanzahidain@gmail.com" class="btn btn-secondary">Hubungi Kami</a>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <h3>Smart Home Organizer</h3>
                <p>Membantu keluarga tetap teratur dan harmonis.</p>

                <!-- SOCIAL MEDIA LINKS -->
                <div class="social-links">
                    <a href="https://instagram.com/luqhid_08" target="_blank" class="social-link" title="Instagram">
                        <img src="assets/image/social/instagram.png" alt="Instagram">
                    </a>
                    <a href="https://wa.me/6282221905487" target="_blank" class="social-link" title="WhatsApp">
                        <img src="assets/image/social/whatsapp.png" alt="WhatsApp">
                    </a>
                    <a href="https://t.me/buasss_hass" target="_blank" class="social-link" title="Telegram">
                        <img src="assets/image/social/telegram.png" alt="Telegram">
                    </a>
                    <a href="mailto:luqmanzahidain@gmail.com" class="social-link" title="Gmail">
                        <img src="assets/image/social/gmail.png" alt="Gmail">
                    </a>
                </div>
            </div>
            <div class="footer-right">
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Ketentuan Layanan</a>
                <a href="help.php">Pusat Bantuan</a>
                <a href="#">Hubungi Kami</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 Smart Home Organizer Inc. SMK Telkom Sidoarjo. Dibuat untuk membantu keluarga tetap teratur.
        </div>
    </footer>
</body>

</html>