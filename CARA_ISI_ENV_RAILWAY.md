# 🔧 CARA ISI CREDENTIALS RAILWAY KE FILE .ENV

## 📸 Lihat Screenshot Railway Anda

Dari tab **"Variables"** di Railway, Anda akan lihat:

```
PGHOST           = ******* (disembunyikan)
PGPORT           = ******* (disembunyikan)
PGDATABASE       = ******* (disembunyikan)
PGUSER           = ******* (disembunyikan)
PGPASSWORD       = ******* (disembunyikan)
```

## 👁️ CARA LIHAT NILAI YANG TERSEMBUNYI:

1. **Klik ikon "mata" (👁️)** atau **"..." (titik tiga)** di sebelah kanan setiap variable
2. Pilih **"Copy"** atau **"Reveal"**
3. Nilai akan terlihat/tercopy

## ✏️ CARA ISI DI FILE `.env`:

### Step 1: Buka file `.env`

Sudah saya update dengan template baru.

### Step 2: Copy nilai dari Railway

Di Railway Variables tab, **klik ikon copy** atau **reveal** untuk setiap variable:

**PGHOST:**

- Klik copy di sebelah PGHOST
- Contoh nilai: `containers-us-west-123.railway.app`

**PGPORT:**

- Klik copy di sebelah PGPORT
- Contoh nilai: `6543` atau `5432`

**PGDATABASE:**

- Klik copy di sebelah PGDATABASE
- Contoh nilai: `railway` atau `postgres`

**PGUSER:**

- Klik copy di sebelah PGUSER
- Contoh nilai: `postgres`

**PGPASSWORD:**

- Klik copy di sebelah PGPASSWORD
- Contoh nilai: `ABcd1234EFgh5678IJkl` (password random panjang)

### Step 3: Paste ke `.env`

Buka file `.env`, cari bagian ini:

```properties
database.default.hostname = [COPY_PGHOST_DISINI]
database.default.port     = [COPY_PGPORT_DISINI]
database.default.database = [COPY_PGDATABASE_DISINI]
database.default.username = [COPY_PGUSER_DISINI]
database.default.password = [COPY_PGPASSWORD_DISINI]
```

**Ganti** dengan nilai yang sudah Anda copy, **TANPA TANDA KUTIP**:

```properties
database.default.hostname = containers-us-west-123.railway.app
database.default.port     = 6543
database.default.database = railway
database.default.username = postgres
database.default.password = ABcd1234EFgh5678IJkl
```

### Step 4: Simpan file `.env`

Tekan **Ctrl + S** untuk save.

---

## ✅ CONTOH HASIL AKHIR `.env`:

```properties
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'
app.forceGlobalSecureRequests = false

# ============================================
# DATABASE CONFIGURATION - Railway PostgreSQL
# ============================================

database.default.hostname = containers-us-west-123.railway.app
database.default.port     = 6543
database.default.database = railway
database.default.username = postgres
database.default.password = ABcd1234EFgh5678IJkl
database.default.DBDriver = Postgre
database.default.DBPrefix =

# SSL Configuration (Required untuk Railway)
database.default.encrypt.ssl_verify = false

# Encryption key
encryption.key = ""

# End
```

---

## 🧪 TEST KONEKSI SETELAH ISI `.env`:

Di terminal PowerShell:

```powershell
cd c:\Users\rizki\OneDrive\Documents\manajemen_kelas
php spark db:table users
```

**Expected output jika BERHASIL:**

```
CodeIgniter v4.6.1 Command Line Tool - Server Time: ...

+-----------+----------+----------+----------+----------+------+
| hostname  | database | username | DBDriver | DBPrefix | port |
+-----------+----------+----------+----------+----------+------+
| contai... | railway  | postgres | Postgre  |          | 6543 |
+-----------+----------+----------+----------+----------+------+

Error: Table "users" not found (NORMAL - karena belum migrasi)
```

**Expected output jika GAGAL:**

```
Unable to connect to the database.
```

Jika GAGAL → cek lagi credentials di `.env` apakah sudah benar.

---

## 📌 TIPS:

1. **Jangan pakai tanda kutip** di nilai `.env` kecuali untuk `app.baseURL`
2. **Pastikan tidak ada spasi** sebelum/sesudah `=`
3. **Password bisa sangat panjang** (20-30 karakter) - itu normal
4. **Kalau copy paste**, pastikan tidak ada enter/newline di tengah nilai

---

**Setelah `.env` sudah diisi dengan benar, kasih tahu saya dan kita lanjut ke migrasi data!** 🚀
