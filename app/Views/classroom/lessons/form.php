<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-3xl">
    <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 mb-6">Tambah Materi Pembelajaran</h2>
    <?php if (session('error')): ?><div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><i class="fas fa-circle-exclamation mt-0.5"></i><div class="whitespace-pre-line"><?= session('error') ?></div></div><?php endif; ?>
    <form method="post" action="/classroom/lessons/store" enctype="multipart/form-data" class="space-y-6">
        <div class="grid sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Kelas</label>
                <?php if(!empty($autoKelas)): ?>
                    <input type="text" value="<?= esc($autoKelas) ?>" class="w-full rounded-lg border border-purple-200 bg-gray-100 px-3 py-2 text-sm" readonly>
                    <input type="hidden" name="kelas" value="<?= esc($autoKelas) ?>">
                <?php else: ?>
                    <input type="text" name="kelas" value="<?= old('kelas') ?>" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Mata Pelajaran</label>
                <?php if(!empty($mapelList)): ?>
                <select name="mapel" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0">
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($mapelList as $kode=>$nama): ?>
                        <option value="<?= esc(is_string($kode)?$kode:$nama) ?>" <?= old('mapel')===$kode || old('mapel')===$nama ? 'selected' : '' ?>><?= esc($nama) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                    <input type="text" name="mapel" value="<?= old('mapel') ?>" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" placeholder="Mapel">
                <?php endif; ?>
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Judul</label>
            <input type="text" name="judul" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required>
        </div>
        <!-- Ringkasan dihapus sesuai spesifikasi baru -->
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Kegiatan Pembelajaran</label>
            <textarea name="konten" rows="8" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 font-mono text-sm focus:border-purple-400 focus:ring-0"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Link Video (opsional)</label>
            <input type="url" name="video_url" value="<?= old('video_url') ?>" placeholder="https://..." class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0">
            <p class="text-[11px] text-gray-400 mt-1">Tempel URL YouTube / Drive / lainnya.</p>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Lampiran</label>
            <div class="mt-1 rounded-xl border border-dashed border-purple-300 bg-gradient-to-r from-purple-50 to-pink-50 p-4">
                <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-purple-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-purple-500" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt" />
                <p class="mt-2 text-xs text-purple-600">Multiple, max 5MB / file. pdf, gambar, dokumen, ppt, xls, txt</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button name="action" value="draft" class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white shadow hover:bg-gray-600"><i class="fas fa-save text-xs"></i> Draft</button>
            <button name="action" value="publish" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 px-5 py-2.5 text-sm font-medium text-white shadow hover:shadow-md"><i class="fas fa-rocket text-xs"></i> Publish</button>
            <a href="/classroom/lessons" class="ml-auto text-sm font-medium text-gray-500 hover:text-gray-700">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
