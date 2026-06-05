<?php
//edit tugas (admin)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekAdmin();

$id = (int)($_GET['id'] ?? 0);
$tugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tugas WHERE id=$id"));
if (!$tugas) { header("Location: tugas.php"); exit(); }

$errors = [];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = sanitize($_POST['judul'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $kategori  = $_POST['kategori']  ?? '';
    $prioritas = $_POST['prioritas'] ?? '';
    $status    = $_POST['status']    ?? '';
    $deadline  = $_POST['deadline']  ?? '';

    if (empty($judul))     $errors[] = 'Judul tidak boleh kosong.';
    if (empty($kategori))  $errors[] = 'Kategori harus dipilih.';
    if (empty($prioritas)) $errors[] = 'Prioritas harus dipilih.';
    if (empty($status))    $errors[] = 'Status harus dipilih.';

    if (empty($errors)) {
        $dl = $deadline ? "'$deadline'" : 'NULL';
        mysqli_query($conn, "
            UPDATE tugas SET
                judul='".mysqli_real_escape_string($conn,$judul)."',
                deskripsi='".mysqli_real_escape_string($conn,$deskripsi)."',
                kategori='$kategori',
                prioritas='$prioritas',
                status='$status',
                deadline=$dl
            WHERE id=$id
        ");
        $msg = 'Tugas berhasil diperbarui.';
        $tugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tugas WHERE id=$id"));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tugas Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="tugas.php" class="nav-link active">Semua Tugas</a>
        <a href="users.php" class="nav-link">Kelola User</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>
<div class="container">
    <div class="page-title">Edit Tugas</div>
    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php foreach($errors as $e): ?><div class="alert alert-danger"><?= $e ?></div><?php endforeach; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Judul Tugas</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($tugas['judul']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($tugas['deskripsi']) ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-control">
                        <?php foreach(['individu','kelompok','belajar','paper','lainnya'] as $k): ?>
                        <option value="<?= $k ?>" <?= $tugas['kategori']==$k?'selected':'' ?>><?= ucfirst($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-control">
                        <?php foreach(['tinggi','sedang','rendah'] as $p): ?>
                        <option value="<?= $p ?>" <?= $tugas['prioritas']==$p?'selected':'' ?>><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <?php foreach(['belum','proses','selesai'] as $s): ?>
                        <option value="<?= $s ?>" <?= $tugas['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="<?= $tugas['deadline'] ?>">
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="tugas.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
