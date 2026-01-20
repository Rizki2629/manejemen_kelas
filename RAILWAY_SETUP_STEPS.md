# 🚂 Setup Railway PostgreSQL - SDN GU09

## ✅ Langkah 1: Dapatkan Credentials dari Railway

Setelah membuat database di Railway, catat credentials berikut:

```
PGHOST=containers-us-west-xxx.railway.app
PGPORT=6543
PGDATABASE=railway
PGUSER=postgres
PGPASSWORD=xxxxxxxxxxxxxxxxxxxxxx
```

## ✅ Langkah 2: Update File `.env`

Tambahkan di file `.env` Anda:

```properties
# Railway PostgreSQL Configuration
database.default.hostname = containers-us-west-xxx.railway.app
database.default.port     = 6543
database.default.database = railway
database.default.username = postgres
database.default.password = xxxxxxxxxxxxxxxxxxxxxx
database.default.DBDriver = Postgre
database.default.DBPrefix =

# SSL Configuration (Railway requires SSL)
database.default.encrypt.ssl_verify = false
```

**GANTI dengan credentials Anda yang didapat dari Railway!**

## ✅ Langkah 3: Test Koneksi

Jalankan command ini untuk test koneksi:

```powershell
php spark db:table migrations
```

Jika berhasil, lanjut ke migrasi data.

## ✅ Langkah 4: Jalankan Script Migrasi

Script migrasi akan:

- ✅ Baca data dari `sdngu09.sql`
- ✅ Convert MySQL syntax ke PostgreSQL
- ✅ Import semua 28 tabel
- ✅ Validasi jumlah data

```powershell
php app/Commands/MigrateToPostgreSQL.php
```

---

**Status:** Menunggu credentials Railway dari Anda
