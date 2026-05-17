CREATE DATABASE smart_home;
USE smart_home;


CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nama_rumah_tangga VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

CREATE TABLE IF NOT EXISTS family_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role_dalam_keluarga VARCHAR(50) DEFAULT 'Anggota',
    status TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)

CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) DEFAULT 'Lainnya',
    lokasi_detail VARCHAR(200) NOT NULL,
    penanggung_jawab VARCHAR(100) DEFAULT 'Belum Ditentukan',
    catatan TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_tugas VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    prioritas ENUM('Rendah', 'Sedang', 'Tinggi') DEFAULT 'Sedang',
    assigned_to VARCHAR(100) DEFAULT 'Belum Ditugaskan',
    jadwal DATE,
    deadline DATE,
    status ENUM('Pending', 'Progress', 'Selesai', 'Dibatalkan') DEFAULT 'Pending',
    repeat_type ENUM('Sekali', 'Harian', 'Mingguan', 'Bulanan') DEFAULT 'Sekali',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)

CREATE TABLE IF NOT EXISTS task_pool (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_tugas VARCHAR(150) NOT NULL,
    durasi_menit INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assigned_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    assigned_to VARCHAR(100) NOT NULL,
    nama_tugas VARCHAR(150) NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('Pending','Selesai') DEFAULT 'Pending',
    is_random TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;