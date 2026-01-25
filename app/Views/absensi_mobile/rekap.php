<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rekap Absen Mobile</title>
  <style>
    :root {
      --bg: #0b1220;
      --card: rgba(255,255,255,.06);
      --border: rgba(255,255,255,.12);
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.70);
      --accent: #4f46e5;
      --danger: #ef4444;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
      background: var(--bg);
      color: var(--text);
      padding: 16px;
    }
    .wrap { max-width: 980px; margin: 0 auto; }
    .head {
      display:flex; align-items:center; justify-content:space-between;
      gap: 12px; margin-bottom: 14px;
    }
    .title { font-weight: 900; font-size: 20px; }
    .sub { color: var(--muted); font-size: 12px; }
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 12px;
      margin-bottom: 12px;
    }
    input {
      height: 40px;
      padding: 0 10px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.16);
      background: rgba(0,0,0,.18);
      color: var(--text);
      outline: none;
      width: 180px;
    }
    button {
      height: 40px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.16);
      background: rgba(255,255,255,.10);
      color: var(--text);
      font-weight: 800;
      padding: 0 12px;
      cursor: pointer;
    }
    button.primary {
      border: 0;
      background: linear-gradient(135deg, rgba(79,70,229,1), rgba(59,130,246,1));
      color: #fff;
    }
    button.danger {
      border: 0;
      background: rgba(239,68,68,.92);
      color: #fff;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: rgba(0,0,0,.12);
    }
    th, td {
      text-align: left;
      padding: 10px;
      border-bottom: 1px solid rgba(255,255,255,.08);
      vertical-align: top;
      font-size: 13px;
    }
    th { color: rgba(255,255,255,.85); font-weight: 900; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
    .groupTitle { font-weight: 900; font-size: 16px; margin: 12px 2px 10px; }
    .muted { color: var(--muted); }
    .actions { display:flex; gap: 8px; flex-wrap: wrap; }
    a.btnLink {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      height: 40px;
      padding: 0 12px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.16);
      background: rgba(255,255,255,.10);
      color: var(--text);
      text-decoration: none;
      font-weight: 800;
      font-size: 13px;
    }
    .status { margin-top: 10px; font-size: 12px; color: var(--muted); }
    .status.err { color: rgba(239,68,68,.95); }
    .status.ok { color: rgba(34,197,94,.95); }
    @media (max-width: 720px) {
      .head { flex-direction: column; align-items: flex-start; }
      input { width: 100%; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <?php
      $formatTime = static function ($value) {
        $s = trim((string) ($value ?? ''));
        if ($s === '') return '-';
        try {
          $dt = new DateTime($s);
          $dt->setTimezone(new DateTimeZone(app_timezone()));
          return $dt->format('Y-m-d H:i:s');
        } catch (Throwable $e) {
          return $s;
        }
      };
    ?>

    <div class="head">
      <div>
        <div class="title">Rekap Absen Mobile</div>
        <div class="sub">Total tampil: <?= (int)($total ?? 0) ?> (maks 500). Filter opsional per tanggal.</div>
      </div>
      <div class="card" style="display:flex; gap: 8px; align-items:center; margin:0;">
        <form method="get" action="<?= esc(base_url('rekap-absen-mobile')) ?>" style="display:flex; gap:8px; align-items:center; margin:0; width:100%;">
          <input type="date" name="date" value="<?= esc($date ?? '') ?>" />
          <button class="primary" type="submit">Terapkan</button>
          <a class="btnLink" href="<?= esc(base_url('rekap-absen-mobile')) ?>">Reset</a>
        </form>
      </div>
    </div>

    <div class="status" id="status"></div>

    <?php if (empty($grouped) || !is_array($grouped)) : ?>
      <div class="card">Belum ada data absensi mobile.</div>
    <?php else : ?>
      <?php foreach ($grouped as $tanggal => $rows) : ?>
        <div class="card">
          <div class="groupTitle">Tanggal: <?= esc($tanggal) ?> <span class="muted">(<?= is_array($rows) ? count($rows) : 0 ?> data)</span></div>

          <table role="table" aria-label="Rekap absen mobile">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Alamat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (($rows ?? []) as $r) : ?>
                <tr id="row-<?= (int)$r['id'] ?>">
                  <td>
                    <div><?= esc($formatTime($r['device_taken_at'] ?: ($r['created_at'] ?? '-'))) ?></div>
                    <?php if (!empty($r['latitude']) && !empty($r['longitude'])) : ?>
                      <div class="muted"><?= esc($r['latitude']) ?>, <?= esc($r['longitude']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td><?= esc($r['nisn'] ?? '-') ?></td>
                  <td><?= esc($r['nama'] ?? '-') ?></td>
                  <td><?= esc($r['kelas'] ?? '-') ?></td>
                  <td style="max-width: 360px;">
                    <div><?= esc($r['address'] ?? '-') ?></div>
                  </td>
                  <td>
                    <div class="actions">
                      <a class="btnLink" target="_blank" rel="noopener" href="<?= esc(base_url('rekap-absen-mobile/photo/' . (int)$r['id'])) ?>">Lihat Foto</a>
                      <button class="danger" type="button" onclick="deleteRow(<?= (int)$r['id'] ?>)">Hapus</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script>
    const BASE = '<?= rtrim(base_url(), '/') ?>';
    const statusEl = document.getElementById('status');

    function setStatus(msg, type='') {
      statusEl.textContent = msg || '';
      statusEl.className = 'status' + (type ? ' ' + type : '');
    }

    async function deleteRow(id) {
      if (!confirm('Hapus data ini? Foto juga akan dihapus dari server.')) return;

      setStatus('Menghapus...', '');
      try {
        const res = await fetch(`${BASE}/rekap-absen-mobile/delete/${encodeURIComponent(id)}`, {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
        });
        const json = await res.json().catch(() => ({}));
        if (!res.ok || !json.ok) {
          setStatus(json.message || 'Gagal menghapus data.', 'err');
          return;
        }

        const row = document.getElementById(`row-${id}`);
        if (row) row.remove();
        setStatus('Data berhasil dihapus.', 'ok');
      } catch (e) {
        setStatus('Gagal menghapus (cek koneksi/server).', 'err');
      }
    }
  </script>
</body>
</html>
