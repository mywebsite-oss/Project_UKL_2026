<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <title>Tentang Kami</title>
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
            <a href="about.php" class="active">Tentang</a>
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
    <div class="hero about-hero">
        <div class="container">
            <h1>Tentang Smart Home Organizer</h1>
            <p class="hero-subtitle">Membantu keluarga tetap teratur dan harmonis.</p>
            <p class="hero-desc">Aplikasi sederhana untuk manajemen barang dan tugas rumah tangga yang dirancang untuk membawa keteraturan dan kolaborasi ke setiap rumah tangga.</p>
        </div>
    </div>

    <!-- FEATURES SECTION -->
    <div class="features">
        <div class="container">
            <h2 class="section-title">Fitur Utama Kami</h2>
            <p class="section-subtitle">Dirancang untuk membawa keteraturan dan kolaborasi ke setiap rumah tangga.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <h3>Atur Segalanya</h3>
                    <p>Jangan pernah kehilangan jejak isi lemari dapur, gudang, atau loteng lagi. Inventaris digital yang dapat diakses dan diperbarui oleh semua orang.</p>
                </div>

                <div class="feature-card">
                    <h3>Berbagi Tugas</h3>
                    <p>Ucapkan selamat tinggal pada omelan. Tetapkan, lacak, dan selesaikan tugas rumah melalui dashboard bersama yang membuat semua orang tetap terinformasi.</p>
                </div>

                <div class="feature-card">
                    <h3>Adil untuk Semua</h3>
                    <p>Distribusi tugas cerdas kami memastikan pembagian tanggung jawab yang adil berdasarkan ketersediaan dan preferensi masing-masing anggota.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA SECTION -->
    <div class="cta">
        <div class="container">
            <a href="register.php" class="btn btn-primary btn-large">Mulai Sekarang</a>
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
    <script src="assets/js/navbar.js"></script>
</body>

</html>