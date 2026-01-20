# 🔧 Enable PostgreSQL Extension di PHP (XAMPP)

## ⚠️ Error yang Muncul:

```
The required PHP extension "pgsql" is not loaded.
```

## ✅ Solusi: Enable Extension PostgreSQL

### Step 1: Buka File php.ini

Lokasi: `C:\xampp\php\php.ini`

**Cara buka:**

- Option A: Buka XAMPP Control Panel → Apache → Config → PHP (php.ini)
- Option B: Buka file manual di `C:\xampp\php\php.ini` dengan Notepad

### Step 2: Cari dan Uncomment Extension

Di dalam file `php.ini`, cari baris ini (tekan Ctrl+F, cari "pgsql"):

```ini
;extension=pgsql
;extension=pdo_pgsql
```

**Hapus tanda titik koma (;)** di depan kedua baris tersebut:

```ini
extension=pgsql
extension=pdo_pgsql
```

### Step 3: Simpan File

- Simpan file `php.ini` (Ctrl + S)
- **Tutup semua aplikasi** yang pakai PHP

### Step 4: Restart Apache (jika pakai XAMPP web)

Di XAMPP Control Panel:

- Stop Apache
- Start Apache lagi

### Step 5: Verify Extension Loaded

Buka terminal PowerShell baru (tutup yang lama):

```powershell
php -m | findstr pgsql
```

**Expected output:**

```
pdo_pgsql
pgsql
```

---

## 🚨 Jika Extension TIDAK Ada di php.ini

Jika Anda search dan tidak ketemu `extension=pgsql`, berarti ekstensi tidak ter-install.

### Download Extension PostgreSQL:

1. **Cek versi PHP Anda:**

   ```powershell
   php -v
   ```

   Contoh output: `PHP 8.2.4 (cli) (built: Mar 14 2023 17:54:25) (ZTS Visual C++ 2019 x64)`

2. **Download dari:**
   - https://windows.php.net/downloads/pecl/releases/pgsql/
   - Pilih versi yang sesuai dengan PHP Anda (8.2, x64, TS/NTS)

3. **Extract file:**
   - Extract `php_pgsql.dll` dan `php_pdo_pgsql.dll`
   - Copy ke folder `C:\xampp\php\ext\`

4. **Tambahkan di php.ini:**

   ```ini
   extension=pgsql
   extension=pdo_pgsql
   ```

5. **Restart PHP**

---

## 📝 QUICK FIX (Copy Paste):

Buka `C:\xampp\php\php.ini`, cari section extension dan pastikan ini ada:

```ini
extension=pgsql
extension=pdo_pgsql
```

Save → Tutup terminal lama → Buka terminal baru → Test lagi:

```powershell
php spark db:table migrations
```

---

**Status:** Menunggu Anda enable extension PostgreSQL
**Next Step:** Setelah extension aktif, kita lanjut migrasi data!
