<?php
//edit tugas (user)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekLogin();

$uid = $_SESSION['user_id'];
$id  = (int)($_GET['id'] ?? 0);

$tugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tugas WHERE id=$id AND user_id=$uid"));
if (!$tugas) { header("Location: tugas.php"); exit(); }

$errors = [];
$msg    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = sanitize($_POST['judul']     ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $kategori  = $_POST['kategori']  ?? '';
    $prioritas = $_POST['prioritas'] ?? '';
    $status    = $_POST['status']    ?? '';
    $deadline  = $_POST['deadline']  ?? '';

    if (empty($judul))     $errors[] = 'Judul tidak boleh kosong.';
    if (empty($kategori))  $errors[] = 'Kategori harus dipilih.';
    if (empty($prioritas)) $errors[] = 'Prioritas harus dipilih.';

    if (empty($errors)) {
        $dl = !empty($deadline) ? "'$deadline'" : 'NULL';
        mysqli_query($conn, "
            UPDATE tugas SET
                judul='".mysqli_real_escape_string($conn,$judul)."',
                deskripsi='".mysqli_real_escape_string($conn,$deskripsi)."',
                kategori='$kategori',
                prioritas='$prioritas',
                status='$status',
                deadline=$dl
            WHERE id=$id AND user_id=$uid
        ");
        $msg = 'Tugas berhasil diperbarui!';
        $tugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tugas WHERE id=$id AND user_id=$uid"));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tugas Sistem Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?></span>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="tugas.php" class="nav-link active">Tugas Saya</a>
        <a href="tambah_tugas.php" class="nav-link">Tambah</a>
        <a href="profil.php" class="nav-link">Profil</a>
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
                <label class="form-label">Judul Tugas <span style="color:red;">*</span></label>
                <input type="text" name="judul" class="form-control"
                       value="<?= htmlspecialchars($tugas['judul']) ?>" required maxlength="200">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($tugas['deskripsi']) ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-control">
                        <?php foreach(['individu','kelompok','belajar','organisasi','lainnya'] as $k): ?>
                        <option value="<?= $k ?>" <?= $tugas['kategori']==$k?'selected':'' ?>><?= ucfirst($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-control">
                        <option value="tinggi" <?= $tugas['prioritas']=='tinggi'?'selected':'' ?>>Tinggi</option>
                        <option value="sedang" <?= $tugas['prioritas']=='sedang'?'selected':'' ?>>Sedang</option>
                        <option value="rendah" <?= $tugas['prioritas']=='rendah'?'selected':'' ?>>Rendah</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="belum"   <?= $tugas['status']=='belum'  ?'selected':'' ?>>Belum Dikerjakan</option>
                        <option value="proses"  <?= $tugas['status']=='proses' ?'selected':'' ?>>Sedang Proses</option>
                        <option value="selesai" <?= $tugas['status']=='selesai'?'selected':'' ?>>Selesai</option>
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
