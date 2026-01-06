# Script Tes Pengiriman Data Presensi

Script ini digunakan untuk melakukan tes pengiriman data sebanyak 1000 data pada sistem presensi karyawan berbasis Laravel.

## Cara Penggunaan

### 1. Persiapan Database
Pastikan database MySQL/MariaDB Anda sudah:
- Server database sedang berjalan
- Database sudah dibuat
- Tabel `log_absen` sudah ada dengan struktur:
  ```sql
  CREATE TABLE log_absen (
      id INT AUTO_INCREMENT PRIMARY KEY,
      data_raw TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```

### 2. Konfigurasi Script
Edit file `test_send_data.php` dan sesuaikan konfigurasi database:
```php
$host = '127.0.0.1';          // Host database
$dbname = 'laravel';          // Nama database
$username = 'root';           // Username database
$password = '';               // Password database
```

### 3. Menjalankan Script
```bash
php test_send_data.php
```

## Fitur Script

- **Generate Data Acak**: Script akan membuat 1000 data presensi dengan:
  - PIN: 4 digit acak (1000-9999)
  - Status Scan: 0 atau 1
  - Waktu Scan: Acak dalam 30 hari terakhir

- **Penyimpanan Database**: Data disimpan langsung ke tabel `log_absen` dalam format JSON

- **Progress Report**: Menampilkan progress penyimpanan setiap data

- **Error Handling**: Menangani error koneksi database dan query

## Output
Script akan menampilkan:
- Status koneksi database
- Progress penyimpanan (Data ke-X berhasil disimpan)
- Ringkasan akhir (Berhasil: X, Gagal: Y)

## Catatan
- Script menggunakan PDO untuk koneksi database
- Delay 1ms antara setiap insert untuk menghindari overload
- Data disimpan dalam format JSON di kolom `data_raw`

## Troubleshooting
Jika koneksi database gagal:
1. Pastikan MySQL/MariaDB server sedang berjalan
2. Periksa konfigurasi host, database, username, password
3. Pastikan database dan tabel sudah ada
4. Periksa firewall dan izin akses database