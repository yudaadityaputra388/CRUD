<?php
require 'koneksi.php';

// Penghapusan hanya lewat POST (+ token CSRF) agar tidak bisa dipicu lewat link/gambar dari situs lain
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    set_flash('error', 'Permintaan hapus tidak valid.');
    header('Location: index.php');
    exit;
}

$nim = trim($_POST['nim'] ?? '');

if ($nim === '') {
    set_flash('error', 'NIM tidak ditemukan.');
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conn->prepare('DELETE FROM mahasiswa WHERE nim = ?');
    $stmt->bind_param('s', $nim);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        set_flash('success', 'Data mahasiswa dengan NIM ' . $nim . ' berhasil dihapus.');
    } else {
        set_flash('error', 'Data dengan NIM ' . $nim . ' tidak ditemukan.');
    }
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    set_flash('error', 'Data gagal dihapus. Silakan coba lagi.');
}

header('Location: index.php');
exit;
