# Hosting dan Migrasi Data

Panduan ini dipakai saat project `sistem-ujian-web` dipindahkan dari lokal ke hosting.

## 1. Tentukan jalur migrasi

Pilih salah satu jalur berikut:

- `Fresh install`: dipakai jika database hosting masih kosong. Jalankan migrasi Laravel di server, lalu seed jika memang ingin akun awal bawaan.
- `Migrasi data penuh`: dipakai jika data lokal sudah berisi guru, mapel, ujian, soal, hasil, dan riwayat pelanggaran yang ingin dibawa apa adanya. Jalur ini memakai dump SQL dari lokal lalu diimport ke hosting.

Jika data lokal sudah dipakai, utamakan `Migrasi data penuh` agar seluruh isi tabel ikut terbawa.

## 2. Siapkan file aplikasi

Yang perlu ada di server:

- source code project
- folder `vendor`
- folder `public/build` hasil `npm run build`
- folder `storage/app/public` jika ada media soal yang sudah diupload
- file `.env` production

Template konfigurasi production sudah disiapkan di [.env.production.example](C:/Users/zeroTwo/Herd/sistem-ujian-web/.env.production.example).

## 3. Export database dari lokal

Jalankan dari root project:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\export-mysql-backup.ps1
```

Hasil dump akan dibuat ke folder `backups` dengan nama seperti `sistem_ujian_web-20260508-123456.sql`.

Jika ingin lokasi lain:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\export-mysql-backup.ps1 -OutputDirectory backups\hosting
```

Jika kredensial lokal berbeda dari `.env`, Anda bisa override:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\export-mysql-backup.ps1 -Database nama_db -DbHost 127.0.0.1 -Port 3306 -Username root
```

## 4. Import dump ke database hosting

Jika hosting mengizinkan koneksi MySQL remote, Anda bisa import langsung dari lokal:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\import-mysql-backup.ps1 -SqlFile .\backups\nama-file.sql -DbHost HOST_MYSQL -Port 3306 -Database DB_HOSTING -Username USER_HOSTING -Password PASSWORD_HOSTING -CreateDatabase
```

Jika hosting tidak membuka akses MySQL remote, upload file `.sql` lalu import lewat phpMyAdmin atau panel database hosting.

## 5. Urutan deploy yang aman

Untuk server yang benar-benar kosong:

1. upload source code
2. upload atau install `vendor`
3. jalankan `npm run build` lokal lalu upload `public/build`
4. buat file `.env` production
5. jalankan `php artisan key:generate`
6. jalankan `php artisan storage:link`
7. jalankan `php artisan migrate --force`
8. import data jika memakai dump SQL
9. jalankan `php artisan config:cache`
10. jalankan `php artisan route:cache`
11. jalankan `php artisan view:cache`

Catatan penting:

- Jika Anda memakai `Migrasi data penuh`, jangan jalankan `migrate:fresh` di hosting karena itu akan menghapus tabel.
- Jika dump SQL sudah berisi schema dan data lengkap, Anda boleh skip `db:seed`.
- Setelah import dump penuh, cukup jalankan `php artisan optimize:clear` lalu cache ulang config dan route.

## 6. Media upload dan file pendukung

Jika soal sudah punya gambar atau video upload manual, pindahkan juga isi folder berikut:

- `storage/app/public`

Setelah itu pastikan symbolic link public storage tersedia:

```powershell
php artisan storage:link
```

## 7. Checklist sebelum go live

- `.env` production sudah mengarah ke database hosting
- `APP_URL` sudah memakai domain final
- `APP_DEBUG=false`
- database hosting sudah terisi
- folder `storage` dan `bootstrap/cache` writable
- halaman login terbuka
- akun admin bisa login
- media soal tampil
- export nilai berjalan
