<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
    <h1 class="text-3xl font-bold mb-6">Edit Berita</h1>

    <form action="/admin/berita/update/<?= esc($berita['id']) ?>" method="post" class="max-w-lg bg-white p-6 rounded shadow">
        <div class="mb-4">
            <label for="judul" class="block font-semibold mb-1">Judul</label>
            <input type="text" id="judul" name="judul" value="<?= esc($berita['judul']) ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="isi" class="block font-semibold mb-1">Isi</label>
            <textarea id="isi" name="isi" rows="10" class="w-full border border-gray-300 rounded px-3 py-2" required><?= esc($berita['isi']) ?></textarea>
        </div>

        <div class="mb-4">
            <label for="tanggal" class="block font-semibold mb-1">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="<?= esc($berita['tanggal']) ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="gambar" class="block font-semibold mb-1">URL Gambar</label>
            <input type="text" id="gambar" name="gambar" value="<?= esc($berita['gambar']) ?>" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
        <a href="/admin/berita" class="ml-4 text-gray-600 hover:underline">Batal</a>
    </form>

    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#isi',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                      alignleft aligncenter alignright alignjustify | \
                      bullist numlist outdent indent | removeformat | help',
            readonly: false
        });
    </script>
<?= $this->endSection() ?>
