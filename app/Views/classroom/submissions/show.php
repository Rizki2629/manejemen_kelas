<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="/classroom/assignments/<?= $assignment['id'] ?>" class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-500 font-medium"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 uppercase tracking-wide"><i class="fas fa-file-alt"></i> Submission</span>
    </div>
    <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 leading-tight mb-2">Submission Saya</h2>
    <p class="text-sm text-gray-500 mb-8">Tugas: <span class="font-medium text-gray-700"><?= esc($assignment['judul']) ?></span></p>
    <?php if (!$submission): ?>
        <div class="bg-white/70 backdrop-blur border border-dashed border-purple-200 rounded-2xl p-8 text-center">
            <p class="text-gray-600 mb-4">Belum ada submission.</p>
            <a class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium shadow hover:shadow-md transition" href="/classroom/assignments/<?= $assignment['id'] ?>/submit"><i class="fas fa-upload text-xs"></i> Kirim Sekarang</a>
        </div>
    <?php else: ?>
        <div class="mb-4 flex flex-wrap items-center gap-4 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1"><i class="fas fa-clock text-[10px]"></i> Dikirim: <?= esc($submission['submitted_at']) ?></span>
            <?php if($submission['late']): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-600"><i class="fas fa-exclamation-circle text-[10px]"></i> Terlambat</span><?php endif; ?>
            <?php if($submission['score'] !== null): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700"><i class="fas fa-star text-[10px]"></i> Dinilai</span><?php else: ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500"><i class="fas fa-hourglass-half text-[10px]"></i> Menunggu Nilai</span><?php endif; ?>
        </div>
        <div class="bg-white/90 backdrop-blur border border-gray-100 rounded-2xl p-6 mb-6 shadow-sm">
            <?php if (trim($submission['content_text'])): ?>
                <pre class="whitespace-pre-wrap text-sm font-mono bg-gray-50 border border-gray-100 rounded-xl p-4 mb-5 overflow-x-auto"><?= esc($submission['content_text']) ?></pre>
            <?php endif; ?>
            <?php if ($submission['content_html']): ?>
                <div class="prose max-w-none">
                    <?= $submission['content_html'] ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($attachments)): ?>
            <div class="mb-6">
                <h3 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-600">Lampiran</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    <?php foreach($attachments as $att): ?>
                    <a class="group flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-white hover:border-purple-300 hover:bg-purple-50/50 transition" href="/classroom/attachment/<?= $att['id'] ?>/download">
                        <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-pink-500 to-purple-500 text-white rounded-lg flex items-center justify-center shadow">
                            <i class="fas fa-file text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate group-hover:text-purple-700"><?= esc($att['original_name']) ?></p>
                        </div>
                        <i class="fas fa-download text-xs text-purple-400 group-hover:text-purple-600"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($submission['score'] !== null): ?>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-5 mb-6 text-sm">
                <div class="flex items-center gap-2 mb-2 text-green-700 font-semibold"><i class="fas fa-award text-xs"></i> Nilai</div>
                <div class="text-2xl font-bold text-green-600 mb-2 leading-none"><?= esc($submission['score']) ?></div>
                <?php if($submission['feedback_text']): ?>
                    <div class="text-gray-700"><span class="font-medium">Feedback:</span> <span class="italic"><?= esc($submission['feedback_text']) ?></span></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="flex gap-3">
            <a href="/classroom/assignments/<?= $assignment['id'] ?>/submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium shadow hover:shadow-md transition"><i class="fas fa-edit text-xs"></i> Edit Submission</a>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
