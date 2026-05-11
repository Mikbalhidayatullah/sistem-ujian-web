# Roadmap Ekosistem Sekolah

Dokumen ini menjadi acuan untuk memecah sistem sekolah ke beberapa aplikasi yang terintegrasi tetapi tetap mudah dirawat.

## Aplikasi yang dipakai

### 1. Sistem ujian

- Folder saat ini: `C:\Users\zeroTwo\Herd\sistemujianapp`
- Peran: pelaksanaan ujian, anti-kecurangan, hasil, rekap nilai guru
- Domain lokal yang dipakai sekarang: `sistemujianapp.test`

### 2. Portal sekolah

- Folder yang disarankan: `C:\Users\zeroTwo\Herd\portal-sekolah`
- Domain lokal yang disarankan: `portal-sekolah.test`
- Peran:
  - halaman profil sekolah
  - berita dan pengumuman
  - halaman guru dan staf
  - agenda sekolah
  - unduhan dokumen
  - dashboard portal internal

### 3. Pusat autentikasi / SSO

- Folder yang disarankan: `C:\Users\zeroTwo\Herd\pusat-auth-sekolah`
- Domain lokal yang disarankan: `auth-sekolah.test`
- Peran:
  - login terpusat
  - manajemen user
  - role dan permission lintas aplikasi
  - token akses antar aplikasi
  - sesi login tunggal

## Alur integrasi yang disarankan

1. User membuka `portal-sekolah.test` atau `sistemujianapp.test`.
2. Jika belum login untuk area internal, aplikasi mengarahkan user ke `auth-sekolah.test`.
3. Setelah login berhasil, aplikasi SSO mengembalikan user ke aplikasi asal.
4. Role user menentukan akses ke portal admin, guru, atau modul lain.
5. Untuk siswa, login akun bisa dipakai di portal sekolah, sedangkan ujian tetap bisa memakai mode tamu jika kebijakan sekolah menginginkannya.

## Strategi implementasi bertahap

### Tahap 1

- Stabilkan `sistemujianapp`
- Tambahkan test dasar dan dokumentasi
- Pastikan data role admin dan guru rapi

### Tahap 2

- Buat `portal-sekolah`
- Siapkan halaman publik dan dashboard admin dasar
- Pisahkan identitas visual sekolah dari sistem ujian

### Tahap 3

- Buat `pusat-auth-sekolah`
- Pindahkan user internal ke pusat autentikasi
- Integrasikan login admin dan guru dari ujian dan portal ke SSO

### Tahap 4

- Tambahkan aplikasi lain jika dibutuhkan:
  - e-learning
  - absensi
  - perpustakaan
  - keuangan

## Keputusan teknis yang disarankan

- Tetap gunakan project Laravel terpisah untuk tiap aplikasi.
- Gunakan API atau token SSO untuk integrasi.
- Simpan data domain bisnis di aplikasi masing-masing, jangan semua tabel dipaksa masuk satu project.
- Untuk lokal dengan Herd, gunakan domain yang konsisten agar migrasi ke hosting lebih mudah.
