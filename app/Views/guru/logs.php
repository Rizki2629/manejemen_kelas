<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="space-y-4" x-data>
  <div class="rounded-2xl p-4 bg-gradient-to-br from-sky-50 to-white border border-sky-200 shadow-lg" data-aos="fade-up" data-aos-duration="600">
    <form method="get" class="grid md:grid-cols-5 gap-3 items-end">
      <div>
    <label class="block text-sm mb-1 text-sky-700 font-medium">Kelas</label>
  <select name="classId" class="w-full px-3 py-2 rounded-lg bg-white border border-sky-200 focus:outline-none focus:ring-2 focus:ring-sky-500">
          <option value="">Semua</option>
          <?php if (!empty($classes)): foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (string)$classId === (string)$c['id'] ? 'selected' : '' ?>><?= esc($c['nama']) ?></option>
          <?php endforeach; endif; ?>
        </select>
      </div>
      <div>
    <label class="block text-sm mb-1 text-fuchsia-700 font-medium">Dari</label>
  <input type="date" name="from" value="<?= esc($from) ?>" class="w-full px-3 py-2 rounded-lg bg-white border border-fuchsia-200 focus:outline-none focus:ring-2 focus:ring-fuchsia-500" />
      </div>
      <div>
    <label class="block text-sm mb-1 text-rose-700 font-medium">Sampai</label>
  <input type="date" name="to" value="<?= esc($to) ?>" class="w-full px-3 py-2 rounded-lg bg-white border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500" />
      </div>
      <div>
    <button class="w-full px-4 py-2 rounded-xl text-white hover:shadow-xl transition-all bg-gradient-to-r from-indigo-600 via-fuchsia-500 to-rose-500">Filter</button>
      </div>
      <div>
    <a href="/guru/logs/export?classId=<?= urlencode((string)$classId) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>" class="w-full block text-center px-4 py-2 rounded-xl text-white hover:shadow-xl transition-all bg-gradient-to-r from-emerald-600 to-teal-500">Export CSV</a>
      </div>
    </form>
  </div>

  <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-x-auto" data-aos="fade-up" data-aos-duration="600">
    <table class="min-w-full text-sm">
  <thead class="bg-gradient-to-r from-indigo-50 to-fuchsia-50">
        <tr>
          <th class="text-left px-4 py-2">Tanggal</th>
          <th class="text-left px-4 py-2">Siswa</th>
          <th class="text-left px-4 py-2">Kelas</th>
          <th class="text-left px-4 py-2">Kebiasaan</th>
          <th class="text-left px-4 py-2">Nilai</th>
          <th class="text-left px-4 py-2">Catatan</th>
        </tr>
      </thead>
      <tbody>
    <?php foreach($rows as $i=>$r): ?>
      <tr class="border-t border-slate-100 <?= $i%2===0 ? 'bg-white' : 'bg-slate-50' ?> hover:bg-slate-100/70">
            <td class="px-4 py-2"><?= esc($r['log_date']) ?></td>
            <td class="px-4 py-2"><?= esc($r['siswa_nama']) ?></td>
            <td class="px-4 py-2"><?= esc($r['kelas_nama']) ?></td>
            <td class="px-4 py-2"><?= esc($r['habit_nama']) ?></td>
            <td class="px-4 py-2">
              <?php
                $v = $r['value_bool']==1 ? 'Ya' : 'Tidak';
                if ($r['value_time']) $v .= ' / '.$r['value_time'];
                if ($r['value_number'] !== null) $v .= ' / '.$r['value_number'];
                echo esc($v);
              ?>
            </td>
            <td class="px-4 py-2"><?= esc($r['notes']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php $pages = (int)ceil($total / $perPage); if ($pages>1): ?>
  <div class="flex items-center justify-center gap-2">
    <?php for ($i=1;$i<=$pages;$i++): $active=$i==$page; ?>
      <a href="?classId=<?= urlencode((string)$classId) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&page=<?= $i ?>" class="px-3 py-1 rounded-lg border border-slate-200 <?= $active?'bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white border-transparent':'hover:bg-slate-100' ?> transition-all"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>
