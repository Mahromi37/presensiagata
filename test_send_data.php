<?php

// Script untuk mengirim 1000 data tes ke tabel log_absen
// Menggunakan PDO untuk koneksi database langsung

// Konfigurasi database - SESUAIKAN dengan setting database Anda
// Untuk MySQL:
$host = '127.0.0.1';
$dbname = 'laravel'; // Ganti dengan nama database Anda
$username = 'root'; // Ganti dengan username database Anda
$password = ''; // Ganti dengan password database Anda

// Atau gunakan SQLite untuk testing (uncomment baris di bawah):
// $dsn = 'sqlite:test_database.db';

echo "=== Script Tes Pengiriman Data Presensi ===\n";
echo "Konfigurasi Database:\n";
echo "Host: $host\n";
echo "Database: $dbname\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? '(kosong)' : '***') . "\n\n";

try {
    // Untuk MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Atau untuk SQLite (uncomment baris di bawah dan comment baris di atas):
    // $pdo = new PDO($dsn);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database berhasil!\n\n";
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage() . "\n\n" .
        "Solusi:\n" .
        "1. Pastikan MySQL/MariaDB server sedang berjalan\n" .
        "2. Jika menggunakan XAMPP: Start Apache dan MySQL di XAMPP Control Panel\n" .
        "3. Jika menggunakan WAMP: Start WAMP server\n" .
        "4. Buat database dengan nama '$dbname' di phpMyAdmin atau MySQL console\n" .
        "5. Atau gunakan SQLite: Uncomment baris \$dsn dan comment baris PDO MySQL di atas\n\n" .
        "Untuk membuat database di MySQL:\n" .
        "   - Buka phpMyAdmin (http://localhost/phpmyadmin)\n" .
        "   - Buat database baru dengan nama '$dbname'\n" .
        "   - Import atau buat tabel log_absen\n\n" .
        "Edit file test_send_data.php untuk mengubah konfigurasi database.\n");
}

// Cek apakah tabel log_absen ada, jika tidak buat
try {
    $pdo->query("SELECT 1 FROM log_absen LIMIT 1");
    echo "Tabel log_absen sudah ada.\n\n";
} catch (PDOException $e) {
    echo "Tabel log_absen tidak ditemukan. Membuat tabel...\n";
    $createTable = "CREATE TABLE log_absen (
        id INT AUTO_INCREMENT PRIMARY KEY,
        data_raw TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    try {
        $pdo->exec($createTable);
        echo "Tabel log_absen berhasil dibuat.\n\n";
    } catch (PDOException $e) {
        die("Gagal membuat tabel: " . $e->getMessage() . "\n");
    }
}

$successCount = 0;
$failCount = 0;

echo "Memulai penyimpanan 1000 data presensi ke database...\n";
echo "Tekan Ctrl+C untuk membatalkan.\n\n";

for ($i = 1; $i <= 1000; $i++) {
    // Generate data presensi acak
    $pin = rand(1000, 9999); // PIN acak 4 digit
    $status_scan = rand(0, 1); // 0 atau 1
    $scan = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days')); // Waktu scan acak dalam 30 hari terakhir

    $data = [
        'data' => [
            'pin' => $pin,
            'status_scan' => $status_scan,
            'scan' => $scan,
        ]
    ];

    $data_raw = json_encode($data);

    try {
        $stmt = $pdo->prepare("INSERT INTO log_absen (data_raw, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute([$data_raw]);
        $successCount++;
        echo "Data ke-$i berhasil disimpan (PIN: $pin, Status: $status_scan, Scan: $scan).\n";
    } catch (PDOException $e) {
        $failCount++;
        echo "Error pada data ke-$i: " . $e->getMessage() . "\n";
    }

    // Delay kecil untuk menghindari overload
    usleep(1000); // 1ms delay
}

echo "\n=== PENYIMPANAN SELESAI ===\n";
echo "Total data diproses: 1000\n";
echo "Berhasil disimpan: $successCount\n";
echo "Gagal disimpan: $failCount\n";

if ($successCount > 0) {
    echo "\nData tersimpan di tabel 'log_absen' dalam database '$dbname'.\n";
    echo "Anda dapat memeriksa data dengan query: SELECT * FROM log_absen ORDER BY id DESC LIMIT 10;\n";
}

$pdo = null;

?>