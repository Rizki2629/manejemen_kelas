<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
    <h1 class="text-3xl font-bold mb-6">Tambah Berita</h1>

    <form action="/admin/berita/store" method="post" class="w-full mx-auto bg-white rounded-lg shadow-lg p-8 flex flex-col space-y-6" enctype="multipart/form-data" id="beritaForm" style="max-width: 100vw; max-height: 100vh;">
        <div>
            <label for="judul" class="block text-gray-700 font-semibold mb-2">Judul</label>
            <input type="text" id="judul" name="judul" required
                class="w-full max-w-full rounded-md border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
        </div>

        <div>
            <label for="isi" class="block text-gray-700 font-semibold mb-2">Isi</label>
            <textarea id="isi" name="isi" rows="20" class="w-full max-w-full border border-gray-300 rounded-md px-4 py-2" required></textarea>
        </div>

        <div class="relative w-48">
            <label for="tanggal" class="block text-gray-700 font-semibold mb-2">Tanggal</label>
            <div class="flex items-center border border-gray-300 rounded-md px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent transition">
                <input type="text" id="tanggal" name="tanggal" placeholder="YYYY-MM-DD" readonly
                    class="w-full bg-transparent focus:outline-none cursor-pointer" required />
                <button type="button" id="calendarBtn" class="ml-2 text-gray-500 hover:text-blue-600 focus:outline-none" aria-label="Pilih tanggal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Upload Gambar</label>
            <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center cursor-pointer hover:border-blue-500 transition">
                <p class="text-gray-500 mb-2">Drag & drop gambar di sini, atau klik untuk memilih file</p>
                <p class="text-sm text-gray-400">Hanya JPG, PNG. Maks 2MB.</p>
                <input type="file" id="gambar_upload" name="gambar_upload[]" accept="image/jpeg,image/png" multiple class="hidden" />
                <div id="preview" class="mt-4 flex flex-wrap gap-4 justify-center"></div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
            <a href="/admin/berita" class="px-6 py-2 border border-gray-400 rounded-md text-gray-700 hover:bg-gray-100 transition">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Simpan Berita</button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />

    <script>
        // Initialize flatpickr date picker
        const tanggalInput = document.getElementById('tanggal');
        const calendarBtn = document.getElementById('calendarBtn');
        const fp = flatpickr(tanggalInput, {
            dateFormat: "Y-m-d",
            allowInput: true,
            clickOpens: false,
        });
        calendarBtn.addEventListener('click', () => {
            fp.open();
        });

        // Dropzone functionality
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('gambar_upload');
        const preview = document.getElementById('preview');
        let files = [];

        function updatePreview() {
            preview.innerHTML = '';
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative w-24 h-24 rounded overflow-hidden border border-gray-300';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}" class="object-cover w-full h-full" />
                        <button type="button" data-index="${index}" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 focus:outline-none" aria-label="Hapus gambar">
                            &times;
                        </button>
                    `;
                    preview.appendChild(div);

                    div.querySelector('button').addEventListener('click', (ev) => {
                        const idx = ev.target.getAttribute('data-index');
                        files.splice(idx, 1);
                        updatePreview();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        dropzone.addEventListener('click', () => {
            fileInput.click();
        });

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500');
        });

        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500');
            const droppedFiles = Array.from(e.dataTransfer.files);
            droppedFiles.forEach(file => {
                if ((file.type === 'image/jpeg' || file.type === 'image/png') && file.size <= 2 * 1024 * 1024) {
                    files.push(file);
                } else {
                    alert('File harus JPG atau PNG dan maksimal 2MB.');
                }
            });
            updatePreview();
        });

        fileInput.addEventListener('change', (e) => {
            const selectedFiles = Array.from(e.target.files);
            selectedFiles.forEach(file => {
                if ((file.type === 'image/jpeg' || file.type === 'image/png') && file.size <= 2 * 1024 * 1024) {
                    files.push(file);
                } else {
                    alert('File harus JPG atau PNG dan maksimal 2MB.');
                }
            });
            updatePreview();
            fileInput.value = '';
        });

        // On form submit, append files to FormData
        document.getElementById('beritaForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Append files from dropzone
            files.forEach((file, index) => {
                formData.append('gambar_upload[]', file);
            });

            fetch(this.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '/admin/berita';
                } else {
                    alert('Gagal menyimpan berita.');
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan saat mengirim data.');
            });
        });
<?= $this->endSection() ?>
