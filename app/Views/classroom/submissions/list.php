<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-10 py-8 max-w-[1500px]">
    <!-- Header -->
    <div class="flex items-start justify-between flex-wrap gap-6 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2 text-purple-600">
                <i class="fas fa-file-alt text-lg"></i>
                <h1 class="text-3xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Submission</h1>
            </div>
            <p class="text-sm text-gray-500 font-medium">
                <?= esc($assignment['judul']) ?> • <span class="text-gray-600">Latihan <?= esc($assignment['kelas']) ?></span>
            </p>
        </div>
        <div class="text-xs text-right text-gray-500 font-medium mt-1">
            <?php if(!empty($assignment['due_at'])): ?>Deadline: <span class="text-gray-700 font-semibold"><?= esc($assignment['due_at']) ?></span><?php endif; ?>
        </div>
    </div>

    <?php if (session('success')): ?><div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> <?= session('success') ?></div><?php endif; ?>

    <!-- Stats Bar -->
    <?php
        $total = count($submissions);
        $graded = 0; $pending = 0; $notSubmitted = 0;
        foreach($submissions as $s){
            if(!empty($s['submitted_at'])){
                if($s['score']!==null) $graded++; else $pending++;
            } else { $notSubmitted++; }
        }
    ?>
    <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-wrap items-center gap-6 text-sm">
        <span class="px-4 py-1 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white font-semibold shadow text-xs">
            <?= $graded + $pending ?>/<?= $total ?> submissions
        </span>
        <span class="text-emerald-600 font-medium text-xs"><?= $graded ?> dinilai</span>
        <span class="text-amber-600 font-medium text-xs"><?= $pending ?> pending</span>
        <span class="text-gray-500 font-medium text-xs"><?= $total - ($graded + $pending) ?> belum submit</span>
        <div class="ml-auto text-[11px] text-gray-400 font-medium hidden sm:block">Update: <?= date('d M Y H:i') ?></div>
    </div>

    <!-- Search & Filters -->
    <div class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col lg:flex-row gap-4 items-stretch">
        <div class="relative flex-1">
            <input id="searchInput" type="text" placeholder="Cari nama siswa..." class="w-full h-11 rounded-xl border-gray-200 focus:ring-2 focus:ring-purple-200 focus:border-purple-400 pl-11 text-sm" />
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <div class="flex gap-3">
            <select id="statusFilter" class="h-11 rounded-xl border-gray-200 text-sm focus:ring-purple-200 focus:border-purple-400">
                <option value="">Semua Status</option>
                <option value="graded">Sudah Dinilai</option>
                <option value="pending">Pending Review</option>
                <option value="nosubmit">Belum Submit</option>
            </select>
            <select id="kelasFilter" class="h-11 rounded-xl border-gray-200 text-sm focus:ring-purple-200 focus:border-purple-400">
                <option value="">Semua Kelas</option>
                <?php $kelasSet=[]; foreach($submissions as $s){ $kelas=$assignment['kelas']; $kelasSet[$kelas]=true; } foreach(array_keys($kelasSet) as $k): ?>
                    <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm bg-white">
        <table class="min-w-full text-sm" id="submissionTable">
            <thead class="text-[11px] uppercase tracking-wide bg-gradient-to-r from-purple-50 via-pink-50 to-purple-50 text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold">No</th>
                    <th class="px-5 py-3 text-left font-semibold">Nama Siswa</th>
                    <th class="px-5 py-3 text-left font-semibold">Kelas</th>
                    <th class="px-5 py-3 text-left font-semibold">Waktu Submit</th>
                    <th class="px-5 py-3 text-left font-semibold">Status</th>
                    <th class="px-5 py-3 text-left font-semibold">Nilai</th>
                    <th class="px-5 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="submissionTbody">
                <?php if(empty($submissions)): ?>
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada data.</td></tr>
                <?php endif; ?>
                <?php $no=1; foreach($submissions as $s):
                    $nama = $s['siswa_nama'] ?? $s['tbs_nama'] ?? $s['user_nama'] ?? ('User #'.$s['siswa_user_id']);
                    $kelas = $assignment['kelas'];
                    $submittedAt = $s['submitted_at'] ? date('d M, H:i', strtotime($s['submitted_at'])) : '-';
                    $hasSubmit = !empty($s['submitted_at']);
                    $gradedRow = $hasSubmit && $s['score']!==null;
                ?>
                <tr class="hover:bg-purple-50/40 transition" data-nama="<?= strtolower(esc($nama)) ?>" data-status="<?= $gradedRow?'graded':($hasSubmit?'pending':'nosubmit') ?>" data-kelas="<?= esc($kelas) ?>">
                    <td class="px-5 py-3 font-medium text-gray-700"><?= $no++ ?></td>
                    <td class="px-5 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shadow ring-2 ring-white" style="background: linear-gradient(135deg,#a855f7,#6366f1)">
                            <?= strtoupper(substr($nama,0,1)) ?>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 leading-tight"><?= esc($nama) ?></p>
                            <p class="text-[10px] text-gray-400 font-medium">ID: <?= esc($s['siswa_user_id']) ?></p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-700 font-medium"><?= esc($kelas) ?></td>
                    <td class="px-5 py-3 text-gray-600 font-medium"><?= esc($submittedAt) ?></td>
                    <td class="px-5 py-3">
                        <?php if($gradedRow): ?>
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">Sudah Submit</span>
                        <?php elseif($hasSubmit): ?>
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">Pending Review</span>
                        <?php else: ?>
                            <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-600 text-[11px] font-semibold">Belum Submit</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <?php if($s['score']!==null): ?>
                            <span class="inline-flex items-center justify-center w-10 h-8 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100"><?= esc($s['score']) ?></span>
                        <?php elseif($hasSubmit): ?>
                            <span class="inline-flex items-center justify-center w-10 h-8 rounded-lg text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100">-</span>
                        <?php else: ?>
                            <span class="inline-flex items-center justify-center w-10 h-8 rounded-lg text-xs font-semibold bg-gray-50 text-gray-400 border border-gray-100">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="/classroom/submissions/<?= $s['id'] ?>/grade" class="w-8 h-8 grid place-content-center rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-600 text-xs" title="Nilai"><i class="fas fa-pen"></i></a>
                            <form method="post" action="/classroom/assignments/<?= $assignment['id'] ?>/submissions/<?= $s['siswa_user_id'] ?>/auto-grade" onsubmit="return confirm('Auto-grade PG untuk siswa ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-8 h-8 grid place-content-center rounded-lg bg-green-50 hover:bg-green-100 text-green-600 text-xs" title="Rilis Nilai"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="post" action="/classroom/assignments/<?= $assignment['id'] ?>/submissions/<?= $s['siswa_user_id'] ?>/reset" onsubmit="return confirm('Reset latihan siswa ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-8 h-8 grid place-content-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="Reset"><i class="fas fa-undo"></i></button>
                            </form>
                            <form method="post" action="/classroom/assignments/<?= $assignment['id'] ?>/submissions/<?= $s['id'] ?>/delete" onsubmit="return confirm('Hapus submission ini permanen? Tindakan tidak bisa dibatalkan.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-8 h-8 grid place-content-center rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs" title="Hapus Submission"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if(!empty($submissions)): ?>
        <div class="px-6 py-4 flex items-center justify-between text-[11px] text-gray-500 bg-gray-50 border-t border-gray-100">
            <span>Menampilkan <?= count($submissions) ?> siswa</span>
            <!-- Simple placeholder pagination (client-side could be added later) -->
            <div class="flex gap-1">
                <button disabled class="w-8 h-8 rounded-lg bg-white border text-gray-300">«</button>
                <button class="w-8 h-8 rounded-lg bg-purple-600 text-white font-semibold">1</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
// Simple client-side filtering
document.addEventListener('DOMContentLoaded',()=>{
  const search=document.getElementById('searchInput');
  const status=document.getElementById('statusFilter');
  const kelas=document.getElementById('kelasFilter');
  const rows=[...document.querySelectorAll('#submissionTbody tr[data-nama]')];
  function apply(){
    const q=(search.value||'').trim().toLowerCase();
    const st=status.value; const kl=kelas.value;
    let visible=0; rows.forEach(r=>{
      const matchNama = !q || r.dataset.nama.includes(q);
      const matchStatus = !st || r.dataset.status===st;
      const matchKelas = !kl || r.dataset.kelas===kl;
      const show = matchNama && matchStatus && matchKelas;
      r.style.display = show?'' : 'none'; if(show) visible++;
    });
  }
  [search,status,kelas].forEach(el=>el && el.addEventListener('input',apply));
});
</script>
<?= $this->endSection() ?>
