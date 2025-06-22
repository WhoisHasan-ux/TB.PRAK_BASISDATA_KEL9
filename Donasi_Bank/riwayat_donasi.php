<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Proses tambah donasi
$error = '';
// Ambil donatur dan penerima untuk dropdown
$donatur_list = $conn->query('SELECT id_donatur, nama FROM donatur ORDER BY nama')->fetchAll(PDO::FETCH_ASSOC);
$penerima_list = $conn->query('SELECT id_penerima, nama_penerima FROM penerima ORDER BY nama_penerima')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_donatur = $_POST['id_donatur'] ?? '';
    $id_penerima = $_POST['id_penerima'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tanggal = $_POST['tanggal_donasi'] ?? '';
    if ($id_donatur && $id_penerima && $jumlah && $tanggal) {
        try {
            $stmt = $conn->prepare('INSERT INTO donasi (id_donatur, id_penerima, jumlah, keterangan, tanggal_donasi) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$id_donatur, $id_penerima, $jumlah, $keterangan, $tanggal]);
            header('Location: riwayat_donasi.php');
            exit();
        } catch (PDOException $e) {
            $error = 'Gagal menambah donasi: ' . $e->getMessage();
        }
    } else {
        $error = 'Semua field wajib diisi!';
    }
}
// Ambil data donasi (join donatur dan penerima)
$sql = "SELECT d.*, don.nama AS nama_donatur, pen.nama_penerima
        FROM donasi d
        JOIN donatur don ON d.id_donatur = don.id_donatur
        JOIN penerima pen ON d.id_penerima = pen.id_penerima
        ORDER BY d.tanggal_donasi DESC";
$data = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Donasi</title>
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
    </style>
</head>
<body>
    <table border="1" width="100%" style="margin-bottom:20px;">
        <tr>
            <td align="center"><a href="dashboard.php">Dashboard</a></td>
            <td align="center"><a href="donatur.php">Donatur</a></td>
            <td align="center"><a href="penerima.php">Penerima</a></td>
            <td align="center"><a href="donasi.php">Donasi</a></td>
            <td align="center"><a href="riwayat_donasi.php">Riwayat Donasi</a></td>
        </tr>
    </table>
    <h2>Tambah Donasi</h2>
    <?php if ($error): ?>
        <p style="color:red;"> <?php echo htmlspecialchars($error); ?> </p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Donatur*:<br>
            <select name="id_donatur" required>
                <option value="">--Pilih Donatur--</option>
                <?php foreach($donatur_list as $d): ?>
                    <option value="<?php echo $d['id_donatur']; ?>"><?php echo htmlspecialchars($d['nama']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>
        <label>Penerima*:<br>
            <select name="id_penerima" required>
                <option value="">--Pilih Penerima--</option>
                <?php foreach($penerima_list as $p): ?>
                    <option value="<?php echo $p['id_penerima']; ?>"><?php echo htmlspecialchars($p['nama_penerima']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>
        <label>Jumlah*:<br><input type="number" name="jumlah" step="0.01" required></label><br><br>
        <label>Keterangan:<br><textarea name="keterangan"></textarea></label><br><br>
        <label>Tanggal Donasi*:<br><input type="datetime-local" name="tanggal_donasi" required></label><br><br>
        <button type="submit">Tambah Donasi</button>
    </form>
    <h2>Data Donasi</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Donatur</th>
            <th>Penerima</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
        <?php foreach($data as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id_donasi']); ?></td>
            <td><?php echo htmlspecialchars($row['tanggal_donasi']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_donatur']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
            <td>Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
