<?php
//dashboard (user)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekLogin();

$uid = $_SESSION['user_id'];

//statistik user
$total    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE user_id=$uid"))[0];
$selesai  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE user_id=$uid AND status='selesai'"))[0];
$proses   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE user_id=$uid AND status='proses'"))[0];
$terlambat= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE user_id=$uid AND deadline < CURDATE() AND status!='selesai'"))[0];

$persen = $total > 0 ? round($selesai / $total * 100) : 0;

$prioritas_list = mysqli_query($conn, "
    SELECT * FROM tugas
    WHERE user_id=$uid AND status!='selesai'
    ORDER BY FIELD(prioritas,'tinggi','sedang','rendah'), deadline ASC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Sistem Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?></span>
        <a href="dashboard.php" class="nav-link active">Dashboard</a>
        <a href="tugas.php" class="nav-link">Tugas Saya</a>
        <a href="tambah_tugas.php" class="nav-link">Tambah</a>
        <a href="profil.php" class="nav-link">Profil</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</div>

    <?php if ($terlambat > 0): ?>
    <div class="alert alert-danger">
        ⚠️ Kamu memiliki <strong><?= $terlambat ?> tugas terlambat</strong>! Segera selesaikan.
        <a href="tugas.php?status=belum&sort=deadline" style="margin-left:8px;">Lihat sekarang</a>
    </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-number"><?= $total ?></div>
            <div class="stat-label">Total Tugas</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number"><?= $selesai ?></div>
            <div class="stat-label">Selesai</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?= $proses ?></div>
            <div class="stat-label">Proses</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-number"><?= $terlambat ?></div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Daftar Tugas</span>
            <a href="tambah_tugas.php" class="btn btn-primary btn-sm">+ Tambah Tugas</a>
        </div>
        <?php if (mysqli_num_rows($prioritas_list) === 0): ?>
            <p style="text-align:center;color:#94a3b8;padding:2rem 0;">Belum ada tugas</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Judul</th><th>Deskripsi</th><th>Prioritas</th><th>Status</th><th>Deadline</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php while($row=mysqli_fetch_assoc($prioritas_list)): ?>
                    <?php $late = (!empty($row['deadline']) && $row['deadline'] < date('Y-m-d')); ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td><span class="badge badge-<?= $row['prioritas'] ?>"><?= ucfirst($row['prioritas']) ?></span></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td class="<?= $late?'deadline-red':'' ?>">
                            <?= $row['deadline'] ? date('d/m/Y', strtotime($row['deadline'])) : '-' ?>
                            <?= $late ? ' ⚠️' : '' ?>
                        </td>
                        <td>
                            <a href="edit_tugas.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
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