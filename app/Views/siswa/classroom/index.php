<?= $this->extend('layouts/siswa_layout') ?>
<?= $this->section('title') ?>Classroom<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $activeTab = strtolower((string)($_GET['tab'] ?? '')); if (!in_array($activeTab,['materi','tugas'])) $activeTab=''; ?>
<div class="space-y-10">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-3"><i class="fas fa-chalkboard-teacher text-indigo-600"></i> Classroom <span class="text-base font-normal text-slate-500">Kelas <?= esc($kelas ?? '-') ?></span></h1>
            <p class="text-sm text-slate-500 mt-1">Materi & Tugas yang sudah dipublikasikan oleh guru / wali kelas Anda.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-start gap-3">
            <i class="fas fa-info-circle mt-1"></i>
            <div>
                <p class="font-medium mb-1">Informasi</p>
                <p><?= esc($message) ?></p>
            </div>
        </div>
    <?php else: ?>

    <!-- Materi Section -->
    <section class="space-y-4" <?= ($activeTab==='tugas') ? 'style="display:none"':'' ?>>
        <div class="flex items-center gap-2">
            <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="fas fa-book-open text-fuchsia-600"></i> Materi Terbaru</h2>
            <span class="text-xs bg-fuchsia-100 text-fuchsia-700 px-2 py-0.5 rounded-full font-medium"><?= count($lessons) ?></span>
        </div>
        <?php if (empty($lessons)): ?>
            <div class="p-5 rounded-xl border border-dashed border-slate-300 bg-white/60 text-slate-500 text-sm">Belum ada materi untuk kelas Anda.</div>
        <?php else: ?>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($lessons as $ls): ?>
                <a href="<?= base_url('classroom/lessons/'.$ls['id']) ?>" class="group relative rounded-2xl p-5 bg-white/70 backdrop-blur border border-slate-200/60 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-indigo-50 via-fuchsia-50 to-pink-50"></div>
                    <div class="flex items-start gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-fuchsia-500 to-pink-500 text-white flex items-center justify-center shadow-inner"><i class="fas fa-book text-lg"></i></div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-slate-800 leading-snug line-clamp-2 group-hover:text-fuchsia-600">
                                <?= esc($ls['judul']) ?>
                            </h3>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-medium uppercase tracking-wide text-slate-500">
                                <span class="px-2 py-0.5 rounded bg-slate-100">Mapel: <?= esc($ls['mapel'] ?: '-') ?></span>
                                <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">Materi</span>
                                <?php if (!empty($ls['published_at'])): ?>
                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700"><?= date('d M Y', strtotime($ls['published_at'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- Tugas Section -->
    <section class="space-y-4" <?= ($activeTab==='materi') ? 'style="display:none"':'' ?>>
        <div class="flex items-center gap-2">
            <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="fas fa-tasks text-indigo-600"></i> Tugas / Latihan</h2>
            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium"><?= count($assignments) ?></span>
        </div>
        <?php if (empty($assignments)): ?>
            <div class="p-5 rounded-xl border border-dashed border-slate-300 bg-white/60 text-slate-500 text-sm">Belum ada tugas untuk kelas Anda.</div>
        <?php else: ?>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($assignments as $as): 
                $dueLabel = $as['due_at'] ? date('d M Y H:i', strtotime($as['due_at'])) : 'Tidak ada batas';
                $isOver = $as['due_at'] && strtotime($as['due_at']) < time();
            ?>
                <a href="<?= base_url('classroom/assignments/'.$as['id']) ?>" class="group relative rounded-2xl p-5 bg-white/70 backdrop-blur border border-slate-200/60 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition bg-gradient-to-br from-indigo-50 via-fuchsia-50 to-pink-50"></div>
                    <div class="flex items-start gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white flex items-center justify-center shadow-inner"><i class="fas fa-file-alt text-lg"></i></div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-slate-800 leading-snug line-clamp-2 group-hover:text-indigo-600">
                                <?= esc($as['judul']) ?>
                            </h3>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-medium uppercase tracking-wide text-slate-500">
                                <span class="px-2 py-0.5 rounded bg-slate-100">Mapel: <?= esc($as['mapel'] ?: '-') ?></span>
                                <span class="px-2 py-0.5 rounded bg-violet-100 text-violet-700">Tugas</span>
                                <span class="px-2 py-0.5 rounded <?= $isOver ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' ?>">Batas: <?= esc($dueLabel) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <?php endif; ?>
</div>
<?= $this->endSection() ?>