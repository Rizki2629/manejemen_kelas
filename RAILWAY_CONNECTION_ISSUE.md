# ⚠️ CONNECTION TIMEOUT KE RAILWAY POSTGRESQL

## 🔍 Masalah:

```
pg_pconnect(): Unable to connect to PostgreSQL server: Connection timed out
```

## 🛠️ Solusi:

### 1. Cek Railway Database Public Access

Di Railway Dashboard:

1. Klik database PostgreSQL Anda
2. Klik tab **"Settings"**
3. Scroll ke **"Networking"**
4. Pastikan **"Enable Public Networking"** atau **"TCP Proxy"** **AKTIF** ✅
5. Jika tidak aktif, **enable** dan tunggu 1-2 menit

### 2. Gunakan Internal URL (jika ada)

Railway biasanya punya 2 connection URLs:

- **Private URL** (hanya dari Railway services)
- **Public URL** (dari internet/lokal)

Cek di tab "Connect", cari:

- **Public** atau **External** connection string
- Biasanya format: `autorack.proxy.rlwy.net` atau `containers-us-west-xxx.railway.app`

### 3. Test Koneksi dengan psql (jika ada)

```powershell
psql -h autorack.proxy.rlwy.net -p 5432 -U postgres -d railway
```

Jika timeout juga, berarti ada block di network.

### 4. Cek Firewall/Antivirus

- **Windows Firewall**: Allow outbound port 5432
- **Antivirus**: Temporary disable dan test lagi

---

## 🔄 ALTERNATIF: Pakai MySQL yang Sudah Ada di Hosting

Karena koneksi Railway timeout, opsi terbaik adalah:

### Opsi A: Tetap Pakai MySQL di Hosting (Via Tunnel)

Anda sudah punya database MySQL di hosting `sdngu09.my.id` yang **sudah jalan** dan **data lengkap**.

**Keuntungan:**

- ✅ Data sudah ada
- ✅ Tidak perlu migrasi
- ✅ Aplikasi sudah compatible

**Cara:**

1. Setup SSH tunnel atau remote MySQL
2. Update `.env` kembali ke MySQL
3. Aplikasi langsung jalan

### Opsi B: Railway dengan Private Network

Jika Railway Anda part of team/paid plan, bisa pakai internal network.

### Opsi C: Alternatif PostgreSQL Lain

**Supabase** (lebih mudah connect):

1. https://supabase.com/
2. Free tier 500MB
3. Biasanya tidak ada firewall issue
4. Connection pooler support

**Neon** (paling cepat):

1. https://neon.tech/
2. Free unlimited
3. WebSocket support (bypass firewall)

---

## ❓ Rekomendasi Saya:

**Untuk saat ini, TETAP PAKAI MYSQL** yang sudah ada di hosting `sdngu09.my.id`:

### Update `.env` kembali ke MySQL:

```properties
# Database MySQL (via tunnel atau remote)
database.default.hostname = sdngu09.my.id
database.default.port     = 3306
database.default.database = sdngu09
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

Atau kalau pakai SSH tunnel:

```properties
database.default.hostname = 127.0.0.1
database.default.port     = 3307  # port tunnel
database.default.database = sdngu09
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

---

## 🎯 Keputusan Anda:

**Pilihan 1:** Fix Railway (cek public networking + firewall)  
**Pilihan 2:** Kembali pakai MySQL hosting (paling cepat & reliable)  
**Pilihan 3:** Coba PostgreSQL alternative (Supabase/Neon)

**Mana yang Anda pilih?**

---

**Status:** Connection timeout ke Railway PostgreSQL  
**Next Step:** Pilih salah satu solusi di atas
