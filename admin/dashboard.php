<?php
//dashboard (admin)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekAdmin();

//statistik
$total_tugas   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas"))[0];
$selesai       = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE status='selesai'"))[0];
$proses        = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE status='proses'"))[0];
$belum         = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE status='belum'"))[0];
$terlambat     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM tugas WHERE deadline < CURDATE() AND status != 'selesai'"))[0];
$total_user    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='user'"))[0];

//Tugas terbaru
$tugas_terbaru = mysqli_query($conn, "
    SELECT t.*, u.nama as nama_user
    FROM tugas t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 8
");

$persen = $total_tugas > 0 ? round($selesai / $total_tugas * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin Sistem Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?> (Admin)</span>
        <a href="dashboard.php" class="nav-link active">Dashboard</a>
        <a href="tugas.php" class="nav-link">Semua Tugas</a>
        <a href="users.php" class="nav-link">Kelola User</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">Dashboard Admin</div>

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-number"><?= $total_tugas ?></div>
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
        <div class="stat-card primary">
            <div class="stat-number"><?= $total_user ?></div>
            <div class="stat-label">Total User</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Progres Keseluruhan</span>
            <span style="font-weight:600;color:#4f46e5;"><?= $persen ?>%</span>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width:<?= $persen ?>%"></div>
        </div>
        <p style="font-size:0.82rem;color:#64748b;margin-top:6px;"><?= $selesai ?> dari <?= $total_tugas ?> tugas selesai</p>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Tugas Terbaru</span>
            <a href="tugas.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>User</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($tugas_terbaru)): ?>
                    <?php
                        $late = (!empty($row['deadline']) && $row['deadline'] < date('Y-m-d') && $row['status'] != 'selesai');
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['nama_user']) ?></td>
                        <td><span class="badge badge-<?= $row['prioritas'] ?>"><?= ucfirst($row['prioritas']) ?></span></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td class="<?= $late ? 'deadline-red' : '' ?>">
                            <?= $row['deadline'] ? date('d/m/Y', strtotime($row['deadline'])) : '-' ?>
                            <?= $late ? ' ⚠️' : '' ?>
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