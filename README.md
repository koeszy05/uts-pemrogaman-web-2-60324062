# UTS Pemrograman Web 2 — Sistem Manajemen Kategori Buku

## Identitas Mahasiswa

| | |
|---|---|
| **Nama** | Reza Koes Alqoroni |
| **NIM** | 60324062 |
| **Mata Kuliah** | Pemrograman Web 2 |
| **Program Studi** | Informatika |
| **Institut** | UIN K.H. Abdurrahman Wahid Pekalongan |

---

## Deskripsi Aplikasi

Aplikasi web sederhana untuk mengelola data **Kategori Buku** di perpustakaan. Dibuat menggunakan PHP native dan MySQL sebagai bagian dari ujian tengah semester (UTS) mata kuliah Pemrograman Web 2.

Fitur yang tersedia:
- Menampilkan daftar kategori buku (Read)
- Menambah kategori baru (Create)
- Mengubah data kategori (Update)
- Menghapus kategori (Delete)

---

## Cara Instalasi dan Menjalankan Aplikasi

### Kebutuhan
- XAMPP (Apache + MySQL)
- Browser (Chrome, Firefox, dll)

### Langkah-langkah

**1. Clone atau download repository ini**
```
git clone https://github.com/koeszy05/uts-pemrogaman-web-2-60324062.git
```
Letakkan folder hasil clone di dalam `C:\xampp\htdocs\`

**2. Jalankan XAMPP**

Aktifkan modul **Apache** dan **MySQL** di XAMPP Control Panel.

**3. Buat database**

Buka `http://localhost/phpmyadmin`, lalu jalankan query berikut di tab SQL:

```sql
CREATE DATABASE uts_perpustakaan_60324062
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE uts_perpustakaan_60324062;

CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    kode_kategori VARCHAR(10) UNIQUE NOT NULL,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES
('KAT-001', 'Pemrograman', 'Buku-buku tentang bahasa pemrograman', 'Aktif'),
('KAT-002', 'Database', 'Buku-buku tentang sistem basis data', 'Aktif'),
('KAT-003', 'Jaringan', 'Buku-buku tentang jaringan komputer', 'Aktif');
```

**4. Sesuaikan konfigurasi database**

Buka file `config/database.php`, pastikan isinya seperti ini:
```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'uts_perpustakaan_60324062');
```

**5. Buka aplikasi di browser**

```
http://localhost/uts_60324062/index.php
```

---

## Struktur Folder

```
uts_60324062/
├── config/
│   └── database.php    # konfigurasi koneksi database
├── index.php           # halaman utama - daftar kategori (Read)
├── create.php          # halaman tambah kategori baru (Create)
├── edit.php            # halaman edit kategori (Update)
├── delete.php          # proses hapus kategori (Delete)
└── README.md           # dokumentasi proyek
```

---

## Link Repository GitHub

[https://github.com/koeszy05/uts-pemrogaman-web-2-60324062](https://github.com/koeszy05/uts-pemrogaman-web-2-60324062)
