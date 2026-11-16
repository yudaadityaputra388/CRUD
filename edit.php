<?php
require 'koneksi.php';

$error = [];

// Ambil NIM dari URL (saat membuka form) atau dari hidden input (saat submit)
$nim = trim($_POST['nim'] ?? $_GET['nim'] ?? '');

if ($nim === '') {
    set_flash('error', 'NIM tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $stmt = $conn->prepare('UPDATE mahasiswa SET nama = ?, program_studi = ?, alamat = ? WHERE nim = ?');
            $stmt->bind_param('ssss', $nama, $prodi, $alamat, $nim);
            $stmt->execute();
            $stmt->close();

            set_flash('success', 'Data mahasiswa "' . $nama . '" berhasil diperbarui.');
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error[] = 'Data gagal diperbarui. Silakan coba lagi.';
        }
    }
} else {
    // Muat data awal dari database
    $stmt = $conn->prepare('SELECT nama, program_studi, alamat FROM mahasiswa WHERE nim = ?');
    $stmt->bind_param('s', $nim);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$data) {
        set_flash('error', 'Data mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        header('Location: index.php');
        exit;
    }

    $nama   = $data['nama'];
    $prodi  = $data['program_studi'];
    $alamat = $data['alamat'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container container-narrow">
    <header class="page-head">
        <h1>Edit Mahasiswa</h1>
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
        <input type="hidden" name="nim" value="<?= e($nim) ?>">

        <div class="field">
            <label for="nim_tampil">NIM</label>
            <input type="text" id="nim_tampil" value="<?= e($nim) ?>" readonly>
            <small class="muted">NIM adalah kunci data dan tidak dapat diubah.</small>
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
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
</main>
</body>
</html>
