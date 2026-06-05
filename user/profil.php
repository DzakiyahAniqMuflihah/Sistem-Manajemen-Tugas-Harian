<?php
//profil (user)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekLogin();

$uid    = $_SESSION['user_id'];
$user   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
$errors = [];
$msg    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama         = sanitize($_POST['nama']         ?? '');
    $password_lama= $_POST['password_lama']         ?? '';
    $password_baru= $_POST['password_baru']         ?? '';
    $konfirmasi   = $_POST['konfirmasi']             ?? '';

    if (empty($nama)) $errors[] = 'Nama tidak boleh kosong.';

    if (!empty($password_lama) || !empty($password_baru)) {
        if (md5($password_lama) !== $user['password']) {
            $errors[] = 'Password lama tidak sesuai.';
        }
        if (strlen($password_baru) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        }
        if ($password_baru !== $konfirmasi) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }
    }

    if (empty($errors)) {
        $nama_esc = mysqli_real_escape_string($conn, $nama);
        if (!empty($password_baru)) {
            $hash = md5($password_baru);
            mysqli_query($conn, "UPDATE users SET nama='$nama_esc', password='$hash' WHERE id=$uid");
        } else {
            mysqli_query($conn, "UPDATE users SET nama='$nama_esc' WHERE id=$uid");
        }
        $_SESSION['nama'] = $nama;
        $msg = 'Profil berhasil diperbarui!';
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Sistem Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?></span>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="tugas.php" class="nav-link">Tugas Saya</a>
        <a href="tambah_tugas.php" class="nav-link">Tambah</a>
        <a href="profil.php" class="nav-link active">Profil</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">Profil Saya</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>

    <div class="card" style="max-width:540px;">
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled style="background:#f8fafc;">
                <small style="color:#94a3b8;">Username tidak dapat diubah</small>
            </div>

            <hr style="border:none;border-top:1px solid #e2e8f0;margin:1.25rem 0;">
            <p style="font-weight:600;font-size:0.9rem;margin-bottom:1rem;">Ganti Password <span style="font-weight:400;color:#94a3b8;">(kosongkan jika tidak ingin mengubah)</span></p>

            <div class="form-group">
                <label class="form-label">Password Lama</label>
                <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama">
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control" placeholder="Min. 6 karakter">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi" class="form-control" placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
</body>
</html>
