<?php
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Hitung jumlah donatur
$total_donatur = $conn->query('SELECT COUNT(*) FROM donatur')->fetchColumn();
// Hitung jumlah total donasi (uang)
$total_donasi = $conn->query('SELECT SUM(jumlah) FROM donasi')->fetchColumn();
if ($total_donasi === null) $total_donasi = 0;

// Ambil seluruh riwayat donasi terbaru (join donatur & penerima)
$sql = "SELECT d.tanggal_donasi, don.nama AS nama_donatur, pen.nama_penerima, d.jenis_donasi, d.jumlah, d.keterangan
        FROM donasi d
        JOIN donatur don ON d.id_donatur = don.id_donatur
        JOIN penerima pen ON d.id_penerima = pen.id_penerima
        ORDER BY d.tanggal_donasi DESC";
$stmt = $conn->query($sql);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
        }
        th {
            background: #1976d2;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        td, th {
            border: 1px solid #bbb;
            padding: 8px;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        tr:hover {
            background: #e3f0fc;
        }
        /* Info box styling */
        .info-box {
            display: inline-block;
            background: #e3f0fc;
            border: 1px solid #1976d2;
            border-radius: 8px;
            padding: 18px 32px;
            margin: 0 18px 24px 0;
            font-size: 1.2em;
            color: #1976d2;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(25,118,210,0.07);
        }
        .info-box b {
            color: #222;
            font-size: 1.3em;
        }
        @media (max-width: 600px) {
            .info-box { display: block; margin-bottom: 18px; }
        }
    </style>
</head>
<body>
    <table border="1" width="100%" style="margin-bottom:20px;">
        <tr>
            <td align="center"><a href="dashboard.php">Dashboard</a></td>
            <td align="center"><a href="donatur.php">Donatur</a></td>
            <td align="center"><a href="penerima.php">Penerima</a></td>
            <td align="center"><a href="donasi.php">Donasi</a></td>
        </tr>
    </table>
    <h2>Dashboard</h2>
    <div class="info-box">Jumlah Donatur: <b><?php echo $total_donatur; ?></b></div>
    <div class="info-box">Jumlah Total Donasi: <b>Rp <?php echo number_format($total_donasi,0,',','.'); ?></b></div>
    <h3>Riwayat Donasi Terbaru</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Tanggal</th>
            <th>Donatur</th>
            <th>Penerima</th>
            <th>Jenis Donasi</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
        <?php foreach($riwayat as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['tanggal_donasi']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_donatur']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_donasi']); ?></td>
            <td>Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
