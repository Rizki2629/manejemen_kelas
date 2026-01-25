# Sistem Manajemen Kelas & Habit Tracking

Aplikasi web untuk manajemen kelas dan tracking kebiasaan harian siswa menggunakan CodeIgniter 4.

## Fitur Utama

### 🎯 **Untuk Siswa**
- Dashboard habit tracking harian (7 kebiasaan baik)
- Laporan bulanan dengan visualisasi grafik
- Profil siswa dan monitoring progress

### 👨‍🏫 **Untuk Guru/Admin**
- Manajemen data siswa dan pengguna
- Input dan rekap absensi
- Manajemen nilai siswa
- Dashboard monitoring

### 🔐 **Sistem Autentikasi**
- Role-based access control (Admin, Guru, Siswa)
- Password encryption dengan bcrypt
- Session management

## Instalasi

1. Clone repository
```bash
git clone https://github.com/RizkiDev26/manajemen_kelas.git
cd manajemen_kelas
```

2. Install dependencies
```bash
composer install
```

3. Setup database
- Buat database MySQL
- Copy `.env.example` ke `.env`
- Konfigurasi database di file `.env`

4. Jalankan migrations
```bash
php spark migrate
php spark db:seed DemoSeeder
```

5. Jalankan aplikasi
```bash
php spark serve
```

## Login Default

### Admin
- Username: `admin`
- Password: `123456`

### Siswa
- Username: `[NISN siswa]`
- Password: `siswa123`

## Teknologi

- **Framework**: CodeIgniter 4.6.1
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Alpine.js
- **Authentication**: Custom dengan bcrypt

## Struktur Database

- `users` - Data pengguna sistem
- `siswa` - Data siswa
- `habits` - Master data kebiasaan
- `habit_logs` - Log tracking harian
- `absensi` - Data absensi
- `nilai` - Data nilai siswa

## Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

MIT License
