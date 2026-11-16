<?php
require 'koneksi.php';

$error = [];
$nim = $nama = $prodi = $alamat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim    = trim($_POST['nim'] ?? '');
    $nama   = trim($_POST['nama'] ?? '');
    $prodi  = trim($_POST['program_studi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (!csrf_valid()) {
        $error[] = 'Sesi formulir tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $error = validasi_mahasiswa($nim, $nama, $prodi, $alamat);
    }

    if (!$error) {
        try {
            $stmt = $conn->prepare('INSERT INTO mahasiswa (nim, nama, program_studi, alamat) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $nim, $nama, $prodi, $alamat);
            $stmt->execute();
            $stmt->close();

            set_flash('success', 'Data mahasiswa "' . $nama . '" berhasil ditambahkan.');
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) { // duplicate entry pada primary key
                $error[] = 'NIM ' . $nim . ' sudah terdaftar. Gunakan NIM lain.';
            } else {
                $error[] = 'Data gagal disimpan. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container container-narrow">
    <header class="page-head">
        <h1>Tambah Mahasiswa</h1>
        <a href="index.php" class="btn btn-ghost">Kembali</a>
    </header>

    <?php if ($error): ?>
        <div class="alert alert-error" role="alert">
            <ul>
                <?php foreach ($error as $pesan): ?><li><?= e($pesan) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="card form" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <div class="field">
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" maxlength="20" required value="<?= e($nim) ?>">
        </div>

        <div class="field">
            <label for="nama">Nama lengkap</label>
            <input type="text" id="nama" name="nama" maxlength="100" required value="<?= e($nama) ?>">
        </div>

        <div class="field">
            <label for="program_studi">Program studi</label>
            <input type="text" id="program_studi" name="program_studi" maxlength="50" list="daftar-prodi" required value="<?= e($prodi) ?>">
            <datalist id="daftar-prodi">
                <option value="Teknik Informatika">
                <option value="Sistem Informasi">
                <option value="Teknik Elektro">
                <option value="Manajemen">
                <option value="Akuntansi">
            </datalist>
        </div>

        <div class="field">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="4" required><?= e($alamat) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="index.php" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan data</button>
        </div>
    </form>
</main>
</body>
</html>
