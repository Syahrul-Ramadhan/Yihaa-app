<?php
// Ganti isinya sesuai dengan file .env kamu
$host = 'isi_DB_HOST_dari_env';      // Contoh: aws-0-ap-southeast-1.pooler.supabase.com
$port = 'isi_DB_PORT_dari_env';      // Contoh: 6543 atau 5432
$dbname = 'isi_DB_DATABASE_dari_env';// Contoh: postgres
$username = 'isi_DB_USERNAME_dari_env';
$password = 'isi_DB_PASSWORD_dari_env';

try {
    echo "Mencoba menghubungkan ke Supabase...<br>";

    // Setting koneksi
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    // Kita coba buat koneksi PDO murni
    $pdo = new PDO($dsn, $username, $password);

    // Jika kode ini jalan, berarti koneksi sukses
    echo "<h1>✅ SUKSES! Koneksi Database Berhasil.</h1>";

    // Tes fitur Emulate Prepares (yang bikin error tadi)
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    echo "Fitur Emulate Prepares berhasil diaktifkan.<br>";

} catch (PDOException $e) {
    echo "<h1>❌ GAGAL! Koneksi Database Bermasalah.</h1>";
    echo "<strong>Pesan Error:</strong> " . $e->getMessage();
}
?>
