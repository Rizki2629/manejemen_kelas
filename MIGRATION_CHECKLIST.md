# ✅ CHECKLIST MIGRASI RAILWAY POSTGRESQL

## 📋 Langkah 1: Setup Railway (5 menit)

- [ ] Buka https://railway.app/
- [ ] Login with GitHub
- [ ] Klik "New Project"
- [ ] Pilih "Provision PostgreSQL"
- [ ] Tunggu database dibuat (30 detik)

## 📋 Langkah 2: Dapatkan Credentials

Di Railway dashboard PostgreSQL:

- [ ] Klik tab "Variables" atau "Connect"
- [ ] Catat credentials:
  ```
  PGHOST: ____________________
  PGPORT: ____________________
  PGDATABASE: ________________
  PGUSER: ____________________
  PGPASSWORD: ________________
  ```

## 📋 Langkah 3: Update `.env`

- [ ] Buka file `.env`
- [ ] Ganti bagian database dengan:
  ```properties
  database.default.hostname = [PGHOST dari Railway]
  database.default.port     = [PGPORT dari Railway]
  database.default.database = [PGDATABASE dari Railway]
  database.default.username = [PGUSER dari Railway]
  database.default.password = [PGPASSWORD dari Railway]
  database.default.DBDriver = Postgre
  ```
- [ ] Simpan file `.env`

## 📋 Langkah 4: Test Koneksi

Di terminal PowerShell:

```powershell
cd c:\Users\rizki\OneDrive\Documents\manajemen_kelas
php spark db:table migrations
```

Expected output: Tampil struktur tabel migrations atau "table not found" (normal, karena belum migrasi)

- [ ] Koneksi berhasil

## 📋 Langkah 5: Jalankan Migrasi

**PENTING:** Pastikan MySQL lokal (XAMPP) sudah jalan!

```powershell
php spark migrate:postgresql
```

Script akan:

- ✅ Validasi file sdngu09.sql
- ✅ Test koneksi PostgreSQL Railway
- ✅ Buat temporary MySQL database
- ✅ Import sdngu09.sql ke MySQL temp
- ✅ Migrasi 28 tabel ke PostgreSQL
- ✅ Validasi jumlah data
- ✅ Cleanup

Estimasi waktu: 5-10 menit

- [ ] Migrasi selesai tanpa error

## 📋 Langkah 6: Test Aplikasi

```powershell
php spark serve
```

Buka browser: http://localhost:8080

- [ ] Login berhasil
- [ ] Dashboard muncul tanpa error
- [ ] Data siswa/guru tampil

## 📋 Langkah 7: Verifikasi di Railway Dashboard

Di Railway PostgreSQL:

- [ ] Klik tab "Data" atau "Query"
- [ ] Jalankan query: `SELECT COUNT(*) FROM users;`
- [ ] Data ada dan sesuai

---

## ❌ Troubleshooting

### Error: "Unable to connect to the database"

- ✅ Cek credentials di `.env` sudah benar
- ✅ Cek Railway database status: "Active"
- ✅ Cek koneksi internet

### Error: "MySQL lokal tidak tersedia"

- ✅ Start XAMPP MySQL
- ✅ Test: `mysql -u root` di terminal

### Error: Migrasi timeout

- ✅ Pisahkan migrasi per tabel (manual via pgAdmin)
- ✅ Gunakan script import SQL langsung

---

## 🎯 Setelah Selesai

✅ **Database MySQL lokal**: Bisa dihapus (sudah ada backup di Railway)
✅ **File sdngu09.sql**: Simpan sebagai backup
✅ **Railway PostgreSQL**: Monitor usage di dashboard
✅ **Aplikasi**: Update semua query jika ada yang error

---

**Status Checklist:**

- [ ] Semua langkah selesai
- [ ] Aplikasi berjalan dengan PostgreSQL Railway
- [ ] Data lengkap dan akurat

**Dibuat:** 19 Januari 2026  
**Last Updated:** ****\_\_\_****
