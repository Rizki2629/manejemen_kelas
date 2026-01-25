<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="/classroom/assignments/<?= $assignment['id'] ?>/submissions" class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-500 font-medium"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 uppercase tracking-wide"><i class="fas fa-pen"></i> Penilaian</span>
    </div>
    <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 leading-tight mb-2">Nilai Submission #<?= $submission['id'] ?></h2>
    <p class="text-sm text-gray-500 mb-8">Tugas: <span class="font-medium text-gray-700"><?= esc($assignment['judul']) ?></span></p>
    <div class="bg-white/90 backdrop-blur border border-gray-100 rounded-2xl p-6 mb-8 shadow-sm">
        <div class="mb-4 flex flex-wrap items-center gap-4 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1"><i class="fas fa-clock text-[10px]"></i> Dikirim: <?= esc($submission['submitted_at']) ?></span>
            <?php if($submission['late']): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-600"><i class="fas fa-exclamation-circle text-[10px]"></i> Terlambat</span><?php endif; ?>
            <?php if($submission['score'] !== null): ?><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700"><i class="fas fa-star text-[10px]"></i> Sudah Dinilai</span><?php endif; ?>
        </div>
        <?php if (trim($submission['content_text'])): ?>
            <pre class="whitespace-pre-wrap text-sm font-mono bg-gray-50 border border-gray-100 rounded-xl p-4 mb-5 overflow-x-auto"><?= esc($submission['content_text']) ?></pre>
        <?php endif; ?>
        <?php if ($submission['content_html']): ?>
            <div class="prose max-w-none">
                <?= $submission['content_html'] ?>
            </div>
        <?php endif; ?>
    </div>
    <form method="post" action="/classroom/submissions/<?= $submission['id'] ?>/grade" class="space-y-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100 rounded-2xl p-6">
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Nilai</label>
            <input type="number" step="0.01" name="score" value="<?= esc($submission['score'] ?? '') ?>" class="border border-purple-200 focus:border-purple-400 focus:ring-0 rounded-lg w-full px-3 py-2 text-sm bg-white/80" required>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Feedback</label>
            <textarea name="feedback_text" rows="4" class="border border-purple-200 focus:border-purple-400 focus:ring-0 rounded-lg w-full px-3 py-2 text-sm bg-white/80"><?= esc($submission['feedback_text'] ?? '') ?></textarea>
        </div>
        <div class="flex justify-end">
            <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium shadow hover:shadow-md transition"><i class="fas fa-save text-xs"></i> Simpan Nilai</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
