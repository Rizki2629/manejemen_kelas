# 🌐 Setup MongoDB Atlas untuk SDN GU09

## 📋 Langkah-langkah Setup MongoDB Atlas (Cloud)

### 1. Buat Akun MongoDB Atlas (GRATIS)

1. **Kunjungi:** https://www.mongodb.com/cloud/atlas/register
2. **Daftar** dengan email atau Google account
3. **Verifikasi email** Anda

### 2. Buat Cluster (Database)

1. **Setelah login**, klik **"Build a Database"** atau **"Create"**
2. **Pilih plan:**
   - ✅ **Shared (FREE)** - M0 Sandbox (512MB storage, cukup untuk testing)
   - Region: **Singapore** (terdekat dengan Indonesia)
3. **Cluster Name:** `sdngu09-cluster`
4. Klik **"Create Cluster"** (tunggu 3-5 menit)

### 3. Setup Database Access (User & Password)

1. Di sidebar kiri, klik **"Database Access"**
2. Klik **"Add New Database User"**
3. **Authentication Method:** Password
4. **Username:** `sdngu09_admin`
5. **Password:** Generate password yang kuat (simpan di `.env` nanti)
   - Contoh: `MyStrongP@ssw0rd123`
6. **Database User Privileges:**
   - Pilih **"Read and write to any database"**
7. Klik **"Add User"**

### 4. Setup Network Access (IP Whitelist)

1. Di sidebar kiri, klik **"Network Access"**
2. Klik **"Add IP Address"**
3. **Pilih salah satu:**
   - ✅ **Allow Access from Anywhere** (0.0.0.0/0) - untuk development/testing
   - Atau tambahkan IP spesifik (IP publik Anda)
4. Klik **"Confirm"**

### 5. Dapatkan Connection String

1. Kembali ke **"Database"** di sidebar
2. Klik tombol **"Connect"** pada cluster Anda
3. Pilih **"Connect your application"**
4. **Driver:** PHP, **Version:** 1.13 or later
5. **Copy Connection String:**
   ```
   mongodb+srv://sdngu09_admin:<password>@sdngu09-cluster.xxxxx.mongodb.net/?retryWrites=true&w=majority
   ```
6. **Ganti `<password>`** dengan password user yang tadi dibuat
7. **Simpan connection string** ini (akan dipakai di `.env`)

---

## ⚙️ Konfigurasi di `.env`

Tambahkan ke file `.env`:

```properties
# MongoDB Atlas Configuration
mongodb.connection_string = "mongodb+srv://sdngu09_admin:MyStrongP@ssw0rd123@sdngu09-cluster.xxxxx.mongodb.net/?retryWrites=true&w=majority"
mongodb.database = sdngu09
mongodb.timeout = 30000
```

**PENTING:**

- Ganti `MyStrongP@ssw0rd123` dengan password Anda
- Ganti `sdngu09-cluster.xxxxx` dengan cluster ID Anda dari connection string

---

## ✅ Testing Koneksi

Setelah setup selesai, jalankan script test koneksi (akan saya buatkan).

---

**Status:** Setup Manual - Ikuti langkah di atas  
**Estimasi Waktu:** 10-15 menit  
**Biaya:** FREE (MongoDB Atlas M0)
