<?php
//halaman login
session_start();
require_once 'includes/koneksi.php';
require_once 'includes/auth.php';

cekSudahLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        $username_safe = mysqli_real_escape_string($conn, $username);
        $password_hash = md5($password);

        $query = "SELECT * FROM users WHERE username='$username_safe' AND password='$password_hash' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['nama']      = $user['nama'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <h2>Sistem Manajemen Tugas Harian</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Masukkan username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px;">
                Masuk
            </button>
        </form>
    </div>
</div>
</body>
</html>