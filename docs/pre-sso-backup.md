# Backup Snapshot Sebelum Integrasi SSO

Dokumen ini menandai snapshot project `sistemujianapp` sebelum proses integrasi ke SSO dimulai.

## Tujuan snapshot

- Menyimpan versi stabil terakhir dari sistem ujian mandiri.
- Menjaga agar alur login admin dan guru saat ini tetap bisa dipulihkan.
- Menyediakan titik aman sebelum perubahan arsitektur autentikasi lintas aplikasi.

## Status project saat snapshot ini dibuat

- Admin dan guru login langsung di project ujian.
- Siswa masuk ujian dengan nama lengkap, token, dan PIN tanpa akun siswa khusus.
- Guru bisa membuat ujian, mengatur akses manual, menambah soal, import cepat, melihat pelanggaran, dan export nilai.
- Rekapan nilai dan hasil siswa sudah tersedia.
- Smoke test inti sudah lulus.

## Backup yang disiapkan

Backup dibuat sebagai arsip `.zip` ke folder `backups/` menggunakan script:

- `scripts/create-pre-sso-backup.ps1`

Arsip ini sengaja hanya menyertakan file penting project dan mengecualikan folder berat atau file sementara seperti:

- `vendor`
- `node_modules`
- `storage/logs`
- `bootstrap/cache`

## Cara membuat backup ulang

Jalankan dari root project:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\create-pre-sso-backup.ps1
```

## Cara restore draft ini

1. Extract file zip backup ke folder tujuan baru.
2. Jalankan `composer install`.
3. Jalankan `npm install`.
4. Jalankan `npm run build`.
5. Pastikan `.env` dan database sesuai kebutuhan lokal Anda.

## Catatan

Setelah integrasi SSO dimulai, backup ini menjadi acuan jika Anda ingin:

- rollback ke versi login lokal lama
- membandingkan alur auth sebelum dan sesudah SSO
- menyimpan versi presentasi atau demo yang berdiri sendiri
