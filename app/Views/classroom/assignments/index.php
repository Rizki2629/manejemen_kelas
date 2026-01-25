<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-500">Daftar Tugas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan kerjakan tugas kelas</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="get" class="flex items-center gap-2">
                <input type="text" name="kelas" value="<?= esc($kelasFilter) ?>" placeholder="Filter Kelas" class="px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <button class="px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-500">Terapkan</button>
            </form>
            <?php if (in_array($role,['guru','walikelas','admin'])): ?>
                <a href="/classroom/assignments/create" class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-600 to-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow hover:shadow-lg transition">
                    <i class="fas fa-plus text-xs"></i> <span>Buat Tugas</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php if (session('success')): ?><div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-start gap-2"><i class="fas fa-check-circle mt-0.5"></i><span><?= session('success') ?></span></div><?php endif; ?>
    <?php if (session('error')): ?><div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 whitespace-pre-line flex items-start gap-2"><i class="fas fa-exclamation-circle mt-0.5"></i><span><?= session('error') ?></span></div><?php endif; ?>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($assignments as $a): $subCount = $submissionCounts[$a['id']] ?? 0; ?>
        <div class="relative bg-white rounded-3xl border border-green-300/60 shadow-sm hover:shadow-lg transition overflow-hidden group">
            <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-green-50 via-transparent to-purple-50 opacity-0 group-hover:opacity-100 transition"></div>
            <div class="p-5 flex flex-col h-full relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <?php if ($a['mapel']): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-2xl text-[11px] font-semibold bg-green-100 text-green-800 shadow-inner">
                            <i class="fas fa-book-open text-[10px]"></i> <?= esc($a['mapel']) ?>
                        </span>
                        <?php endif; ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium tracking-wide <?= $a['visibility']==='published' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' ?>">
                            <?= strtoupper($a['visibility']) ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-semibold text-gray-700">Kelas <?= esc($a['kelas']) ?></span>
                        <div class="relative">
                            <button data-menu-button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition" aria-label="Menu">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <div class="hidden absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-20" data-menu-panel>
                                <a href="/classroom/assignments/<?= $a['id'] ?>" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50"><i class="fas fa-eye text-[10px]"></i> Lihat</a>
                                <?php if (in_array($role,['guru','walikelas','admin'])): ?>
                                    <a href="/classroom/assignments/<?= $a['id'] ?>/edit" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50"><i class="fas fa-edit text-[10px]"></i> Edit</a>
                                    <a href="/classroom/assignments/<?= $a['id'] ?>?as=siswa" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50"><i class="fas fa-user-graduate text-[10px]"></i> Lihat sbg Siswa</a>
                                    <form action="/classroom/assignments/<?= $a['id'] ?>/delete" method="post" onsubmit="return confirm('Hapus tugas ini?')" class="m-0">
                                        <?= csrf_field() ?>
                                        <button class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50"><i class="fas fa-trash text-[10px]"></i> Hapus</button>
                                    </form>
                                <?php else: ?>
                                    <a href="/classroom/assignments/<?= $a['id'] ?>/submit" class="flex items-center gap-2 px-3 py-2 text-xs text-purple-600 hover:bg-purple-50"><i class="fas fa-pencil-alt text-[10px]"></i> Kerjakan</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="font-bold text-xl leading-snug mb-3 text-blue-900">
                    <a href="/classroom/assignments/<?= $a['id'] ?>" class="hover:underline decoration-pink-400/50">
                        <?= esc($a['judul']) ?>
                    </a>
                </h3>
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-medium mb-4">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-100 text-blue-700"><i class="fas fa-users text-[10px]"></i> <span><?= $subCount ?></span> siswa</span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg <?= $a['due_at'] && strtotime($a['due_at']) < time() ? 'bg-red-100 text-red-600' : 'bg-sky-100 text-sky-700' ?>">
                        <i class="fas fa-clock text-[10px]"></i>
                        <?= $a['due_at']? esc(date('d/m/Y - H:i', strtotime($a['due_at']))) : 'Tidak ada deadline' ?>
                    </span>
                    <?php if ($a['allow_late']): ?><span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-100 text-amber-700"><i class="fas fa-hourglass-half text-[10px]"></i> Late OK</span><?php endif; ?>
                </div>
                <div class="mt-auto flex items-center justify-between pt-2">
                    <a href="/classroom/assignments/<?= $a['id'] ?>" class="text-xs font-semibold text-purple-600 hover:text-purple-500 inline-flex items-center gap-1">Detail <i class="fas fa-arrow-right text-[10px]"></i></a>
                    <?php if (in_array($role,['guru','walikelas','admin']) && $a['visibility']==='draft'): ?>
                        <form action="/classroom/assignments/<?= $a['id'] ?>/publish" method="post">
                            <?= csrf_field() ?>
                            <button class="text-[10px] bg-green-600 text-white px-2 py-1 rounded shadow hover:bg-green-500">Publish</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($assignments)): ?>
            <div class="col-span-full bg-white border border-dashed border-gray-300 rounded-xl p-10 text-center">
                <p class="text-gray-500 text-sm">Belum ada tugas. <?php if (in_array($role,['guru','walikelas','admin'])): ?><a href="/classroom/assignments/create" class="text-purple-600 underline">Buat tugas pertama</a><?php endif; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('click', e => {
    document.querySelectorAll('[data-menu-panel]').forEach(p=>{ if(!p.contains(e.target)) p.classList.add('hidden'); });
    const btn = e.target.closest('[data-menu-button]');
    if(btn){
        const panel = btn.parentElement.querySelector('[data-menu-panel]');
        panel.classList.toggle('hidden');
    }
});
</script>
<?= $this->endSection() ?>
