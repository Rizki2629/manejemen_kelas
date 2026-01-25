<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="/classroom/assignments/<?= $assignment['id'] ?>" class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-500 font-medium"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 uppercase tracking-wide"><i class="fas fa-upload"></i> Submit</span>
    </div>
    <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 mb-2">Kirim Tugas</h2>
    <p class="text-sm text-gray-500 mb-8">Tugas: <span class="font-medium text-gray-700"><?= esc($assignment['judul']) ?></span></p>
    <?php if (session('success')): ?><div class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><i class="fas fa-circle-check mt-0.5"></i><div><?= session('success') ?></div></div><?php endif; ?>
    <?php if (session('error')): ?><div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><i class="fas fa-circle-exclamation mt-0.5"></i><div><?= session('error') ?></div></div><?php endif; ?>
    <form method="post" action="/classroom/assignments/<?= $assignment['id'] ?>/submit" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Konten (teks)</label>
            <textarea name="content_text" rows="6" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required><?= $submission['content_text'] ?? '' ?></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Konten HTML (opsional)</label>
            <textarea name="content_html" rows="6" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 font-mono text-sm focus:border-purple-400 focus:ring-0"><?= $submission['content_html'] ?? '' ?></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Lampiran</label>
            <div class="mt-1 rounded-xl border border-dashed border-purple-300 bg-gradient-to-r from-purple-50 to-pink-50 p-4">
                <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-purple-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-purple-500" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip" />
                <p class="mt-2 text-xs text-purple-600">Multiple, max 5MB / file. pdf, gambar, dokumen, zip</p>
                <?php if (!empty($attachments)): ?>
                    <ul class="mt-4 grid sm:grid-cols-2 gap-2">
                        <?php foreach($attachments as $att): ?>
                            <li>
                                <a class="group flex items-center gap-3 p-2 rounded-lg border border-gray-200 bg-white hover:border-purple-300 hover:bg-purple-50 transition text-xs" href="/classroom/attachment/<?= $att['id'] ?>/download">
                                    <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-gradient-to-br from-pink-500 to-purple-500 text-white shadow"><i class="fas fa-file text-[11px]"></i></span>
                                    <span class="flex-1 truncate group-hover:text-purple-700 font-medium"><?= esc($att['original_name']) ?></span>
                                    <i class="fas fa-download text-[10px] text-purple-400 group-hover:text-purple-600"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex justify-end pt-2">
            <button class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-2.5 text-sm font-medium text-white shadow hover:shadow-md"><i class="fas fa-paper-plane text-xs"></i> Kirim / Update</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
