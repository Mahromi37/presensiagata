<?php

// Script untuk mengirim 1000 data tes ke tabel log_absen
// VERSI SQLITE - Lebih mudah untuk testing tanpa setup MySQL

$databaseFile = 'test_presensi.db';

echo "=== Script Tes Pengiriman Data Presensi (SQLite) ===\n";
echo "File Database: $databaseFile\n\n";

try {
    $pdo = new PDO("sqlite:$databaseFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Koneksi database SQLite berhasil!\n\n";
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage() . "\n");
}

// Buat tabel log_absen jika belum ada
$createTable = "CREATE TABLE IF NOT EXISTS log_absen (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    data_raw TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
try {
    $pdo->exec($createTable);
    echo "Tabel log_absen siap digunakan.\n\n";
} catch (PDOException $e) {
    die("Gagal membuat tabel: " . $e->getMessage() . "\n");
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
        $stmt = $pdo->prepare("INSERT INTO log_absen (data_raw, created_at, updated_at) VALUES (?, datetime('now'), datetime('now'))");
        $stmt->execute([$data_raw]);
        $successCount++;
        echo "Data ke-$i berhasil disimpan (PIN: $pin, Status: $status_scan).\n";
    } catch (PDOException $e) {
        $failCount++;
        echo "Error pada data ke-$i: " . $e->getMessage() . "\n";
    }

    // Delay kecil untuk menghindari overload
    usleep(500); // 0.5ms delay
}

echo "\n=== PENYIMPANAN SELESAI ===\n";
echo "Total data diproses: 1000\n";
echo "Berhasil disimpan: $successCount\n";
echo "Gagal disimpan: $failCount\n";

if ($successCount > 0) {
    echo "\nData tersimpan di file '$databaseFile'.\n";
    echo "Anda dapat memeriksa data dengan membuka file database menggunakan DB Browser for SQLite.\n";
    echo "Atau gunakan query: SELECT * FROM log_absen ORDER BY id DESC LIMIT 10;\n";
}

$pdo = null;

?>