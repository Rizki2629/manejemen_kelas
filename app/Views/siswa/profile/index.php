<?= $this->extend('layouts/siswa_layout') ?>
<?= $this->section('title') ?>Profil Saya<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto space-y-6">
  <?php if (session()->getFlashdata('success')): ?>
  <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
    <div class="flex items-center gap-2">
      <i class="fas fa-check-circle"></i>
      <span><?= session()->getFlashdata('success') ?></span>
    </div>
  </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
  <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 shadow-sm">
    <div class="flex items-center gap-2">
      <i class="fas fa-exclamation-circle"></i>
      <span><?= session()->getFlashdata('error') ?></span>
    </div>
  </div>
  <?php endif; ?>

  <?php if (session('errors')): ?>
  <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 shadow-sm">
    <div class="font-semibold mb-2">Periksa kembali form ubah password:</div>
    <ul class="list-disc list-inside space-y-1 text-sm">
      <?php foreach (session('errors') as $error): ?>
      <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- Page Header (like admin) -->
  <div class="rounded-2xl p-6 bg-white shadow-xl border border-slate-200">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-600 text-white flex items-center justify-center shadow">
          <i class="fas fa-user-graduate text-sm"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold text-slate-900">Profile Siswa</h1>
          <p class="text-sm text-slate-500">Kelola informasi profil siswa Anda</p>
        </div>
      </div>
      <a href="/siswa" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-300 shadow">
        Kembali
      </a>
    </div>
  </div>

  <!-- Two-column layout -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Left: Avatar & quick info (styled like admin) -->
    <div class="rounded-2xl bg-white p-6 shadow-xl border border-slate-200">
      <?php $initial = strtoupper(mb_substr(trim($student['nama'] ?? 'S'), 0, 2, 'UTF-8')); ?>
      <div class="flex flex-col items-center text-center">
        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg mb-3">
          <?= esc($initial) ?>
        </div>
        <div class="flex items-center gap-2">
          <h2 class="text-lg font-semibold text-slate-900"><?= esc($student['nama'] ?? '-') ?></h2>
        </div>
        <div class="mt-1 text-sm text-indigo-600">@<?= esc($student['nisn'] ?? ($student['nis'] ?? '-')) ?></div>
        <div class="mt-1 text-sm text-slate-600">Kelas <?= esc($class['nama'] ?? '-') ?></div>

        <!-- Role pill -->
        <div class="mt-4 w-full">
          <div class="rounded-xl bg-indigo-50 text-indigo-700 px-4 py-3 border border-indigo-100">
            <div class="text-xs uppercase tracking-wide">Role</div>
            <div class="font-semibold">Siswa</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: Detail info -->
    <div class="rounded-2xl bg-white p-6 shadow-xl border border-slate-200">
      <div class="flex items-center gap-2 mb-4">
        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center">
          <i class="fas fa-info text-xs"></i>
        </div>
        <h3 class="font-semibold text-slate-900">Informasi Detail</h3>
      </div>
      <?php if ($student): ?>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <div class="text-xs text-slate-500 mb-1">Nama Lengkap</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($student['nama']) ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Username</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($student['nisn'] ?? ($student['nis'] ?? '-')) ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">NIPD</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($student['nis'] ?? '-') ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">NISN</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($student['nisn'] ?? '-') ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Kelas</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($class['nama'] ?? '-') ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Jenis Kelamin</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc(($student['jenis_kelamin']==='L'?'Laki-laki':($student['jenis_kelamin']==='P'?'Perempuan':($student['jenis_kelamin']??'-')))) ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Tempat, Tanggal Lahir</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc(($ttlTempat ?? '-') . ', ' . ($ttlTanggal ?? '-')) ?></div>
        </div>
        <div>
          <div class="text-xs text-slate-500 mb-1">Agama</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($agama ?? '-') ?></div>
        </div>
        <div class="md:col-span-2">
          <div class="text-xs text-slate-500 mb-1">Alamat</div>
          <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-800 font-medium"><?= esc($student['alamat'] ?? '-') ?></div>
        </div>
      </div>
      <div class="mt-6 flex gap-3">
        <a href="/siswa" class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-4 py-2 rounded-xl font-semibold text-center transition-all shadow">Kembali</a>
      </div>
      <?php else: ?>
        <div class="text-slate-600">Data siswa tidak ditemukan.</div>
      <?php endif; ?>
    </div>
  </div>

  <div class="rounded-2xl bg-white p-6 shadow-xl border border-slate-200">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-2">
          <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
            <i class="fas fa-lock text-sm"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Keamanan Akun</h3>
        </div>
        <p class="text-sm text-slate-500">Ganti password akun siswa tanpa keluar dari menu profil.</p>
      </div>
      <button type="button" onclick="showChangePasswordModal()" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-4 py-2 rounded-xl font-semibold transition-all shadow">
        Ubah Password
      </button>
    </div>
  </div>

  <!-- Ibadah Card (if Islam) -->
  <?php if (!empty($isIslam) && !empty($ibadahHabitId)): ?>
  <div x-data="ibadahTab()" x-init="init()" class="rounded-2xl p-6 bg-white border border-violet-200 shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded-md bg-violet-100 text-violet-700 flex items-center justify-center"><i class="fas fa-mosque text-xs"></i></div>
        <h3 class="font-semibold text-violet-700">Ibadah Harian</h3>
      </div>
      <input type="date" x-model="date" @change="load()" class="px-3 py-2 rounded-lg bg-white border border-slate-300 focus:outline-none focus:ring-2 focus:ring-violet-500" />
    </div>
    <div class="flex flex-wrap gap-2">
      <template x-for="p in prayers" :key="p">
        <button type="button" @click="toggle(p)" class="px-3 py-1.5 rounded-lg border text-sm transition-colors"
          :class="selected.includes(p) ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-700 border-violet-200 hover:bg-slate-50'">
          <span x-text="p"></span>
        </button>
      </template>
    </div>
    <div class="mt-4 text-right">
      <button @click="save()" :disabled="loading" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-fuchsia-500 text-white shadow hover:shadow-lg">Simpan</button>
    </div>
  </div>
  <script>
  function ibadahTab(){
    return {
      habitId: <?= (int)($ibadahHabitId ?? 0) ?>,
      date: '<?= esc($today) ?>',
      prayers: ['Subuh','Dzuhur','Ashar','Maghrib','Isya'],
      selected: [],
      loading: false,
      async init(){ await this.load(); },
      async load(){
        this.loading = true;
        try{
          const res = await fetch(`/siswa/summary?date=${encodeURIComponent(this.date)}`);
          const {data} = await res.json();
          const row = (data||[]).find(r=> r.habit_id == this.habitId);
          if (row && row.notes) {
            try { const j = JSON.parse(row.notes); this.selected = Array.isArray(j.prayers) ? j.prayers : []; } catch(e){ this.selected = []; }
          } else { this.selected = []; }
        } finally { this.loading = false; }
      },
      toggle(p){
        const i = this.selected.indexOf(p);
        if (i===-1) this.selected.push(p); else this.selected.splice(i,1);
      },
      async save(){
        this.loading = true;
        try{
          const payload = { date: this.date, habits: {} };
          payload.habits[this.habitId] = { prayers: this.selected, bool: this.selected.length>0 };
          const res = await fetch('/siswa/logs', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
          if(!res.ok){ alert('Gagal menyimpan'); return; }
        } finally { this.loading = false; }
      }
    }
  }
  </script>
  <?php endif; ?>
</div>

<div id="changePasswordModal" class="fixed inset-0 bg-slate-900/60 hidden z-50 items-center justify-center p-4">
  <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-xl font-bold text-slate-900">Ubah Password</h3>
      <button type="button" onclick="hideChangePasswordModal()" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form action="<?= base_url('siswa/profile/change-password') ?>" method="POST" class="space-y-4">
      <?= csrf_field() ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Password Saat Ini</label>
        <input type="password" name="current_password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru</label>
        <input type="password" name="new_password" required minlength="6" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all">
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 py-3 font-semibold text-white shadow transition-all hover:from-emerald-600 hover:to-teal-700">Simpan</button>
        <button type="button" onclick="hideChangePasswordModal()" class="flex-1 rounded-xl bg-slate-100 py-3 font-semibold text-slate-700 transition-colors hover:bg-slate-200">Batal</button>
      </div>
    </form>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
function showChangePasswordModal() {
  const modal = document.getElementById('changePasswordModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function hideChangePasswordModal() {
  const modal = document.getElementById('changePasswordModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
