<?php
session_start();
require_once 'config/database.php';

// pastikan id ada dan valid
if (!isset($_GET['id']) || (int)$_GET['id'] <= 0) {
    $_SESSION['pesan'] = "ID tidak valid.";
    $_SESSION['tipe_pesan'] = "danger";
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// cek datanya ada tidak sebelum dihapus
$cek = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ?");
$cek->bind_param("i", $id);
$cek->execute();
$hasil = $cek->get_result();

if ($hasil->num_rows == 0) {
    $_SESSION['pesan'] = "Data tidak ditemukan atau sudah terhapus.";
    $_SESSION['tipe_pesan'] = "warning";
    header("Location: index.php");
    exit();
}

$data = $hasil->fetch_assoc();
$cek->close();

// proses hapus
$hapus = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$hapus->bind_param("i", $id);
$hapus->execute();

// cek apakah benar terhapus dengan affected_rows
// affected_rows = jumlah baris yang terpengaruh, kalau 0 berarti tidak ada yang terhapus
if ($hapus->affected_rows > 0) {
    $_SESSION['pesan'] = "Kategori '" . htmlspecialchars($data['nama_kategori']) . "' berhasil dihapus.";
    $_SESSION['tipe_pesan'] = "success";
} else {
    $_SESSION['pesan'] = "Gagal hapus data.";
    $_SESSION['tipe_pesan'] = "danger";
}

$hapus->close();
header("Location: index.php");
exit();