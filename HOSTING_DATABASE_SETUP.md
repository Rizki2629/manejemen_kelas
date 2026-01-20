# Setup Database Hosting sdngu09.my.id

Panduan lengkap untuk mengkonfigurasi aplikasi **Manajemen Kelas** agar terhubung dengan database di hosting sdngu09.my.id.

## 📋 Langkah-langkah Setup

### 1. Persiapan Database di Hosting

1. **Login ke Control Panel** hosting sdngu09.my.id (cPanel/DirectAdmin)
2. **Buat Database Baru**:
   - Masuk ke menu "MySQL Databases" atau "Database"
   - Buat database dengan nama: `manajemen_kelas` atau `sdngu09_manajemen_kelas`
   - Catat nama database lengkap (biasanya dengan prefix)

3. **Buat User Database**:
   - Buat user baru dengan username yang mudah diingat
   - Set password yang kuat
   - Catat username lengkap (biasanya dengan prefix)

4. **Berikan Privileges**:
   - Tambahkan user ke database
   - Berikan ALL PRIVILEGES
   - Pastikan user bisa SELECT, INSERT, UPDATE, DELETE, CREATE, DROP

### 2. Update Konfigurasi Aplikasi

#### File `.env` (untuk production di hosting)

```bash
# Environment
CI_ENVIRONMENT = production

# App Configuration
app.baseURL = 'http://sdngu09.my.id/'
app.forceGlobalSecureRequests = false

# Database Configuration - UPDATE DENGAN KREDENSIAL HOSTING ANDA
database.default.hostname = localhost
database.default.database = [nama_database_lengkap]
database.default.username = [username_database_lengkap] 
database.default.password = [password_database]
database.default.DBDriver = MySQLi
database.default.port = 3306

# Security
encryption.key = [generate_32_character_key]
```

#### Contoh Konfigurasi Database Hosting

```bash
# Contoh umum format hosting
database.default.hostname = localhost
database.default.database = sdngu09_manajemen_kelas
database.default.username = sdngu09_dbuser
database.default.password = SecurePassword123!
```

### 3. Upload dan Deploy

1. **Upload Files**:
   - Upload semua file project ke public_html atau folder domain
   - Pastikan struktur folder benar
   - Set permissions yang tepat (755 untuk folder, 644 untuk file)

2. **Konfigurasi Web Server**:
   - Pastikan `.htaccess` di folder public aktif
   - Document root harus mengarah ke folder `public/`

3. **Setup Database**:
   ```bash
   # Jika SSH tersedia
   php spark migrate
   php spark db:seed DemoSeeder
   php spark db:seed SiswaUsersSeeder
   php spark db:seed HabitsSeeder
   ```

### 4. Testing Koneksi

Jalankan script testing:
```bash
php setup_hosting_database.php
```

Script ini akan:
- ✅ Test koneksi ke database
- ✅ Cek keberadaan tabel
- ✅ Tampilkan troubleshooting tips

### 5. Login Default Setelah Setup

#### Admin
- **Username**: `admin`
- **Password**: `123456`

#### Siswa  
- **Username**: `[NISN siswa]`
- **Password**: `siswa123`

## 🔧 Troubleshooting

### Error: "Access denied for user"
- ✅ Periksa username dan password di `.env`
- ✅ Pastikan user database memiliki privileges yang cukup
- ✅ Cek apakah hosting mengizinkan koneksi dari IP tertentu

### Error: "Unknown database"
- ✅ Pastikan database sudah dibuat di control panel
- ✅ Periksa nama database di `.env` (perhatikan prefix)
- ✅ Cek case-sensitive (huruf besar/kecil)

### Error: "Can't connect to MySQL server"
- ✅ Periksa hostname di `.env`
- ✅ Pastikan MySQL service aktif
- ✅ Cek port (biasanya 3306)

### Error 500 Internal Server Error
- ✅ Cek file `.htaccess` 
- ✅ Pastikan PHP version compatible (7.4+)
- ✅ Set permissions folder writable/ ke 755
- ✅ Cek error logs di control panel

## 📁 Struktur File untuk Hosting

```
public_html/
├── app/
├── public/          # Document root harus di sini
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── vendor/
├── writable/        # Set permission 755
├── .env            # Konfigurasi database hosting
└── .htaccess
```

## 🔐 Keamanan Production

1. **Environment**: Set `CI_ENVIRONMENT = production`
2. **Debug**: Disable debug mode di production
3. **Encryption**: Generate key baru untuk production
4. **Permissions**: Set file permissions dengan benar
5. **HTTPS**: Pertimbangkan menggunakan SSL certificate

## 📞 Support

Jika mengalami kesulitan:
1. Cek error logs di hosting control panel
2. Jalankan script `setup_hosting_database.php`
3. Periksa kompatibilitas PHP dan MySQL version
4. Hubungi support hosting untuk bantuan database
