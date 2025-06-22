<?php
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();
if($conn) {
    echo "Koneksi ke database berhasil!<br>";
    // Cek tabel donatur
    $stmt = $conn->query("SHOW TABLES LIKE 'donatur'");
    echo $stmt->rowCount() ? "Tabel donatur ditemukan.<br>" : "Tabel donatur tidak ditemukan.<br>";
    // Cek tabel penerima
    $stmt = $conn->query("SHOW TABLES LIKE 'penerima'");
    echo $stmt->rowCount() ? "Tabel penerima ditemukan.<br>" : "Tabel penerima tidak ditemukan.<br>";
    // Cek tabel donasi
    $stmt = $conn->query("SHOW TABLES LIKE 'donasi'");
    echo $stmt->rowCount() ? "Tabel donasi ditemukan.<br>" : "Tabel donasi tidak ditemukan.<br>";
    // Cek tabel riwayat_donasi
    $stmt = $conn->query("SHOW TABLES LIKE 'riwayat_donasi'");
    echo $stmt->rowCount() ? "Tabel riwayat_donasi ditemukan.<br>" : "Tabel riwayat_donasi tidak ditemukan.<br>";
} else {
    echo "Gagal koneksi ke database.";
}
?>
