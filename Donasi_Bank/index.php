<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if ($username) {
        $stmt = $conn->prepare('SELECT * FROM admin WHERE username = ? AND is_active = 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Username tidak ditemukan!';
        }
    } else {
        $error = 'Username harus diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
</head>
<body>
    <h2>Login Admin (Tanpa Password)</h2>
    <?php if ($error): ?>
        <p style="color:red;"> <?php echo htmlspecialchars($error); ?> </p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Username:<br><input type="text" name="username" required></label><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
