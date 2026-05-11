<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <title>Smart Home Organizer</title>
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
            <a href="index.php" class="active">Home</a>
            <a href="about.php">Tentang</a>
            <a href="help.php">Bantuan</a>
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
    <div class="hero">
        <div class="container">
            <h1>Selamat Datang di Smart Home Organizer</h1>
            <p class="hero-subtitle">Temukan barang Anda lebih cepat. Atur rumah Anda lebih cerdas.</p>
            <p class="hero-desc">Pelacakan barang rumah tangga dan manajemen tugas yang sederhana untuk keluarga. Jaga rumah Anda tetap teratur dan bebas stres.</p>
            <a href="register.php" class="btn btn-primary btn-large">Mulai Sekarang</a>
        </div>
    </div>

    <!-- FEATURES -->
    <div class="features">
        <div class="container">
            <h2 class="section-title">Fitur Utama</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>Pelacakan Barang</h3>
                    <p>Pantau setiap barang di rumah. Jangan pernah lagi bertanya "di mana kita menaruh itu?" dengan sistem pencatatan lokasi yang jelas.</p>
                </div>
                <div class="feature-card">
                    <h3>Manajemen Tugas</h3>
                    <p>Bagikan pekerjaan rumah ke anggota keluarga dengan mudah. Acak tugas secara adil dan jaga tanggung jawab bersama.</p>
                </div>
                <div class="feature-card">
                    <h3>Berbagi Keluarga</h3>
                    <p>Semua anggota keluarga bisa melihat tugas mereka masing-masing. Pembaruan status real-time agar semua orang terinformasi.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="cta">
        <div class="container">
            <a href="register.php" class="btn btn-primary btn-large">Daftar Gratis</a>
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