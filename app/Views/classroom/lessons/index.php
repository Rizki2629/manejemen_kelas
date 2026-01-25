<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-2 sm:px-4 lg:px-4 py-4">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8">
        <div class="space-y-2">
            <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500">Materi Kelas</h2>
            <p class="text-sm text-gray-500">Daftar materi terbaru untuk siswa</p>
            <?php if(!empty($stats)): ?>
                <div class="flex flex-wrap gap-3 pt-2 text-xs">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">Published: <strong><?= $stats['published'] ?? 0 ?></strong></span>
                    <?php if(isset($stats['drafts'])): ?><span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">Draft: <strong><?= $stats['drafts'] ?></strong></span><?php endif; ?>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">Total: <strong><?= $pagination['total'] ?? count($lessons) ?></strong></span>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto">
            <form method="get" class="flex flex-col sm:flex-row gap-2 w-full">
                <div class="flex gap-2 w-full">
                    <input type="text" name="kelas" value="<?= esc($kelasFilter) ?>" placeholder="Kelas (misal: 5A)" class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-indigo-400 focus:ring-0" />
                    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Cari judul / isi" class="flex-[2] px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-indigo-400 focus:ring-0" />
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-500 shadow">Filter</button>
                    <?php if($kelasFilter || ($q ?? '')): ?>
                        <a href="/classroom/lessons" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
            <?php if (in_array($role,['guru','walikelas','admin'])): ?>
                <a href="/classroom/lessons/create" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow hover:shadow-lg transition w-full sm:w-auto">
                    <i class="fas fa-plus text-xs"></i> <span>Buat Materi</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php if (session('success')): ?><div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-start gap-2"><i class="fas fa-check-circle mt-0.5"></i><span><?= session('success') ?></span></div><?php endif; ?>
    <?php if (session('error')): ?><div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 whitespace-pre-line flex items-start gap-2"><i class="fas fa-exclamation-circle mt-0.5"></i><span><?= session('error') ?></span></div><?php endif; ?>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($lessons as $l): ?>
            <?php
                $kelasRaw = trim($l['kelas']);
                $kelasDisplay = preg_match('/^kelas\s+/i', $kelasRaw) ? $kelasRaw : 'Kelas ' . $kelasRaw;
                $viewCount = (int)($l['view_count'] ?? 0);
                $mapel = $l['mapel'] ?: 'Mapel';
                // Snippet (70 chars) from konten
                $rawKonten = $l['konten'] ?? '';
                $plain = trim(preg_replace('/\s+/', ' ', strip_tags($rawKonten)));
                if (function_exists('mb_strlen')) {
                    $snippet = mb_strlen($plain) > 70 ? mb_substr($plain, 0, 70) . '…' : ($plain ?: '(Tidak ada konten)');
                } else {
                    $snippet = strlen($plain) > 70 ? substr($plain, 0, 70) . '…' : ($plain ?: '(Tidak ada konten)');
                }
                // Localized date (published_at preferred)
                $ts = $l['published_at'] ?: $l['created_at'] ?? date('Y-m-d H:i:s');
                try { $dt = new DateTime($ts); } catch (\Throwable $e) { $dt = new DateTime(); }
                $days = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $formattedDate = ($days[$dt->format('l')] ?? $dt->format('l')) . ', ' . $dt->format('j') . ' ' . ($months[(int)$dt->format('n')] ?? $dt->format('F')) . ' ' . $dt->format('Y');
            ?>
            <div class="lesson-card group relative bg-white rounded-2xl border border-green-300 hover:border-green-400 shadow-sm hover:shadow-lg transition cursor-pointer p-5 md:p-6 flex flex-col" data-href="/classroom/lessons/<?= $l['id'] ?>">
                <div class="flex items-start justify-between mb-3 md:mb-4 gap-3">
                    <div class="inline-flex items-center gap-1.5 md:gap-2 px-3 py-1.5 md:px-4 md:py-2 rounded-2xl bg-green-100/70">
                        <i class="fas fa-book-open text-green-700 text-base md:text-lg"></i>
                        <span class="text-base md:text-lg font-bold tracking-wide text-blue-700 leading-none"><?= esc($mapel) ?></span>
                    </div>
                    <div class="text-sm md:text-base font-semibold text-black leading-none mt-1 md:mt-2 whitespace-nowrap"><?= esc($kelasDisplay) ?></div>
                </div>
                <h3 class="text-xl md:text-2xl font-extrabold text-pink-500 mb-1 md:mb-2 leading-snug line-clamp-2">
                    <a href="/classroom/lessons/<?= $l['id'] ?>" class="hover:underline"><?= esc($l['judul']) ?></a>
                </h3>
                <p class="text-sm md:text-base text-black/90 mb-4 md:mb-6 line-clamp-2"><?= esc($snippet) ?></p>
                <div class="mt-auto w-full space-y-3">
                    <div class="flex items-center flex-wrap gap-3 md:gap-4 text-[11px] md:text-sm text-gray-500">
                        <span class="inline-flex items-center gap-1"><i class="fas fa-eye text-gray-400"></i><span><?= $viewCount ?></span></span>
                        <span class="inline-flex items-center px-4 py-1.5 md:px-5 md:py-2 bg-sky-500 text-white rounded-full font-medium shadow-sm text-[11px] md:text-sm">
                            <?= esc($formattedDate) ?>
                        </span>
                        <a href="/classroom/lessons/<?= $l['id'] ?>" class="hidden md:inline-flex ml-auto justify-center items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-semibold text-xs md:text-sm tracking-wide px-5 md:px-6 py-3 rounded-full shadow-md transition">
                            READ MORE
                            <span class="ml-1 w-6 h-6 inline-flex items-center justify-center bg-white/90 text-green-600 rounded-full text-sm"><i class="fas fa-angle-double-right"></i></span>
                        </a>
                    </div>
                </div>
                <?php if (in_array($role,['guru','walikelas','admin']) && $l['visibility']==='draft'): ?>
                    <form action="/classroom/lessons/<?= $l['id'] ?>/publish" method="post" class="absolute top-2 right-2">
                        <button class="text-[10px] bg-green-600 text-white px-2 py-1 rounded shadow hover:bg-green-500">Publish</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (empty($lessons)): ?>
            <div class="col-span-full bg-white border border-dashed border-gray-300 rounded-xl p-10 text-center">
                <p class="text-gray-500 text-sm">Belum ada materi. <?php if (in_array($role,['guru','walikelas','admin'])): ?><a href="/classroom/lessons/create" class="text-indigo-600 underline">Buat materi pertama</a><?php endif; ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php if(isset($pagination)): ?>
        <?= view('classroom/partials/pagination', ['pagination' => $pagination]) ?>
    <?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.lesson-card').forEach(function(card){
                card.addEventListener('click', function(e){
                    // Avoid navigation when clicking buttons/forms/links inside
                    if (e.target.closest('a, button, form')) return;
                    const href = card.getAttribute('data-href');
                    if(href){ window.location = href; }
                });
            });
        });
    </script>
 </div>
<?= $this->endSection() ?>
