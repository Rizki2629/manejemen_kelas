# 🚀 Panduan Setup Heroku PostgreSQL untuk SDN GU09

## 📦 Persiapan

### 1. Buat Akun Heroku (GRATIS)

1. **Kunjungi:** https://signup.heroku.com/
2. **Daftar** dengan email Anda
3. **Pilih role:** Student/Hobbyist
4. **Primary development language:** PHP
5. **Verifikasi email** Anda

### 2. Install Heroku CLI

**Windows:**

1. Download: https://devcenter.heroku.com/articles/heroku-cli
2. Install file `.exe` yang didownload
3. Restart terminal/PowerShell

**Verifikasi instalasi:**

```powershell
heroku --version
```

### 3. Login ke Heroku CLI

```powershell
heroku login
```

Tekan tombol apapun → akan buka browser → login dengan akun Heroku Anda.

---

## 🗄️ Buat Database PostgreSQL di Heroku

### 1. Buat Aplikasi Heroku (Container untuk Database)

```powershell
cd c:\Users\rizki\OneDrive\Documents\manajemen_kelas
heroku create sdngu09-app
```

Output:

```
Creating ⬢ sdngu09-app... done
https://sdngu09-app.herokuapp.com/ | https://git.heroku.com/sdngu09-app.git
```

### 2. Tambahkan PostgreSQL Add-on (GRATIS)

```powershell
heroku addons:create heroku-postgresql:mini -a sdngu09-app
```

**CATATAN:** Heroku sekarang pakai plan **Mini** ($5/bulan) sebagai free tier. Alternatif:

- Pakai **Neon** (PostgreSQL gratis unlimited): https://neon.tech
- Pakai **Supabase** (PostgreSQL + API gratis): https://supabase.com
- Pakai **Railway** (PostgreSQL gratis 500MB): https://railway.app

**Saya rekomendasikan Railway (paling mudah)** - lihat bagian Alternative di bawah.

### 3. Dapatkan Database Credentials

```powershell
heroku config -a sdngu09-app
```

Output contoh:

```
=== sdngu09-app Config Vars
DATABASE_URL: postgres://username:password@host:5432/database_name
```

**Parse URL ini:**

- **Host:** `ec2-xx-xxx-xxx-xx.compute-1.amazonaws.com`
- **Port:** `5432`
- **Database:** `d123abc456def`
- **Username:** `abcdefghij`
- **Password:** `1234567890abcdef`

---

## 🔧 Konfigurasi di CodeIgniter `.env`

Tambahkan konfigurasi PostgreSQL di `.env`:

```properties
# PostgreSQL Heroku Configuration
database.default.hostname = ec2-xx-xxx-xxx-xx.compute-1.amazonaws.com
database.default.database = d123abc456def
database.default.username = abcdefghij
database.default.password = 1234567890abcdef
database.default.DBDriver = Postgre
database.default.port     = 5432
database.default.DBPrefix =
database.default.charset  = utf8

# SSL Required for Heroku PostgreSQL
database.default.encrypt = [
    'ssl_verify' => false
]
```

**PENTING:** Ganti dengan credentials Anda dari `heroku config`.

---

## 🛠️ Install PostgreSQL Client Lokal (untuk Testing)

### Windows:

1. **Download pgAdmin 4:** https://www.pgadmin.org/download/
2. Install pgAdmin
3. **Buka pgAdmin** → Add New Server:
   - **Name:** SDN GU09 Heroku
   - **Host:** (dari DATABASE_URL)
   - **Port:** 5432
   - **Database:** (dari DATABASE_URL)
   - **Username:** (dari DATABASE_URL)
   - **Password:** (dari DATABASE_URL)
   - **SSL Mode:** Require

---

## 🚀 ALTERNATIF: Railway (LEBIH MUDAH & GRATIS)

Railway lebih user-friendly untuk pemula:

### 1. Buat Akun Railway

1. Kunjungi: https://railway.app/
2. **Sign up with GitHub** (paling mudah)
3. Verifikasi akun

### 2. Buat PostgreSQL Database

1. Di Dashboard Railway, klik **"New Project"**
2. Pilih **"Provision PostgreSQL"**
3. Database akan dibuat otomatis (30 detik)

### 3. Dapatkan Credentials

1. Klik database yang baru dibuat
2. Tab **"Connect"** → pilih **"PostgreSQL"**
3. Salin credentials:
   ```
   Host: containers-us-west-xxx.railway.app
   Port: 6543
   Database: railway
   Username: postgres
   Password: xxxxxxxxxxxxx
   ```

### 4. Copy Connection String

Railway memberikan **Connection URL** langsung:

```
postgresql://postgres:xxxxx@containers-us-west-xxx.railway.app:6543/railway
```

---

## 📄 Update `app/Config/Database.php`

Tambahkan support untuk PostgreSQL:

```php
// ...existing code...

public array $default = [
    'DSN'          => '',
    'hostname'     => getenv('database.default.hostname') ?: 'localhost',
    'username'     => getenv('database.default.username') ?: 'root',
    'password'     => getenv('database.default.password') ?: '',
    'database'     => getenv('database.default.database') ?: 'sdngu09',
    'DBDriver'     => getenv('database.default.DBDriver') ?: 'MySQLi',
    'DBPrefix'     => '',
    'pConnect'     => false,
    'DBDebug'      => ENVIRONMENT !== 'production',
    'charset'      => 'utf8',
    'DBCollat'     => 'utf8_general_ci',
    'swapPre'      => '',
    'encrypt'      => false,
    'compress'     => false,
    'strictOn'     => false,
    'failover'     => [],
    'port'         => getenv('database.default.port') ?: 3306,
    'numberNative' => false,
];

// ...existing code...
```

---

## ✅ Test Koneksi

Jalankan command ini untuk test koneksi:

```powershell
php spark db:table users
```

Jika berhasil, akan tampilkan struktur tabel.

---

## 📊 Migrasi Data MySQL → PostgreSQL

Saya akan buatkan script PHP untuk migrasi otomatis dari `sdngu09.sql` ke PostgreSQL.

---

## ❓ Pilihan Anda:

**A. Heroku PostgreSQL** ($5/bulan, paling stabil)
**B. Railway** (GRATIS 500MB, paling mudah) ✅ **REKOMENDASI**
**C. Neon** (GRATIS unlimited, cepat)
**D. Supabase** (GRATIS + bonus API)

**Mana yang Anda pilih?** Kasih tahu saya, nanti saya guide step-by-step dan buatkan script migrasinya!

---

**Dibuat:** 19 Januari 2026  
**Status:** Menunggu pilihan platform  
**Rekomendasi:** Railway (paling mudah untuk pemula)
