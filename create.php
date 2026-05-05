<?php
session_start();
require_once 'config/database.php';

$errors = [];
$kode = '';
$nama = '';
$deskripsi = '';
$status = 'Aktif';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ambil data dari form
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
        $errors[] = "Kode harus diawali KAT- (contoh: KAT-004)";
    } else {
        // cek kode sudah ada di database atau belum
        // pakai prepared statement biar aman dari SQL injection
        $cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ?");
        $cek->bind_param("s", $kode); // "s" = string
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $errors[] = "Kode $kode sudah dipakai, coba kode lain.";
        }
        $cek->close();
    }

    // validasi nama
    if ($nama == '') {
        $errors[] = "Nama kategori harus diisi!";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama kategori minimal 3 karakter.";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama kategori terlalu panjang (maks 50 karakter).";
    }

    // deskripsi boleh kosong, tapi kalau diisi jangan lebih dari 200 karakter
    if ($deskripsi != '' && strlen($deskripsi) > 200) {
        $errors[] = "Deskripsi maksimal 200 karakter.";
    }

    // status harus salah satu dari dua pilihan
    if (!in_array($status, ['Aktif', 'Nonaktif'])) {
        $errors[] = "Pilih status dulu (Aktif / Nonaktif).";
    }

    // kalau semua validasi lolos, simpan ke database
    if (empty($errors)) {
        // "ssss" artinya 4 parameter bertipe string
        $stmt = $conn->prepare("INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $kode, $nama, $deskripsi, $status);

        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Kategori baru berhasil ditambahkan!";
            $_SESSION['tipe_pesan'] = "success";
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Gagal menyimpan, coba lagi.";
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
    <title>Tambah Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tambah Kategori Baru</h4>
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
                                    value="<?= htmlspecialchars($kode) ?>" placeholder="Contoh: KAT-004" required>
                                <div class="form-text">Format: diawali KAT-, panjang 4-10 karakter, tidak boleh duplikat.</div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                                    value="<?= htmlspecialchars($nama) ?>" placeholder="Contoh: Pemrograman" required>
                                <div class="form-text">Minimal 3 karakter, maksimal 50 karakter.</div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                    placeholder="Keterangan kategori (opsional, maks 200 karakter)"><?= htmlspecialchars($deskripsi) ?></textarea>
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
                                <button type="submit" class="btn btn-primary">Simpan</button>
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