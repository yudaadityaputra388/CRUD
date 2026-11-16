<?php
// koneksi.php - Koneksi MySQLi + fungsi bantu (helper)

session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_akademik';

// Laporkan error MySQLi sebagai exception agar mudah ditangani
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa pengaturan di koneksi.php.');
}

/** Escape output HTML (mencegah XSS) */
function e($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/** Simpan pesan sementara (flash message) untuk halaman berikutnya */
function set_flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

/** Ambil lalu hapus pesan flash */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/** Validasi input form mahasiswa. Mengembalikan array error (kosong = valid) */
function validasi_mahasiswa(string $nim, string $nama, string $prodi, string $alamat): array
{
    $error = [];

    if ($nim === '' || !preg_match('/^[A-Za-z0-9]{1,20}$/', $nim)) {
        $error[] = 'NIM wajib diisi (huruf/angka saja, maksimal 20 karakter).';
    }
    if ($nama === '' || mb_strlen($nama) > 100) {
        $error[] = 'Nama wajib diisi (maksimal 100 karakter).';
    }
    if ($prodi === '' || mb_strlen($prodi) > 50) {
        $error[] = 'Program studi wajib diisi (maksimal 50 karakter).';
    }
    if ($alamat === '') {
        $error[] = 'Alamat wajib diisi.';
    }

    return $error;
}

/** Token CSRF sederhana untuk semua form POST */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
