<?php
session_start();
require_once 'config/database.php';

// ambil id dari url
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['pesan'] = "ID tidak valid.";
    $_SESSION['tipe_pesan'] = "danger";
    header("Location: index.php");
    exit();
}

// cek dulu datanya ada atau tidak
$cek = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$cek->bind_param("i", $id);
$cek->execute();
$hasil = $cek->get_result();

if ($hasil->num_rows == 0) {
    $_SESSION['pesan'] = "Data tidak ditemukan.";
    $_SESSION['tipe_pesan'] = "danger";
    header("Location: index.php");
    exit();
}

// isi variabel dengan data lama (untuk pre-fill form)
$data = $hasil->fetch_assoc();
$cek->close();

$errors = [];
$kode = $data['kode_kategori'];
$nama = $data['nama_kategori'];
$deskripsi = $data['deskripsi'];
$status = $data['status'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim(htmlspecialchars($_POST['kode_kategori'] ?? ''));
    $nama = trim(htmlspecialchars($_POST['nama_kategori'] ?? ''));
    $deskripsi = trim(htmlspecialchars($_POST['deskripsi'] ?? ''));
    $status = trim($_POST['status'] ?? '');

    // validasi kode
    if ($kode == '') {
        $errors[] = "Kode kategori harus diisi!";
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors[] = "Panjang kode harus 4-10 karakter.";
    } elseif (substr($kode, 0, 4) != 'KAT-') {
        $errors[] = "Kode harus diawali KAT-";
    } else {
        // exclude data yang sedang diedit biar tidak bentrok sama dirinya sendiri
        // kalau tidak pakai AND id_kategori != ?, data yang diedit akan selalu dianggap duplikat
        $cek_duplikat = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?");
        $cek_duplikat->bind_param("si", $kode, $id); // "si" = string, integer
        $cek_duplikat->execute();
        $cek_duplikat->store_result();
        if ($cek_duplikat->num_rows > 0) {
            $errors[] = "Kode $kode sudah dipakai data lain.";
        }
        $cek_duplikat->close();
    }

    // validasi nama
    if ($nama == '') {
        $errors[] = "Nama kategori harus diisi!";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama minimal 3 karakter.";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama terlalu panjang (maks 50 karakter).";
    }

    if ($deskripsi != '' && strlen($deskripsi) > 200) {
        $errors[] = "Deskripsi maks 200 karakter.";
    }

    if (!in_array($status, ['Aktif', 'Nonaktif'])) {
        $errors[] = "Status tidak valid.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?");
        $stmt->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);

        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Data berhasil diupdate!";
            $_SESSION['tipe_pesan'] = "success";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Gagal update, coba lagi.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">Edit Kategori</h4>
                    </div>
                    <div class="card-body">

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= $err ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="kode_kategori" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_kategori" name="kode_kategori"
                                    value="<?= htmlspecialchars($kode) ?>" required>
                                <div class="form-text">Format: diawali KAT-, panjang 4-10 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                                    value="<?= htmlspecialchars($nama) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($deskripsi ?? '') ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="aktif" value="Aktif"
                                            <?= ($status == 'Aktif') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="aktif">Aktif</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="nonaktif" value="Nonaktif"
                                            <?= ($status == 'Nonaktif') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="nonaktif">Nonaktif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">Update</button>
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>