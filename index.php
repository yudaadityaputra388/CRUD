<?php
require 'koneksi.php';

$hasil = $conn->query('SELECT nim, nama, program_studi, alamat FROM mahasiswa ORDER BY nama ASC');
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="page-head">
        <div>
            <h1>Data Mahasiswa</h1>
            <p class="muted"><?= $hasil->num_rows ?> mahasiswa terdaftar</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Data</a>
    </header>

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['tipe']) ?>" role="status"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>

    <div class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Alamat</th>
                    <th class="col-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($hasil->num_rows === 0): ?>
                <tr>
                    <td colspan="6" class="empty">Belum ada data. Klik <strong>Tambah Data</strong> untuk menambahkan mahasiswa pertama.</td>
                </tr>
            <?php else: $no = 1; while ($row = $hasil->fetch_assoc()): ?>
                <tr>
                    <td data-label="No"><?= $no++ ?></td>
                    <td data-label="NIM"><?= e($row['nim']) ?></td>
                    <td data-label="Nama"><?= e($row['nama']) ?></td>
                    <td data-label="Program Studi"><span class="badge"><?= e($row['program_studi']) ?></span></td>
                    <td data-label="Alamat"><?= nl2br(e($row['alamat'])) ?></td>
                    <td data-label="Aksi" class="col-aksi">
                        <div class="aksi">
                            <a href="edit.php?nim=<?= urlencode($row['nim']) ?>" class="btn btn-edit btn-sm">Edit</a>
                            <form method="post" action="hapus.php" class="form-hapus" data-nama="<?= e($row['nama']) ?>">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="nim" value="<?= e($row['nim']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
// Konfirmasi sebelum menghapus data
document.querySelectorAll('.form-hapus').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
        var nama = form.dataset.nama;
        if (!confirm('Yakin ingin menghapus data "' + nama + '"? Tindakan ini tidak dapat dibatalkan.')) {
            ev.preventDefault();
        }
    });
});
</script>
</body>
</html>
