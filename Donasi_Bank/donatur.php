<?php
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Proses hapus donatur
$error = '';
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        $stmt = $conn->prepare('DELETE FROM donatur WHERE id_donatur = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            header('Location: donatur.php?msg=hapus_sukses');
            exit();
        } else {
            $error = 'Data donatur tidak ditemukan atau gagal dihapus.';
        }
    } catch (PDOException $e) {
        $error = 'Gagal menghapus donatur: ' . $e->getMessage();
    }
}

// Proses edit donatur
$error = '';
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM donatur WHERE id_donatur = ?');
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_telp = trim($_POST['no_telp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $id_edit = $_POST['id_edit'] ?? '';
    if ($nama) {
        try {
            if ($id_edit) {
                $stmt = $conn->prepare('UPDATE donatur SET nama=?, email=?, no_telp=?, alamat=? WHERE id_donatur=?');
                $stmt->execute([$nama, $email, $no_telp, $alamat, $id_edit]);
                header('Location: donatur.php');
                exit();
            } else {
                $stmt = $conn->prepare('INSERT INTO donatur (nama, email, no_telp, alamat) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nama, $email, $no_telp, $alamat]);
                header('Location: donatur.php');
                exit();
            }
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan donatur: ' . $e->getMessage();
        }
    } else {
        $error = 'Nama donatur wajib diisi!';
    }
}
// Ambil data donatur
$data = $conn->query('SELECT * FROM donatur ORDER BY id_donatur DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Donatur</title>
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
        form textarea {
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
    <h2><?php echo $edit_data ? 'Edit Donatur' : 'Tambah Donatur'; ?></h2>
    <?php if ($error): ?>
        <p style="color:red;"> <?php echo htmlspecialchars($error); ?> </p>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="id_edit" value="<?php echo $edit_data['id_donatur'] ?? ''; ?>">
        <label>Nama Donatur*:<br><input type="text" name="nama" required value="<?php echo htmlspecialchars($edit_data['nama'] ?? ''); ?>"></label><br><br>
        <label>Email:<br><input type="email" name="email" value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>"></label><br><br>
        <label>No. Telp:<br><input type="text" name="no_telp" value="<?php echo htmlspecialchars($edit_data['no_telp'] ?? ''); ?>"></label><br><br>
        <label>Alamat:<br><textarea name="alamat"><?php echo htmlspecialchars($edit_data['alamat'] ?? ''); ?></textarea></label><br><br>
        <button type="submit"><?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Donatur'; ?></button>
        <?php if ($edit_data): ?>
            <a href="donatur.php">Batal</a>
        <?php endif; ?>
    </form>
    <h2>Data Donatur</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No. Telp</th>
            <th>Alamat</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Aksi</th>
        </tr>
        <?php foreach($data as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id_donatur']); ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
            <td>
                <a href="donatur.php?edit=<?php echo $row['id_donatur']; ?>">Edit</a> |
                <a href="donatur.php?hapus=<?php echo $row['id_donatur']; ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
