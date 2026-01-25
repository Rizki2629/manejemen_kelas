<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-4 flex items-center"><i class="fas fa-print text-purple-600 mr-3"></i> Cetak Nilai</h1>

    <!-- Step 1 & 2: Select Kelas & Mapel -->
    <form method="GET" class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
            <select name="kelas" id="kelas" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($availableClasses as $c): $k=$c['kelas']; ?>
                    <option value="<?= esc($k) ?>" <?= ($selectedKelas===$k)?'selected':'' ?>><?= esc($k) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
            <select name="mapel" id="mapel" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">-- Pilih Mapel --</option>
                <?php foreach($mataPelajaranList as $k=>$v): ?>
                    <option value="<?= esc($k) ?>" <?= ($selectedMapel===$k)?'selected':'' ?>><?= esc($v) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-1 flex gap-2">
            <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg transition">Tampilkan</button>
            <?php if($selectedKelas || $selectedMapel): ?>
            <a href="/admin/nilai/cetak" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if($selectedKelas && $selectedMapel): ?>
    <!-- Step 3: Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100/60 p-5 overflow-x-auto relative">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-800 flex items-center gap-2">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 text-white shadow"><i class="fas fa-table"></i></span>
                    <span>Rekap Nilai | Kelas <?= esc($selectedKelas) ?> - <?= esc($selectedMapel) ?></span>
                </h2>
                <p class="mt-1 text-xs text-gray-500">Dibuat otomatis pada <?= date('d/m/Y H:i') ?> oleh <?= esc($currentUser['nama']) ?></p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="exportExcel()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                <button type="button" onclick="printTable()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm"><i class="fas fa-print mr-1"></i> Print</button>
            </div>
        </div>
        <div id="printArea">
            <div class="rounded-xl ring-1 ring-gray-200 overflow-hidden">
            <table class="min-w-full text-xs md:text-sm border-collapse rekap-nilai">
                <thead>
                    <tr class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                        <th rowspan="2" class="px-2 py-2 border border-purple-700/40 text-center font-semibold tracking-wide">No</th>
                        <th rowspan="2" class="px-2 py-2 border border-purple-700/40 text-center font-semibold tracking-wide">NISN</th>
                        <th rowspan="2" class="px-3 py-2 border border-purple-700/40 text-left font-semibold tracking-wide">Nama Siswa</th>
                        <th colspan="<?= $phHeaderCount ?>" class="px-2 py-2 border border-purple-700/40 text-center font-semibold tracking-wide">Penilaian Harian (PH)</th>
                        <th rowspan="2" class="px-2 py-2 border border-purple-700/40 text-center font-semibold">Rata PH</th>
                        <th rowspan="2" class="px-2 py-2 border border-purple-700/40 text-center font-semibold">PTS</th>
                        <th rowspan="2" class="px-2 py-2 border border-purple-700/40 text-center font-semibold">PAS</th>
                        <th rowspan="2" class="px-2 py-2 border border-purple-700/40 text-center font-semibold">Rapot</th>
                    </tr>
                    <tr class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white/90">
                        <?php for($i=1;$i<=$phHeaderCount;$i++): ?>
                            <th class="px-2 py-1 border border-purple-700/40 text-center font-medium">PH <?= $i ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!$reportRows): ?>
                        <tr><td colspan="<?= 3 + $phHeaderCount + 4 ?>" class="text-center py-4 text-gray-500 bg-white">Tidak ada data nilai.</td></tr>
                    <?php else: ?>
                        <?php foreach($reportRows as $idx=>$row): $isOdd = $idx % 2 === 1; ?>
                        <tr class="<?= $isOdd ? 'bg-white' : 'bg-purple-50/40' ?> hover:bg-amber-50 transition">
                            <td class="border border-gray-200 px-2 py-1 text-center font-medium text-gray-700"><?= $row['no'] ?></td>
                            <td class="border border-gray-200 px-2 py-1 text-center text-gray-700"><?= esc($row['nisn']) ?></td>
                            <td class="border border-gray-200 px-3 py-1 text-gray-700 font-medium"><?= esc($row['nama']) ?></td>
                            <?php for($i=1;$i<=$phHeaderCount;$i++): $val = $row['ph'][$i] ?? ''; ?>
                                <td class="border border-gray-200 px-2 py-1 text-center <?= ($val!=='' && $val < 70) ? 'text-red-600 font-semibold' : 'text-gray-700' ?>"><?= ($val!==''?esc($val):'-') ?></td>
                            <?php endfor; ?>
                            <td class="border border-gray-200 px-2 py-1 text-center font-semibold text-indigo-700"><?= esc($row['avg_ph']) ?></td>
                            <td class="border border-gray-200 px-2 py-1 text-center text-gray-700"><?= esc($row['pts']) ?></td>
                            <td class="border border-gray-200 px-2 py-1 text-center text-gray-700"><?= esc($row['pas']) ?></td>
                            <td class="border border-gray-200 px-2 py-1 text-center font-bold text-purple-700"><?= esc($row['rapot']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow border border-gray-100 p-10 text-center text-gray-500">
            <p>Pilih kelas dan mata pelajaran lalu klik Tampilkan untuk melihat tabel nilai.</p>
        </div>
    <?php endif; ?>
</div>
<script>
function printTable(){
    const area = document.getElementById('printArea');
    if(!area) return;
    const css = `@page { size: A4 landscape; margin:18mm 14mm 18mm 14mm; }
        body{font-family:Arial, sans-serif;font-size:11px; }
        h1,h2,h3{margin:0 0 6px; text-align:center;}
        .title-block{margin-bottom:10px;}
        table{border-collapse:collapse;width:100%; font-size:10px;}
        th,td{border:1px solid #444;padding:4px;}
        th{background:#4f46e5;color:#fff;}
        thead tr:nth-child(2) th{background:#6366f1;color:#fff;}
        tbody tr:nth-child(even){background:#f5f3ff;}
        .ttd{page-break-inside:avoid;margin-top:32px;font-size:12px;}
        .ttd table{border:0;width:100%;}
        .ttd td{border:0;vertical-align:top;}
        .sig-name{font-weight:bold;text-decoration:underline;margin-top:70px;}
        .text-left{text-align:left;}
        .text-right{text-align:right;}
        .meta{font-size:11px;margin-top:2px;}
    /* Header center; only kolom nama siswa data tetap kiri */
    .rekap-nilai th{text-align:center;}
    .rekap-nilai td:not(:nth-child(3)){text-align:center;}
    .rekap-nilai td:nth-child(3){text-align:left;}
    /* Proporsional penuh halaman (landscape) */
    .rekap-nilai{width:100%; table-layout:fixed;}
    .rekap-nilai th:nth-child(1){width:3%;}
    .rekap-nilai th:nth-child(2){width:9%;}
    .rekap-nilai th:nth-child(3){width:24%;}
    /* Sisanya (PH, Rata, PTS, PAS, Rapot) bagi rata */
        `;
    // Lokalize tanggal Indonesia
    const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const now = new Date();
    const tglIndo = `${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;
        const kelas = '<?= esc($selectedKelas) ?>';
    const walikelasNamaRaw = '<?= esc($walikelasData['nama'] ?? '') ?>';
    const walikelasGelar = '<?= esc($walikelasData['gelar'] ?? '') ?>';
    const walikelasNama = (walikelasNamaRaw && walikelasGelar) ? (walikelasNamaRaw + ' ' + walikelasGelar) : walikelasNamaRaw;
    const walikelasNIP = '<?= esc($walikelasData['nip'] ?? '') ?>';
        const kepalaNama = '<?= esc($profilSekolah['nama_kepala_sekolah'] ?? '') ?>';
        const kepalaNIP = '<?= esc($profilSekolah['nip_kepala_sekolah'] ?? '') ?>';
        const kelasClean = kelas.replace(/^Kelas\s+/i,'');
        // Hitung jumlah kolom sebelum PTS: No,NISN,Nama + PH columns + Rata PH = phHeaderCount + 4
    const kepalaContent = `Kepala Sekolah<div style=\"height:55px;\"></div><div class=\"sig-name\">${kepalaNama || '( _________________________ )'}</div><div class=\"meta\">NIP: ${kepalaNIP || '_____________'} </div>`;
    // Kurangi jarak: 1) tanggal & label walikelas pada baris atas 2) spacer lebih pendek 40px
    const waliContent = `<div class=\"meta\">Jakarta, ${tglIndo}</div><div>Walikelas ${kelasClean}</div><div style=\"height:40px;\"></div><div class=\"sig-name\">${walikelasNama || '( _________________________ )'}</div><div class=\"meta\">NIP: ${walikelasNIP || '_____________'} </div>`;
    const signatureHTML = `\n<div class=\"ttd\" id=\"signatureSection\" style=\"margin-top:14px;\">\n  <div style=\"display:flex;justify-content:space-between;align-items:flex-start;gap:18px;\">\n    <div style=\"flex:1;min-width:240px;\">${kepalaContent}</div>\n    <div style=\"flex:0 0 auto;min-width:240px;\">${waliContent}</div>\n  </div>\n</div>`;
    const titleHTML = `<div class="title-block">\n  <h2 style=\"font-size:20px;font-weight:700;letter-spacing:.5px;\">Daftar Nilai Murid</h2>\n  <div style=\"text-align:center;font-size:15px;\">Kelas ${kelasClean} | Tahun Pelajaran 2025/2026</div>\n  <div style=\"text-align:center;font-size:14px;font-weight:700;margin-top:3px;\">SDN Grogol Utara 09</div>\n</div>`;
    const win = window.open('', '_blank');
        win.document.write('<html><head><title>Cetak Nilai</title><style>'+css+'</style></head><body>' + titleHTML + area.innerHTML + signatureHTML + '</body></html>');
    win.document.close();
    win.focus();
    win.print();
}

function exportExcel(){
    // Get current filter parameters
    const kelas = '<?= esc($selectedKelas) ?>';
    const mapel = '<?= esc($selectedMapel) ?>';
    
    if (!kelas || !mapel) {
        alert('Pilih kelas dan mata pelajaran terlebih dahulu');
        return;
    }
    
    // Show loading indicator
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Exporting...';
    button.disabled = true;
    
    // Create and submit form for file download
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '<?= base_url('admin/nilai/export-excel') ?>';
    
    const kelasInput = document.createElement('input');
    kelasInput.type = 'hidden';
    kelasInput.name = 'kelas';
    kelasInput.value = kelas;
    form.appendChild(kelasInput);
    
    const mapelInput = document.createElement('input');
    mapelInput.type = 'hidden';
    mapelInput.name = 'mapel';
    mapelInput.value = mapel;
    form.appendChild(mapelInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after a short delay
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}
</script>
<?= $this->endSection() ?>
