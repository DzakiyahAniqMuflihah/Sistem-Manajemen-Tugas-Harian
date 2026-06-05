<?php
//tugas(user)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekLogin();

$uid = $_SESSION['user_id'];
$msg = '';

//delete
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tugas WHERE id=$id AND user_id=$uid");
    $msg = 'Tugas berhasil dihapus.';
}

//filter dan search
$where = "user_id=$uid";
$f_status    = $_GET['status']    ?? '';
$f_prioritas = $_GET['prioritas'] ?? '';
$f_kategori  = $_GET['kategori']  ?? '';
$f_search    = $_GET['search']    ?? '';
$sort        = $_GET['sort']      ?? 'prioritas';

if ($f_status)    $where .= " AND status='".mysqli_real_escape_string($conn,$f_status)."'";
if ($f_prioritas) $where .= " AND prioritas='".mysqli_real_escape_string($conn,$f_prioritas)."'";
if ($f_kategori)  $where .= " AND kategori='".mysqli_real_escape_string($conn,$f_kategori)."'";
if ($f_search)    $where .= " AND judul LIKE '%".mysqli_real_escape_string($conn,$f_search)."%'";

$order = match($sort) {
    'deadline'   => "deadline ASC",
    'judul'      => "judul ASC",
    'created_at' => "created_at DESC",
    default      => "FIELD(prioritas,'tinggi','sedang','rendah'), deadline ASC"
};

$tugas_list = mysqli_query($conn, "SELECT * FROM tugas WHERE $where ORDER BY $order");
$total_rows = mysqli_num_rows($tugas_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas Saya Sistem Manajemen Tugas Harian</title>
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
    <div class="page-title">Tugas Saya</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <div class="card">
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Cari tugas..." value="<?= htmlspecialchars($f_search) ?>">
            <select name="status">
                <option value="">Semua Status</option>
                <option value="belum"   <?= $f_status=='belum'  ?'selected':'' ?>>Belum</option>
                <option value="proses"  <?= $f_status=='proses' ?'selected':'' ?>>Proses</option>
                <option value="selesai" <?= $f_status=='selesai'?'selected':'' ?>>Selesai</option>
            </select>
            <select name="prioritas">
                <option value="">Semua Prioritas</option>
                <option value="tinggi" <?= $f_prioritas=='tinggi'?'selected':'' ?>>Tinggi</option>
                <option value="sedang" <?= $f_prioritas=='sedang'?'selected':'' ?>>Sedang</option>
                <option value="rendah" <?= $f_prioritas=='rendah'?'selected':'' ?>>Rendah</option>
            </select>
            <select name="kategori">
                <option value="">Semua Kategori</option>
                <?php foreach(['individu','kelompok','belajar','organisasi','lainnya'] as $k): ?>
                <option value="<?= $k ?>" <?= $f_kategori==$k?'selected':'' ?>><?= ucfirst($k) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="tugas.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Daftar Tugas <span style="color:#94a3b8;font-weight:400;">(<?= $total_rows ?> tugas)</span></span>
            <a href="tambah_tugas.php" class="btn btn-primary btn-sm">+ Tambah Tugas</a>
        </div>
        <?php if ($total_rows === 0): ?>
            <p style="text-align:center;color:#94a3b8;padding:2rem;">Belum ada tugas. <a href="tambah_tugas.php">Tambah sekarang</a></p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; while($row=mysqli_fetch_assoc($tugas_list)): ?>
                    <?php $late = (!empty($row['deadline']) && $row['deadline'] < date('Y-m-d') && $row['status']!='selesai'); ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <?= htmlspecialchars($row['judul']) ?>
                            <?php if (!empty($row['deskripsi'])): ?>
                                <div style="font-size:0.78rem;color:#94a3b8;margin-top:2px;"><?= htmlspecialchars(substr($row['deskripsi'],0,50)) ?>...</div>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst($row['kategori']) ?></td>
                        <td><span class="badge badge-<?= $row['prioritas'] ?>"><?= ucfirst($row['prioritas']) ?></span></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td class="<?= $late?'deadline-red':($row['deadline']==date('Y-m-d')?'deadline-yellow':'') ?>">
                            <?= $row['deadline'] ? date('d/m/Y', strtotime($row['deadline'])) : '-' ?>
                            <?= $late ? ' ⚠️' : ($row['deadline']==date('Y-m-d') ? : '') ?>
                        </td>
                        <td>
                            <a href="edit_tugas.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="tugas.php?hapus=<?= $row['id'] ?>"
                               onclick="return confirm('Yakin hapus tugas ini?')"
                               class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
