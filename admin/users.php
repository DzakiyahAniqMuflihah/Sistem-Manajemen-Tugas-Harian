<?php
//user (admin)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekAdmin();

$msg = '';
$errors = [];

//delete user
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id == $_SESSION['user_id']) {
        $msg = 'Tidak bisa menghapus akun sendiri!';
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        $msg = 'User berhasil dihapus.';
    }
}

//tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama     = sanitize($_POST['nama'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'user';

    if (empty($nama))     $errors[] = 'Nama tidak boleh kosong.';
    if (empty($username)) $errors[] = 'Username tidak boleh kosong.';
    if (empty($password)) $errors[] = 'Password tidak boleh kosong.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    //mengecek username
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='".mysqli_real_escape_string($conn,$username)."'");
    if (mysqli_num_rows($cek) > 0) $errors[] = 'Username sudah digunakan.';

    if (empty($errors)) {
        $pass_hash = md5($password);
        mysqli_query($conn, "
            INSERT INTO users (nama, username, password, role)
            VALUES ('".mysqli_real_escape_string($conn,$nama)."',
                    '".mysqli_real_escape_string($conn,$username)."',
                    '$pass_hash', '$role')
        ");
        $msg = 'User berhasil ditambahkan.';
    }
}

$users = mysqli_query($conn, "
    SELECT u.*, COUNT(t.id) as jml_tugas
    FROM users u
    LEFT JOIN tugas t ON u.id = t.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?> (Admin)</span>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="tugas.php" class="nav-link">Semua Tugas</a>
        <a href="users.php" class="nav-link active">Kelola User</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>
<div class="container">
    <div class="page-title">Kelola User</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>

    <div class="card">
        <div class="card-header"><span class="card-title">Tambah User Baru</span></div>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap">
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="tambah" class="btn btn-primary">Tambah User</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Daftar User</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Jumlah Tugas</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; while($row=mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><span class="badge badge-<?= $row['role'] ?>"><?= ucfirst($row['role']) ?></span></td>
                    <td><?= $row['jml_tugas'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?hapus=<?= $row['id'] ?>"
                           onclick="return confirm('Hapus user ini? Semua tugasnya akan ikut terhapus!')"
                           class="btn btn-danger btn-sm">Hapus</a>
                        <?php else: ?>
                        <span style="color:#94a3b8;font-size:0.8rem;">Akun Anda</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>