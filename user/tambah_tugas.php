<?php
//tambah tugas (user)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekLogin();

$uid    = $_SESSION['user_id'];
$errors = [];
$msg    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = sanitize($_POST['judul']     ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $kategori  = $_POST['kategori']  ?? '';
    $prioritas = $_POST['prioritas'] ?? '';
    $status    = $_POST['status']    ?? 'belum';
    $deadline  = $_POST['deadline']  ?? '';

    // Validasi
    if (empty($judul))     $errors[] = 'Judul tugas tidak boleh kosong.';
    if (strlen($judul) > 200) $errors[] = 'Judul terlalu panjang (maks. 200 karakter).';
    if (empty($kategori))  $errors[] = 'Kategori harus dipilih.';
    if (empty($prioritas)) $errors[] = 'Prioritas harus dipilih.';
    if (!empty($deadline) && !strtotime($deadline)) $errors[] = 'Format deadline tidak valid.';

    if (empty($errors)) {
        $dl = !empty($deadline) ? "'$deadline'" : 'NULL';
        mysqli_query($conn, "
            INSERT INTO tugas (user_id, judul, deskripsi, kategori, prioritas, status, deadline)
            VALUES ($uid,
                    '".mysqli_real_escape_string($conn,$judul)."',
                    '".mysqli_real_escape_string($conn,$deskripsi)."',
                    '$kategori', '$prioritas', '$status', $dl)
        ");
        header("Location: tugas.php?msg=Tugas+berhasil+ditambahkan");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas Sistem Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?></span>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="tugas.php" class="nav-link">Tugas Saya</a>
        <a href="tambah_tugas.php" class="nav-link active">Tambah</a>
        <a href="profil.php" class="nav-link">Profil</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">Tambah Tugas Baru</div>

        <?php foreach($errors as $e): ?>
    <div class="alert alert-danger"><?= $e ?></div>
        <?php endforeach; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Judul Tugas <span style="color:red;">*</span></label>
                <input type="text" name="judul" class="form-control"
                       placeholder="Contoh: Kerjakan laporan praktikum"
                       value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>" required maxlength="200">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"
                          placeholder="Keterangan tambahan tentang tugas ini (opsional)"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori <span style="color:red;">*</span></label>
                    <select name="kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach(['individu','kelompok','belajar','organisasi','lainnya'] as $k): ?>
                        <option value="<?= $k ?>" <?= ($_POST['kategori']??'')==$k?'selected':'' ?>><?= ucfirst($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas <span style="color:red;">*</span></label>
                    <select name="prioritas" class="form-control" required>
                        <option value="">-- Pilih Prioritas --</option>
                        <option value="tinggi" <?= ($_POST['prioritas']??'')=='tinggi'?'selected':'' ?>>Tinggi</option>
                        <option value="sedang" <?= ($_POST['prioritas']??'')=='sedang'?'selected':'' ?>>Sedang</option>
                        <option value="rendah" <?= ($_POST['prioritas']??'')=='rendah'?'selected':'' ?>>Rendah</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="belum"  <?= ($_POST['status']??'')=='belum' ?'selected':'' ?>>Belum Dikerjakan</option>
                        <option value="proses" <?= ($_POST['status']??'')=='proses'?'selected':'' ?>>Sedang Proses</option>
                        <option value="selesai"<?= ($_POST['status']??'')=='selesai'?'selected':'' ?>>Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control"
                           value="<?= htmlspecialchars($_POST['deadline'] ?? '') ?>"
                           min="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary">Simpan Tugas</button>
                <a href="tugas.php" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
