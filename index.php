<?php
session_start();
require_once 'config/database.php';

// cek apakah ada pesan dari halaman lain (habis tambah/edit/hapus)
$pesan = '';
$tipe_pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    $tipe_pesan = $_SESSION['tipe_pesan'];
    unset($_SESSION['pesan'], $_SESSION['tipe_pesan']);
}

// ambil semua data kategori, yang baru masuk ditampilkan paling atas
$sql = "SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status FROM kategori ORDER BY id_kategori DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kategori Buku</h2>
            <a href="create.php" class="btn btn-primary">+ Tambah Kategori</a>
        </div>

        <?php if ($pesan != ''): ?>
            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th width="110">Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="100">Status</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($hasil->num_rows > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($baris = $hasil->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($baris['kode_kategori']) ?></td>
                                    <td><?= htmlspecialchars($baris['nama_kategori']) ?></td>
                                    <td><?= htmlspecialchars($baris['deskripsi'] ?? '-') ?></td>
                                    <td>
                                        <?php // tampilkan badge warna berbeda tergantung status ?>
                                        <?php if ($baris['status'] == 'Aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?= $baris['id_kategori'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <button onclick="confirmDelete(<?= $baris['id_kategori'] ?>)" class="btn btn-danger btn-sm">Hapus</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data kategori.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(id) {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            window.location.href = 'delete.php?id=' + id;
        }
    }
    </script>
</body>
</html>