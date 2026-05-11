# Sistem Ujian Sekolah

Project ini adalah aplikasi ujian online berbasis Laravel untuk kebutuhan guru, admin, dan siswa tanpa akun siswa khusus. Fokus utamanya adalah pembuatan soal cepat, pelaksanaan ujian dengan token dan PIN, penilaian otomatis, serta monitoring pelanggaran selama ujian berjalan.

## Fitur utama

- Login khusus admin dan guru.
- Admin dapat membuat dan mengubah akun guru.
- Guru dapat membuat mata pelajaran, membuat ujian, menambah soal satu per satu, atau import cepat dengan template.
- Soal mendukung media gambar dan video.
- Siswa masuk ujian hanya dengan nama lengkap, token, dan PIN.
- Nilai dihitung otomatis setelah submit.
- Guru bisa melihat rekapan nilai, jawaban benar dan salah, serta export Excel.
- Monitoring pelanggaran ujian dan auto submit saat batas pelanggaran tercapai.
- Ujian dapat dibuka atau ditutup manual oleh guru selain mengikuti jadwal.

## Akun awal

Seeder bawaan membuat akun admin berikut:

- Email: `admin@example.com`
- Password: `password123`

## Menjalankan project di lokal

Jika Anda memakai Herd, lebih aman gunakan PHP Herd saat menjalankan perintah Artisan.

```powershell
& "C:\Users\zeroTwo\.config\herd\bin\php.bat" artisan migrate --seed
npm install
npm run build
```

Untuk pengembangan harian:

```powershell
& "C:\Users\zeroTwo\.config\herd\bin\php.bat" artisan serve
npm run dev
```

## Migrasi ke MySQL dan persiapan hosting

Project lokal ini sekarang sudah disiapkan memakai MySQL Laragon dengan database `sistem_ujian_web`.

Untuk backup data sebelum pindah hosting:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\export-mysql-backup.ps1
```

Untuk import dump SQL ke server MySQL tujuan:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\import-mysql-backup.ps1 -SqlFile .\backups\nama-file.sql -DbHost HOST_MYSQL -Database DB_HOSTING -Username USER_HOSTING -Password PASSWORD_HOSTING
```

Panduan lengkap deploy aplikasi dan migrasi data ada di [docs/hosting-migration.md](docs/hosting-migration.md).

## Test yang sudah disiapkan

Smoke test sekarang mengecek:

- halaman awal siswa dan login guru/admin tampil benar
- admin bisa login dan diarahkan ke dashboard admin
- guru tidak bisa membuka halaman admin
- siswa bisa masuk ujian, submit, dan melihat hasil tanpa akun siswa

Jalankan test:

```powershell
& "C:\Users\zeroTwo\.config\herd\bin\php.bat" artisan test
```

## Rencana ekosistem

Project ini akan menjadi salah satu aplikasi dalam ekosistem sekolah yang lebih besar:

- `sistemujianapp` untuk ujian online
- `portal-sekolah` untuk web dan portal sekolah
- `pusat-auth-sekolah` untuk SSO

Rancangan folder, domain lokal, dan urutan implementasi ada di file [docs/ecosystem-roadmap.md](docs/ecosystem-roadmap.md).
