<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>Absensi Mobile</title>
  <style>
    :root {
      --bg: #0b1220;
      --card: rgba(255,255,255,.06);
      --border: rgba(255,255,255,.12);
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.70);
      --accent: #4f46e5;
      --danger: #ef4444;
      --ok: #22c55e;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
      background: radial-gradient(1200px 800px at 20% -20%, rgba(79,70,229,.45), transparent 60%),
                  radial-gradient(900px 600px at 100% 0%, rgba(34,197,94,.25), transparent 60%),
                  var(--bg);
      color: var(--text);
      min-height: 100vh;
      padding: 16px;
    }
    .wrap { max-width: 520px; margin: 0 auto; }
    .header {
      display:flex; align-items:center; justify-content:space-between;
      gap: 12px; margin-bottom: 14px;
    }
    .title { font-weight: 800; letter-spacing: .2px; font-size: 20px; }
    .subtitle { color: var(--muted); font-size: 12px; }

    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 14px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 10px 30px rgba(0,0,0,.25);
      margin-bottom: 12px;
    }

    label { display:block; font-size: 12px; color: var(--muted); margin-bottom: 6px; }
    input {
      width: 100%;
      height: 44px;
      padding: 0 12px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.16);
      background: rgba(0,0,0,.18);
      color: var(--text);
      font-size: 15px;
      outline: none;
    }
    input:focus { border-color: rgba(79,70,229,.75); box-shadow: 0 0 0 3px rgba(79,70,229,.18); }

    .row { display:flex; gap: 10px; }
    .row > * { flex: 1; }

    button {
      height: 44px;
      border: 0;
      border-radius: 12px;
      padding: 0 12px;
      font-weight: 700;
      color: white;
      background: linear-gradient(135deg, rgba(79,70,229,1), rgba(59,130,246,1));
      cursor: pointer;
      width: 100%;
    }
    button.secondary {
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.16);
      color: var(--text);
    }
    button.danger {
      background: rgba(239,68,68,.92);
    }
    button:disabled { opacity: .55; cursor: not-allowed; }

    .pill {
      display:inline-flex; align-items:center; gap: 8px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.12);
      padding: 8px 10px;
      border-radius: 999px;
      font-size: 12px;
      color: var(--muted);
    }

    .student {
      display:flex; justify-content:space-between; align-items:center; gap: 10px;
    }
    .student .name { font-weight: 800; font-size: 16px; }
    .student .kelas { color: var(--muted); font-size: 13px; }

    .status { font-size: 12px; margin-top: 10px; color: var(--muted); }
    .status.ok { color: rgba(34,197,94,.95); }
    .status.err { color: rgba(239,68,68,.95); }

    .videoWrap {
      margin-top: 10px;
      overflow: hidden;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(0,0,0,.28);
      position: relative;
      aspect-ratio: 3 / 4;
    }
    video, canvas, img {
      width: 100%; height: 100%; object-fit: cover;
      display:block;
    }
    .inFrameBtn {
      position: absolute;
      top: 10px;
      right: 10px;
      height: 36px;
      width: 44px;
      padding: 0;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      background: rgba(0,0,0,.35);
      border: 1px solid rgba(255,255,255,.18);
      color: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      z-index: 4;
    }
    .inFrameBtn:disabled { opacity: .55; }
    .inFrameBtn svg { width: 18px; height: 18px; }

    .liveOverlay {
      position: absolute;
      right: 10px;
      bottom: 10px;
      max-width: calc(100% - 20px);
      background: transparent;
      border: 0;
      border-radius: 0;
      padding: 0;
      color: rgba(255,255,255,0.95);
      font-weight: 800;
      font-size: 16px;
      line-height: 1.18;
      text-shadow: none;
      pointer-events: none;
      backdrop-filter: none;
      -webkit-backdrop-filter: none;
      display: none;
      white-space: pre-line;
      text-align: right;
      z-index: 3;
    }

    .help { font-size: 12px; color: var(--muted); margin-top: 8px; line-height: 1.35; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div>
        <div class="title">Absensi Mobile</div>
        <div class="subtitle">Buka kamera, ambil foto, otomatis catat waktu & lokasi</div>
      </div>
      <div class="pill" id="caps">Menunggu input NISN</div>
    </div>

    <div class="card">
      <label for="nisn">Masukkan NISN</label>
      <div class="row" style="align-items:center;">
        <input id="nisn" inputmode="numeric" autocomplete="off" placeholder="Contoh: 1234567890" />
        <button id="btnCari" style="max-width: 140px;">Cari</button>
      </div>
      <div class="status" id="lookupStatus"></div>
    </div>

    <div class="card" id="studentCard" style="display:none;">
      <div class="student">
        <div>
          <div class="name" id="studentName">-</div>
          <div class="kelas" id="studentKelas">-</div>
        </div>
        <button class="secondary" id="btnGanti" style="max-width: 160px;">Ganti NISN</button>
      </div>

      <div class="videoWrap" id="cameraWrap" style="display:none;">
        <video id="video" playsinline muted></video>
        <button id="btnSwitch" class="inFrameBtn" disabled aria-label="Ganti kamera" title="Ganti kamera"></button>
        <div id="liveOverlay" class="liveOverlay"></div>
      </div>

      <div class="videoWrap" id="previewWrap" style="display:none;">
        <img id="previewImg" alt="Preview" />
      </div>

      <div class="row" style="margin-top: 10px;">
        <button id="btnKamera">Aktifkan Kamera</button>
      </div>
      <div class="row" style="margin-top: 10px;">
        <button id="btnFoto" class="secondary" disabled>Ambil Foto</button>
        <button id="btnKirim" disabled>Kirim Absensi</button>
      </div>
      <div class="row" style="margin-top: 10px;">
        <button id="btnUlang" class="danger" disabled>Ulangi Foto</button>
      </div>

      <div class="status" id="submitStatus"></div>
      <div class="help">
        Catatan: Kamera & lokasi butuh izin. Jika lokasi tidak diizinkan, foto tetap bisa dikirim (tanpa alamat).
      </div>
    </div>

    <canvas id="canvas" style="display:none;"></canvas>
  </div>

<script>
(() => {
  const BASE = '<?= rtrim(base_url(), '/') ?>';
  const els = {
    caps: document.getElementById('caps'),
    nisn: document.getElementById('nisn'),
    btnCari: document.getElementById('btnCari'),
    lookupStatus: document.getElementById('lookupStatus'),

    studentCard: document.getElementById('studentCard'),
    studentName: document.getElementById('studentName'),
    studentKelas: document.getElementById('studentKelas'),
    btnGanti: document.getElementById('btnGanti'),

    cameraWrap: document.getElementById('cameraWrap'),
    previewWrap: document.getElementById('previewWrap'),
    video: document.getElementById('video'),
    previewImg: document.getElementById('previewImg'),
    canvas: document.getElementById('canvas'),

    btnKamera: document.getElementById('btnKamera'),
    btnSwitch: document.getElementById('btnSwitch'),
    liveOverlay: document.getElementById('liveOverlay'),
    btnFoto: document.getElementById('btnFoto'),
    btnKirim: document.getElementById('btnKirim'),
    btnUlang: document.getElementById('btnUlang'),

    submitStatus: document.getElementById('submitStatus'),
  };

  const state = {
    student: null,
    stream: null,
    facingMode: 'user',
    capturedBlob: null,
    capturedMeta: null,
    overlayTimer: null,
    overlayLoc: {
      ok: false,
      fetching: false,
      lines: [],
      addressFull: '',
      road: '',
      kelurahan: '',
      kecamatan: '',
      kota: '',
      provinsi: '',
      latitude: null,
      longitude: null,
      accuracy_m: null,
    },
  };

  const SWITCH_CAMERA_SVG = `
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
      <path d="M7 7h10a2 2 0 0 1 2 2v2" stroke="rgba(255,255,255,.92)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M17 17H7a2 2 0 0 1-2-2v-2" stroke="rgba(255,255,255,.92)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M19 7l2 2-2 2" stroke="rgba(255,255,255,.92)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M5 17l-2-2 2-2" stroke="rgba(255,255,255,.92)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `;

  function updateSwitchCameraButtonLabel() {
    els.btnSwitch.innerHTML = SWITCH_CAMERA_SVG;
    els.btnSwitch.title = state.facingMode === 'user' ? 'Ganti ke kamera belakang' : 'Ganti ke kamera depan';
    els.btnSwitch.setAttribute('aria-label', els.btnSwitch.title);
  }

  function stripPulauJawa(value) {
    let s = String(value ?? '').trim();
    if (!s) return '';

    // Hapus "Jawa" jika berdiri sendiri (jangan hapus "Jawa Barat/Tengah/Timur")
    s = s.replace(/\bJawa\b(?!\s*(Barat|Tengah|Timur))/gi, '');
    s = s.replace(/\s{2,}/g, ' ');
    s = s.replace(/,\s*,/g, ', ');
    s = s.replace(/^,\s*/g, '');
    s = s.replace(/\s*,\s*$/g, '');
    return s.trim();
  }

  function buildOverlayLines() {
    if (!state.student) return [];

    const tstamp = fmtNow();
    const norm = (v) => String(v || '').trim();
    const uniqParts = (arr) => {
      const out = [];
      const seen = new Set();
      for (const raw of arr) {
        const v = norm(raw);
        if (!v) continue;
        const k = v.toLowerCase();
        if (seen.has(k)) continue;
        seen.add(k);
        out.push(v);
      }
      return out;
    };

    const kelKec = uniqParts([state.overlayLoc.kelurahan, state.overlayLoc.kecamatan]).join(', ');
    const kotaProv = uniqParts([state.overlayLoc.kota, state.overlayLoc.provinsi]).join(', ');

    const rawLine0 = norm(state.overlayLoc.lines[0]);
    const rawLine1 = norm(state.overlayLoc.lines[1]);
    const rawLine2 = norm(state.overlayLoc.lines[2]);
    const roadLine = norm(state.overlayLoc.road) || (rawLine0 && rawLine0.toLowerCase() !== kelKec.toLowerCase() ? rawLine0 : '');
    const fallbackLine2 = rawLine1;
    const fallbackLine3 = rawLine2;

    const base = [
      `${state.student.nama} - ${state.student.kelas || ''}`.trim(),
      tstamp,
    ];

    const locLines = [
      roadLine,
      norm(kelKec || fallbackLine2),
      norm(kotaProv || fallbackLine3),
    ].filter(Boolean);

    // Saat masih fetch, tampilkan alamat yang sudah ada (jika ada).
    // Jangan paksa menampilkan "Mengambil lokasi..." kalau preview sudah punya lokasi.
    if (state.overlayLoc.fetching && locLines.length === 0) {
      return [...base, 'Mengambil lokasi...'];
    }

    return [...base, ...locLines].filter(Boolean);
  }

  function renderLiveOverlay() {
    const lines = buildOverlayLines();
    if (!lines.length) {
      els.liveOverlay.style.display = 'none';
      els.liveOverlay.textContent = '';
      return;
    }
    els.liveOverlay.textContent = lines.join('\n');
    // Use explicit display so it overrides CSS `.liveOverlay { display: none; }`
    els.liveOverlay.style.display = 'block';
  }

  function startOverlayTimer() {
    stopOverlayTimer();
    renderLiveOverlay();
    state.overlayTimer = setInterval(renderLiveOverlay, 1000);
  }

  function stopOverlayTimer() {
    if (state.overlayTimer) {
      clearInterval(state.overlayTimer);
      state.overlayTimer = null;
    }
  }

  async function preloadLocationForOverlay() {
    if (state.overlayLoc.fetching) return;
    state.overlayLoc.fetching = true;
    renderLiveOverlay();

    const loc = await getLocation();
    if (!loc.ok) {
      state.overlayLoc.fetching = false;
      state.overlayLoc.ok = false;
      renderLiveOverlay();
      return;
    }

    const geo = await reverseGeocode(loc.lat, loc.lon);

    const sanitizedLines = (geo.lines || []).map(stripPulauJawa).filter(Boolean);

    state.overlayLoc = {
      ok: true,
      fetching: false,
      lines: sanitizedLines,
      addressFull: sanitizedLines.join(' | '),
      road: stripPulauJawa(geo.road || ''),
      kelurahan: stripPulauJawa(geo.kelurahan || ''),
      kecamatan: stripPulauJawa(geo.kecamatan || ''),
      kota: stripPulauJawa(geo.kota || ''),
      provinsi: stripPulauJawa(geo.provinsi || ''),
      latitude: loc.lat,
      longitude: loc.lon,
      accuracy_m: loc.accuracy,
    };
    renderLiveOverlay();
  }

  function setLookupStatus(msg, type='') {
    els.lookupStatus.textContent = msg || '';
    els.lookupStatus.className = 'status' + (type ? ' ' + type : '');
  }

  function setSubmitStatus(msg, type='') {
    els.submitStatus.textContent = msg || '';
    els.submitStatus.className = 'status' + (type ? ' ' + type : '');
  }

  function fmtNow() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2,'0');
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    return `${days[d.getDay()]}, ${pad(d.getDate())} ${months[d.getMonth()]} ${d.getFullYear()} ${pad(d.getHours())}.${pad(d.getMinutes())}.${pad(d.getSeconds())}`;
  }

  async function lookupNisn() {
    const nisn = (els.nisn.value || '').trim();
    if (!nisn) return;

    setLookupStatus('Mencari data siswa...', '');
    els.btnCari.disabled = true;

    try {
      const res = await fetch(`${BASE}/absensi-mobile/lookup?nisn=${encodeURIComponent(nisn)}`, { headers: { 'Accept': 'application/json' } });
      const json = await res.json().catch(() => ({}));

      if (!res.ok || !json.ok) {
        setLookupStatus(json.message || 'NISN tidak ditemukan.', 'err');
        state.student = null;
        els.studentCard.style.display = 'none';
        els.caps.textContent = 'NISN tidak ditemukan';
        return;
      }

      state.student = json.student;
      els.studentName.textContent = state.student.nama;
      els.studentKelas.textContent = state.student.kelas ? state.student.kelas : '-';
      els.studentCard.style.display = '';
      els.caps.textContent = 'Data siswa ditemukan';
      setLookupStatus('Data ditemukan. Silakan aktifkan kamera.', 'ok');

      resetCapture();
      // Pre-render overlay (time starts updating when camera is active)
      renderLiveOverlay();
    } catch (e) {
      setLookupStatus('Gagal mengambil data. Coba lagi.', 'err');
    } finally {
      els.btnCari.disabled = false;
    }
  }

  function stopStream() {
    if (state.stream) {
      state.stream.getTracks().forEach(t => t.stop());
      state.stream = null;
    }
  }

  async function startCamera() {
    setSubmitStatus('', '');

    if (!navigator.mediaDevices?.getUserMedia) {
      setSubmitStatus('Browser tidak mendukung kamera.', 'err');
      return;
    }

    stopStream();

    const constraints = {
      audio: false,
      video: {
        facingMode: { ideal: state.facingMode },
        width: { ideal: 720 },
        height: { ideal: 960 },
      }
    };

    try {
      els.btnKamera.disabled = true;
      els.btnSwitch.disabled = true;
      els.btnFoto.disabled = true;

      const stream = await navigator.mediaDevices.getUserMedia(constraints);
      state.stream = stream;
      els.video.srcObject = stream;
      await els.video.play();

      els.cameraWrap.style.display = '';
      els.previewWrap.style.display = 'none';
      els.btnFoto.disabled = false;
      els.btnSwitch.disabled = false;
      updateSwitchCameraButtonLabel();

      // Start live overlay (timestamp updates immediately)
      startOverlayTimer();
      preloadLocationForOverlay();

      els.caps.textContent = 'Kamera aktif';
      setSubmitStatus('Kamera aktif. Siapkan wajah terlihat jelas.', '');
    } catch (e) {
      setSubmitStatus('Izin kamera ditolak / kamera tidak tersedia.', 'err');
    } finally {
      els.btnKamera.disabled = false;
    }
  }

  async function switchCamera() {
    state.facingMode = state.facingMode === 'user' ? 'environment' : 'user';
    await startCamera();
  }

  function getLocation() {
    return new Promise((resolve) => {
      if (!navigator.geolocation) return resolve({ ok:false, message:'Geolocation tidak didukung' });

      navigator.geolocation.getCurrentPosition(
        (pos) => resolve({
          ok: true,
          lat: pos.coords.latitude,
          lon: pos.coords.longitude,
          accuracy: pos.coords.accuracy,
        }),
        (err) => resolve({ ok:false, message: err?.message || 'Lokasi tidak tersedia' }),
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
      );
    });
  }

  async function reverseGeocode(lat, lon) {
    try {
      const url = `${BASE}/absensi-mobile/reverse-geocode?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const json = await res.json().catch(() => ({}));
      if (!res.ok || !json.ok) return { lines: [] };
      return {
        road: typeof json.road === 'string' ? json.road : '',
        kelurahan: typeof json.kelurahan === 'string' ? json.kelurahan : '',
        kecamatan: typeof json.kecamatan === 'string' ? json.kecamatan : '',
        kota: typeof json.kota === 'string' ? json.kota : '',
        provinsi: typeof json.provinsi === 'string' ? json.provinsi : '',
        lines: Array.isArray(json.lines) ? json.lines : [],
      };
    } catch (e) {
      return { lines: [] };
    }
  }

  function drawOverlay(ctx, width, height, lines) {
    const margin = Math.round(width * 0.03);
    const baseFontSize = Math.min(20, Math.max(18, Math.round(width * 0.022)));
    const fontSize = Math.round(baseFontSize * 1.4);
    const lineGap = Math.round(fontSize * 0.25);

    ctx.save();
    ctx.font = `700 ${fontSize}px system-ui, -apple-system, Segoe UI, Roboto, Arial`;
    ctx.textBaseline = 'top';
    ctx.textAlign = 'right';

    const maxWidth = Math.round(width * 0.66);

    // Word wrap sederhana
    const wrapped = [];
    for (const line of lines) {
      const words = String(line || '').split(' ');
      let cur = '';
      for (const w of words) {
        const test = cur ? (cur + ' ' + w) : w;
        if (ctx.measureText(test).width <= maxWidth) {
          cur = test;
        } else {
          if (cur) wrapped.push(cur);
          cur = w;
        }
      }
      if (cur) wrapped.push(cur);
    }

    const lineHeight = fontSize + lineGap;
    const totalH = wrapped.length ? (wrapped.length * lineHeight - lineGap) : 0;

    // place at bottom-right (plain text, no background)
    const x = width - margin;
    let yText = height - margin - totalH;

    ctx.fillStyle = 'rgba(255,255,255,0.95)';
    ctx.shadowColor = 'transparent';
    ctx.shadowBlur = 0;
    ctx.shadowOffsetY = 0;
    for (const line of wrapped) {
      ctx.fillText(line, x, yText);
      yText += lineHeight;
    }

    ctx.restore();
  }

  function roundRect(ctx, x, y, w, h, r) {
    const rr = Math.min(r, w/2, h/2);
    ctx.beginPath();
    ctx.moveTo(x + rr, y);
    ctx.arcTo(x + w, y, x + w, y + h, rr);
    ctx.arcTo(x + w, y + h, x, y + h, rr);
    ctx.arcTo(x, y + h, x, y, rr);
    ctx.arcTo(x, y, x + w, y, rr);
    ctx.closePath();
  }

  async function capture() {
    setSubmitStatus('', '');

    if (!state.student) {
      setSubmitStatus('Silakan cari NISN dulu.', 'err');
      return;
    }

    if (!state.stream) {
      setSubmitStatus('Aktifkan kamera dulu.', 'err');
      return;
    }

    els.btnFoto.disabled = true;
    els.btnKirim.disabled = true;
    els.btnUlang.disabled = true;

    const deviceTakenAtIso = new Date().toISOString();
    const overlayLines = buildOverlayLines().map(stripPulauJawa).filter(Boolean);
    const loc = state.overlayLoc.ok
      ? {
          ok: true,
          lat: state.overlayLoc.latitude,
          lon: state.overlayLoc.longitude,
          accuracy: state.overlayLoc.accuracy_m,
        }
      : { ok: false };
    const addressFull = state.overlayLoc.ok ? stripPulauJawa(state.overlayLoc.addressFull || '') : '';

    // Capture video frame
    const video = els.video;
    const canvas = els.canvas;
    const w = video.videoWidth || 720;
    const h = video.videoHeight || 960;
    canvas.width = w;
    canvas.height = h;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, w, h);
    drawOverlay(ctx, w, h, overlayLines);

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.92));
    if (!blob) {
      setSubmitStatus('Gagal memproses foto.', 'err');
      els.btnFoto.disabled = false;
      return;
    }

    state.capturedBlob = blob;
    state.capturedMeta = {
      nisn: state.student.nisn,
      device_taken_at: deviceTakenAtIso,
      latitude: loc.ok ? loc.lat : null,
      longitude: loc.ok ? loc.lon : null,
      accuracy_m: loc.ok ? loc.accuracy : null,
      address: addressFull,
      overlay_lines: overlayLines,
    };

    const url = URL.createObjectURL(blob);
    els.previewImg.src = url;
    els.previewWrap.style.display = '';
    els.cameraWrap.style.display = 'none';

    els.btnKirim.disabled = false;
    els.btnUlang.disabled = false;

    setSubmitStatus('Foto siap dikirim. Klik "Kirim Absensi".', 'ok');
  }

  function resetCapture() {
    state.capturedBlob = null;
    state.capturedMeta = null;
    stopOverlayTimer();
    els.liveOverlay.style.display = 'none';
    els.liveOverlay.textContent = '';
    state.overlayLoc = {
      ok: false,
      fetching: false,
      lines: [],
      addressFull: '',
      road: '',
      kelurahan: '',
      kecamatan: '',
      kota: '',
      provinsi: '',
      latitude: null,
      longitude: null,
      accuracy_m: null,
    };
    els.previewWrap.style.display = 'none';
    els.cameraWrap.style.display = 'none';
    els.btnFoto.disabled = true;
    els.btnKirim.disabled = true;
    els.btnUlang.disabled = true;
    setSubmitStatus('', '');
    stopStream();
  }

  async function submit() {
    if (!state.student || !state.capturedBlob || !state.capturedMeta) {
      setSubmitStatus('Ambil foto dulu.', 'err');
      return;
    }

    els.btnKirim.disabled = true;
    setSubmitStatus('Mengirim absensi...', '');

    try {
      const fd = new FormData();
      fd.append('nisn', state.student.nisn);
      fd.append('device_taken_at', state.capturedMeta.device_taken_at || '');
      if (state.capturedMeta.latitude != null) fd.append('latitude', String(state.capturedMeta.latitude));
      if (state.capturedMeta.longitude != null) fd.append('longitude', String(state.capturedMeta.longitude));
      if (state.capturedMeta.accuracy_m != null) fd.append('accuracy_m', String(state.capturedMeta.accuracy_m));
      if (state.capturedMeta.address) fd.append('address', state.capturedMeta.address);

      fd.append('photo', new File([state.capturedBlob], `${state.student.nisn}.jpg`, { type: 'image/jpeg' }));

      const res = await fetch(`${BASE}/absensi-mobile/submit`, {
        method: 'POST',
        body: fd,
        headers: { 'Accept': 'application/json' },
      });

      const json = await res.json().catch(() => ({}));
      if (!res.ok || !json.ok) {
        setSubmitStatus(json.message || 'Gagal menyimpan absensi.', 'err');
        els.btnKirim.disabled = false;
        return;
      }

      setSubmitStatus('Berhasil! Absensi tersimpan. Terima kasih.', 'ok');
      els.caps.textContent = 'Absensi tersimpan';

      // Stop camera, lock sending
      stopStream();
      els.btnFoto.disabled = true;
      els.btnSwitch.disabled = true;
      els.btnKamera.disabled = true;
      els.btnUlang.disabled = true;

      // Auto reset after a short delay
      setTimeout(() => {
        els.btnKamera.disabled = false;
        els.btnSwitch.disabled = false;
        els.btnFoto.disabled = true;
        els.btnKirim.disabled = true;
        els.btnUlang.disabled = true;
        els.previewWrap.style.display = 'none';
        els.cameraWrap.style.display = 'none';
        setSubmitStatus('', '');
      }, 2500);
    } catch (e) {
      setSubmitStatus('Gagal mengirim. Cek koneksi internet.', 'err');
      els.btnKirim.disabled = false;
    }
  }

  // Events
  els.btnCari.addEventListener('click', (e) => { e.preventDefault(); lookupNisn(); });
  els.nisn.addEventListener('keydown', (e) => { if (e.key === 'Enter') lookupNisn(); });

  els.btnGanti.addEventListener('click', (e) => {
    e.preventDefault();
    resetCapture();
    state.student = null;
    els.studentCard.style.display = 'none';
    els.nisn.value = '';
    els.nisn.focus();
    setLookupStatus('', '');
    els.caps.textContent = 'Menunggu input NISN';
  });

  els.btnKamera.addEventListener('click', (e) => { e.preventDefault(); startCamera(); });
  els.btnSwitch.addEventListener('click', (e) => { e.preventDefault(); switchCamera(); });
  els.btnFoto.addEventListener('click', (e) => { e.preventDefault(); capture().finally(() => { els.btnFoto.disabled = false; }); });
  els.btnUlang.addEventListener('click', (e) => { e.preventDefault(); startCamera(); });
  els.btnKirim.addEventListener('click', (e) => { e.preventDefault(); submit(); });

})();
</script>
</body>
</html>
