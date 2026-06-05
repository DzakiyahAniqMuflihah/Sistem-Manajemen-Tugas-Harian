<?php
//tugas (admin)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php';
cekAdmin();

$msg = '';
$msg_type = 'success';

//delete
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tugas WHERE id=$id");
    $msg = 'Tugas berhasil dihapus.';
}

//filter
$where = "1=1";
$f_status   = $_GET['status']   ?? '';
$f_prioritas= $_GET['prioritas']?? '';
$f_user     = $_GET['user']     ?? '';
$f_search   = $_GET['search']   ?? '';

if ($f_status)    $where .= " AND t.status='".mysqli_real_escape_string($conn,$f_status)."'";
if ($f_prioritas) $where .= " AND t.prioritas='".mysqli_real_escape_string($conn,$f_prioritas)."'";
if ($f_user)      $where .= " AND t.user_id=".(int)$f_user;
if ($f_search)    $where .= " AND t.judul LIKE '%".mysqli_real_escape_string($conn,$f_search)."%'";

$tugas_list = mysqli_query($conn, "
    SELECT t.*, u.nama as nama_user
    FROM tugas t JOIN users u ON t.user_id=u.id
    WHERE $where
    ORDER BY FIELD(t.prioritas,'tinggi','sedang','rendah'), t.deadline ASC
");

$users_list = mysqli_query($conn, "SELECT id, nama FROM users WHERE role='user' ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Tugas Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">Sistem Manajemen Tugas Harian</a>
    <div class="navbar-nav">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['nama']) ?> (Admin)</span>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="tugas.php" class="nav-link active">Semua Tugas</a>
        <a href="users.php" class="nav-link">Kelola User</a>
        <a href="../logout.php" class="nav-link logout">Keluar</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">Semua Tugas</div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?>"><?= $msg ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Cari judul tugas..." value="<?= htmlspecialchars($f_search) ?>">
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
            <select name="user">
                <option value="">Semua User</option>
                <?php
                    $ul = mysqli_query($conn, "SELECT id,nama FROM users WHERE role='user'");
                    while($u=mysqli_fetch_assoc($ul)):
                ?>
                <option value="<?= $u['id'] ?>" <?= $f_user==$u['id']?'selected':'' ?>><?= htmlspecialchars($u['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="tugas.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>User</th>
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
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['nama_user']) ?></td>
                        <td><?= ucfirst($row['kategori']) ?></td>
                        <td><span class="badge badge-<?= $row['prioritas'] ?>"><?= ucfirst($row['prioritas']) ?></span></td>
                        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td class="<?= $late ? 'deadline-red' : '' ?>">
                            <?= $row['deadline'] ? date('d/m/Y', strtotime($row['deadline'])) : '-' ?>
                            <?= $late ? ' ⚠️' : '' ?>
                        </td>
                        <td>
                            <a href="edit_tugas.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="tugas.php?hapus=<?= $row['id'] ?>"
                               onclick="return confirm('Hapus tugas ini?')"
                               class="btn btn-danger btn-sm">Hapus</a>
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