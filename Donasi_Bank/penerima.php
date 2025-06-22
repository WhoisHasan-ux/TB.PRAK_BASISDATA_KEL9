<?php
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Proses hapus penerima
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        $stmt = $conn->prepare('DELETE FROM penerima WHERE id_penerima = ?');
        $stmt->execute([$id]);
        header('Location: penerima.php');
        exit();
    } catch (PDOException $e) {
        $error = 'Gagal menghapus penerima: ' . $e->getMessage();
    }
}

// Proses edit penerima
$error = '';
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM penerima WHERE id_penerima = ?');
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_penerima'] ?? '');
    $jenis = trim($_POST['jenis_penerima'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $no_telp = trim($_POST['no_telp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $id_edit = $_POST['id_edit'] ?? '';
    if ($nama && $jenis) {
        try {
            if ($id_edit) {
                $stmt = $conn->prepare('UPDATE penerima SET nama_penerima=?, jenis_penerima=?, deskripsi=?, alamat=?, no_telp=?, email=? WHERE id_penerima=?');
                $stmt->execute([$nama, $jenis, $deskripsi, $alamat, $no_telp, $email, $id_edit]);
                header('Location: penerima.php');
                exit();
            } else {
                $stmt = $conn->prepare('INSERT INTO penerima (nama_penerima, jenis_penerima, deskripsi, alamat, no_telp, email) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$nama, $jenis, $deskripsi, $alamat, $no_telp, $email]);
                header('Location: penerima.php');
                exit();
            }
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan penerima: ' . $e->getMessage();
        }
    } else {
        $error = 'Nama dan jenis penerima wajib diisi!';
    }
}
// Ambil data penerima
$data = $conn->query('SELECT * FROM penerima ORDER BY id_penerima DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penerima</title>
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
    <h2><?php echo $edit_data ? 'Edit Penerima' : 'Tambah Penerima'; ?></h2>
    <?php if ($error): ?>
        <p style="color:red;"> <?php echo htmlspecialchars($error); ?> </p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id_penerima'] ?? ''; ?>">
        <label>Nama Penerima*:<br><input type="text" name="nama_penerima" required value="<?php echo htmlspecialchars($edit_data['nama_penerima'] ?? ''); ?>"></label><br><br>
        <label>Jenis Penerima*:<br>
            <select name="jenis_penerima" required>
                <option value="">--Pilih--</option>
                <option value="individu" <?php if(($edit_data['jenis_penerima'] ?? '')=='individu') echo 'selected'; ?>>Individu</option>
                <option value="organisasi" <?php if(($edit_data['jenis_penerima'] ?? '')=='organisasi') echo 'selected'; ?>>Organisasi</option>
            </select>
        </label><br><br>
        <label>Deskripsi:<br><textarea name="deskripsi"><?php echo htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea></label><br><br>
        <label>No. Telp:<br><input type="text" name="no_telp" value="<?php echo htmlspecialchars($edit_data['no_telp'] ?? ''); ?>"></label><br><br>
        <label>Email:<br><input type="email" name="email" value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>"></label><br><br>
        <label>Alamat:<br><textarea name="alamat"><?php echo htmlspecialchars($edit_data['alamat'] ?? ''); ?></textarea></label><br><br>
        <button type="submit"><?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Penerima'; ?></button>
        <?php if ($edit_data): ?>
            <a href="penerima.php">Batal</a>
        <?php endif; ?>
    </form>
    <h2>Data Penerima</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Jenis</th>
            <th>Deskripsi</th>
            <th>No. Telp</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Aksi</th>
        </tr>
        <?php foreach($data as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id_penerima']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_penerima']); ?></td>
            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
            <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
            <td>
                <a href="penerima.php?edit=<?php echo $row['id_penerima']; ?>">Edit</a> |
                <a href="penerima.php?hapus=<?php echo $row['id_penerima']; ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
