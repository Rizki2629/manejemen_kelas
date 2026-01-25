<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
        <i class="fas fa-award text-indigo-600 mr-3"></i>Input Nilai <?= strtoupper(esc($examType)) ?>
    </h1>
    <p class="text-gray-600 mb-6">Pilih kelas dan mata pelajaran lalu input nilai <?= strtoupper(esc($examType)) ?>.</p>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <?php if ($userRole === 'admin'): ?>
            <div>
                <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <select name="kelas" id="kelas" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Pilih Kelas</option>
                    <?php foreach ($availableClasses as $c): $v = is_array($c)?($c['kelas']??''):$c; ?>
                        <option value="<?= esc($v) ?>" <?= ($selectedKelas===$v?'selected':'') ?>>Kelas <?= esc($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <input type="hidden" name="kelas" value="<?= esc($userKelas) ?>">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <div class="px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 font-semibold">Kelas <?= esc($userKelas) ?></div>
            </div>
            <?php endif; ?>

            <div class="sm:col-span-2 lg:col-span-2">
                <label for="mapel" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                <select name="mapel" id="mapel" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Pilih Mapel</option>
                    <?php foreach ($orderedMapel as $m): ?>
                        <option value="<?= esc($m) ?>" <?= ($selectedMapel===$m?'selected':'') ?>><?= esc($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold shadow-lg">Tampilkan</button>
            </div>
        </form>
    </div>

    <?php if ($selectedKelas && $selectedMapel): ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Input Nilai <?= strtoupper(esc($examType)) ?></h3>
                <p class="text-sm text-gray-500">Kelas <?= esc($selectedKelas) ?> • Mapel <?= esc($selectedMapel) ?></p>
            </div>
            <div>
                <label class="text-sm text-gray-600 mr-2">Tanggal</label>
                <input type="date" id="tanggalUjian" value="<?= date('Y-m-d') ?>" class="px-3 py-2 border border-gray-200 rounded-xl">
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-100 rounded-xl">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NISN</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Nilai <?= strtoupper(esc($examType)) ?></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (!empty($students)): foreach ($students as $i => $s): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900"><?= $i+1 ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600"><?= esc($s['nisn']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-medium"><?= esc($s['nama']) ?></td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" min="0" max="100" step="1" data-siswa-id="<?= $s['id'] ?>" class="w-24 px-2 py-1.5 text-center border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="0-100">
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada siswa</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex gap-3">
            <button id="saveExam" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">Simpan Nilai <?= strtoupper(esc($examType)) ?></button>
            <a href="<?= base_url('admin/nilai/'.$examType) ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold">Reset</a>
        </div>
    </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <i class="fas fa-info-circle text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-600">Silakan pilih kelas dan mapel terlebih dahulu</p>
        </div>
    <?php endif; ?>
</div>

<script>
// Auto-submit when both filters selected (for quicker flow)
(function(){
  const form = document.querySelector('form[method="GET"]');
  const kelas = document.getElementById('kelas');
  const mapel = document.getElementById('mapel');
  function trySubmit(){ if(form && (!kelas || kelas.value) && mapel && mapel.value){ form.requestSubmit?form.requestSubmit():form.submit(); } }
  if (kelas) kelas.addEventListener('change', trySubmit);
  if (mapel) mapel.addEventListener('change', trySubmit);
})();

// Save handler
(function(){
  const btn = document.getElementById('saveExam');
  if (!btn) return;
  btn.addEventListener('click', async function(){
    const inputs = document.querySelectorAll('input[data-siswa-id]');
    const grades = [];
    inputs.forEach(inp=>{ if(inp.value !== '' && inp.value !== null){ grades.push({ siswa_id: parseInt(inp.dataset.siswaId,10), nilai: parseFloat(inp.value) }); }});
    if (!grades.length){ alert('Isi minimal satu nilai'); return; }
    const tanggal = document.getElementById('tanggalUjian').value || '<?= date('Y-m-d') ?>';
    const kelas = '<?= esc($selectedKelas ?? '') ?>';
    const mapel = '<?= esc($selectedMapel ?? '') ?>';
    const jenis = '<?= esc($examType) ?>';
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    try {
      const resp = await fetch('<?= base_url('admin/nilai/store-bulk-exam') ?>',{
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrfHash },
        body: JSON.stringify({ [csrfName]: csrfHash, kelas, mapel, tanggal, jenis, grades })
      });
      const data = await resp.json();
      if (resp.ok && data.status === 'ok'){
        alert('Nilai berhasil disimpan');
        // stay on page
      } else {
        alert(data.message || 'Gagal menyimpan nilai');
      }
    } catch(err){
      console.error(err); alert('Terjadi kesalahan saat menyimpan');
    }
  });
})();
</script>

<?= $this->endSection() ?>
