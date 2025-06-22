<?php
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Ambil donatur dan penerima untuk dropdown
$donatur_list = $conn->query('SELECT id_donatur, nama FROM donatur ORDER BY nama')->fetchAll(PDO::FETCH_ASSOC);
$penerima_list = $conn->query('SELECT id_penerima, nama_penerima FROM penerima ORDER BY nama_penerima')->fetchAll(PDO::FETCH_ASSOC);

// Proses hapus donasi
$error = '';
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        $stmt = $conn->prepare('DELETE FROM donasi WHERE id_donasi = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            header('Location: donasi.php?msg=hapus_sukses');
            exit();
        } else {
            $error = 'Data donasi tidak ditemukan atau gagal dihapus.';
        }
    } catch (PDOException $e) {
        $error = 'Gagal menghapus donasi: ' . $e->getMessage();
    }
}

// Proses edit donasi
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM donasi WHERE id_donasi = ?');
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Proses tambah/edit donasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_donatur = $_POST['id_donatur'] ?? '';
    $id_penerima = $_POST['id_penerima'] ?? '';
    $jenis_donasi = $_POST['jenis_donasi'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tanggal = $_POST['tanggal_donasi'] ?? '';
    $id_edit = $_POST['id_edit'] ?? '';
    if ($id_donatur && $id_penerima && $jenis_donasi && $jumlah && $tanggal) {
        try {
            if ($id_edit) {
                $stmt = $conn->prepare('UPDATE donasi SET id_donatur=?, id_penerima=?, jenis_donasi=?, jumlah=?, keterangan=?, tanggal_donasi=? WHERE id_donasi=?');
                $stmt->execute([$id_donatur, $id_penerima, $jenis_donasi, $jumlah, $keterangan, $tanggal, $id_edit]);
                header('Location: donasi.php?msg=edit_sukses');
                exit();
            } else {
                $stmt = $conn->prepare('INSERT INTO donasi (id_donatur, id_penerima, jenis_donasi, jumlah, keterangan, tanggal_donasi) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$id_donatur, $id_penerima, $jenis_donasi, $jumlah, $keterangan, $tanggal]);
                header('Location: donasi.php?msg=tambah_sukses');
                exit();
            }
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan donasi: ' . $e->getMessage();
        }
    } else {
        $error = 'Semua field wajib diisi!';
    }
}
// Ambil data donasi (join donatur dan penerima)
$sql = "SELECT d.id_donasi, don.nama AS nama_donatur, pen.nama_penerima, d.jenis_donasi, d.jumlah, d.keterangan, d.tanggal_donasi
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
        /* Form styling */
        form {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            margin-bottom: 30px;
            font-family: Arial, Helvetica, sans-serif;
        }
        form label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="number"],
        form input[type="datetime-local"],
        form textarea,
        form select {
            width: 100%;
            padding: 7px 10px;
            margin-bottom: 12px;
            border: 1px solid #bbb;
            border-radius: 4px;
            font-size: 1em;
            font-family: Arial, Helvetica, sans-serif;
        }
        form textarea {
            min-height: 60px;
        }
        form button, form a {
            background: #1976d2;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            margin-right: 8px;
            transition: background 0.2s;
        }
        form button:hover, form a:hover {
            background: #1256a0;
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
    <h2><?php echo $edit_data ? 'Edit Donasi' : 'Tambah Donasi'; ?></h2>
    <?php if ($error): ?>
        <p style="color:red;"> <?php echo htmlspecialchars($error); ?> </p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id_donasi'] ?? ''; ?>">
        <label>Donatur*:<br>
            <select name="id_donatur" required>
                <option value="">--Pilih Donatur--</option>
                <?php foreach($donatur_list as $d): ?>
                    <option value="<?php echo $d['id_donatur']; ?>" <?php if(($edit_data['id_donatur'] ?? '')==$d['id_donatur']) echo 'selected'; ?>><?php echo htmlspecialchars($d['nama']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>
        <label>Penerima*:<br>
            <select name="id_penerima" required>
                <option value="">--Pilih Penerima--</option>
                <?php foreach($penerima_list as $p): ?>
                    <option value="<?php echo $p['id_penerima']; ?>" <?php if(($edit_data['id_penerima'] ?? '')==$p['id_penerima']) echo 'selected'; ?>><?php echo htmlspecialchars($p['nama_penerima']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>
        <label>Jenis Donasi*:<br>
            <select name="jenis_donasi" required>
                <option value="">--Pilih Jenis--</option>
                <option value="barang" <?php if(($edit_data['jenis_donasi'] ?? '')=='barang') echo 'selected'; ?>>Barang</option>
                <option value="uang" <?php if(($edit_data['jenis_donasi'] ?? '')=='uang') echo 'selected'; ?>>Uang</option>
                <option value="makanan" <?php if(($edit_data['jenis_donasi'] ?? '')=='makanan') echo 'selected'; ?>>Makanan</option>
            </select>
        </label><br><br>
        <label>Jumlah*:<br><input type="number" name="jumlah" step="0.01" required value="<?php echo htmlspecialchars($edit_data['jumlah'] ?? ''); ?>"></label><br><br>
        <label>Keterangan:<br><textarea name="keterangan"><?php echo htmlspecialchars($edit_data['keterangan'] ?? ''); ?></textarea></label><br><br>
        <label>Tanggal Donasi*:<br><input type="datetime-local" name="tanggal_donasi" required value="<?php echo isset($edit_data['tanggal_donasi']) ? date('Y-m-d\TH:i', strtotime($edit_data['tanggal_donasi'])) : ''; ?>"></label><br><br>
        <button type="submit"><?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Donasi'; ?></button>
        <?php if ($edit_data): ?>
            <a href="donasi.php">Batal</a>
        <?php endif; ?>
    </form>
    <h2>Data Donasi</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Donatur</th>
            <th>Penerima</th>
            <th>Jenis Donasi</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Tanggal Donasi</th>
            <th>Aksi</th>
        </tr>
        <?php foreach($data as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id_donasi']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_donatur']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_donasi']); ?></td>
            <td>Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
            <td><?php echo htmlspecialchars($row['tanggal_donasi']); ?></td>
            <td>
                <a href="donasi.php?edit=<?php echo $row['id_donasi']; ?>">Edit</a> |
                <a href="donasi.php?hapus=<?php echo $row['id_donasi']; ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
