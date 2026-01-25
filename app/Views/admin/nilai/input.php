<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php // Normalize selected kelas & mapel coming from controller or legacy query params fallback ?>
<?php $selectedKelas = $selectedKelas ?? ($_GET['kelas'] ?? null); $selectedMapel = $selectedMapel ?? ($_GET['mapel'] ?? null); ?>
<!-- Page Header - Mobile Optimized -->
<div class="mb-6 sm:mb-8 lg:mb-10 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:gap-6">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
                <i class="fas fa-edit text-green-600 mr-3 sm:mr-4 text-2xl sm:text-3xl lg:text-4xl"></i>Data Nilai Murid • Penilaian Harian
            </h1>
            <p class="text-base sm:text-lg text-gray-600">Pilih kelas dan mapel, lalu input nilai melalui modal</p>
        </div>
        
        <!-- Quick Actions - Mobile Optimized -->
    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <button 
                onclick="importFromExcel()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm sm:text-base touch-manipulation">
                <i class="fas fa-file-excel"></i>
                <span>Import Excel</span>
            </button>
        </div>
    </div>
</div>

<!-- Filter Section - Mobile Optimized -->
<div class="px-4 sm:px-6 lg:px-8 mb-6 sm:mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <input type="hidden" name="open" id="openModalFlag" value="0">
            <!-- Kelas Filter -->
            <div>
                <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                <select name="kelas" id="kelas" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">Pilih Kelas</option>
                    <?php if ($userRole === 'admin'): ?>
                        <?php foreach ($availableClasses as $class): ?>
                            <?php if (strtolower($class['kelas']) === 'lulus') continue; ?>
                            <option value="<?= $class['kelas'] ?>" <?= ($selectedKelas === $class['kelas']) ? 'selected' : '' ?>>
                                <?= $class['kelas'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php if ($userKelas && strtolower($userKelas) !== 'lulus'): ?>
                            <option value="<?= $userKelas ?>" selected><?= $userKelas ?></option>
                        <?php endif; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Mata Pelajaran Filter -->
            <div>
                <label for="mapel" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                <select name="mapel" id="mapel" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">Pilih Mapel</option>
                    <?php 
                        // Gunakan variabel yang sudah diproses controller agar slug / query tetap konsisten
                        $selMapel = $selectedMapel ?? ($_GET['mapel'] ?? '');
                    ?>
                    <?php if (!empty($subjectsDynamic)): ?>
                        <?php foreach ($subjectsDynamic as $subj): ?>
                            <?php $isSelected = strtolower($selMapel) === strtolower($subj); ?>
                            <option value="<?= esc($subj) ?>" <?= $isSelected ? 'selected' : '' ?>><?= esc($subj) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            

            <!-- Action Buttons -->
            <div class="sm:col-span-2 lg:col-span-2 flex flex-col sm:flex-row gap-2 sm:gap-3 sm:items-end">
                <button type="button" id="openInputHarian" onclick="handleOpenInputHarianClick()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl text-sm sm:text-base touch-manipulation">
                    <i class="fas fa-pen"></i>
                    <span>Input Penilaian Harian</span>
                </button>
                
                <a href="/admin/nilai/input" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl text-sm sm:text-base touch-manipulation">
                    <i class="fas fa-times"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Rekap Nilai Harian (PH) di bawah filter) -->
<div class="px-4 sm:px-6 lg:px-8 mb-8">
    <?php if (!$selectedKelas || !$selectedMapel): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <i class="fas fa-edit text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pilih Kelas dan Mata Pelajaran</h3>
            <p class="text-gray-600 mb-6">Gunakan filter di atas untuk memilih kelas dan mata pelajaran yang ingin dinilai</p>
        </div>
    <?php elseif (!empty($harianMatrix) && !empty($harianMatrix['students'])): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-x-auto">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Nilai Harian Tersimpan</h3>
                    <p class="text-sm text-gray-500">Kelas <?= esc($selectedKelas) ?> • Mapel <?= esc($selectedMapel) ?></p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openStudentModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">
                        <i class="fas fa-plus mr-2"></i>Input Nilai Harian
                    </button>
                    <button onclick="openEditModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">
                        <i class="fas fa-edit mr-2"></i>Edit Penilaian Harian
                    </button>
                </div>
            </div>
            <?php 
                // Render exactly as many PH columns as existing assessments
                $headers = $harianMatrix['headers'] ?? []; 
            ?>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NISN</th>
                        <?php foreach ($headers as $h): ?>
                            <th class="px-2 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider"><?= esc($h['label']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php $rowNo = 1; foreach ($harianMatrix['students'] as $stu): ?>
                        <tr>
                            <td class="px-4 sm:px-6 py-3 text-sm text-gray-500 w-12"><?= $rowNo++ ?></td>
                            <td class="px-4 sm:px-6 py-3 text-sm text-gray-900 font-medium"><?= esc($stu['nama']) ?></td>
                            <td class="px-4 sm:px-6 py-3 text-sm text-gray-500"><?= esc($stu['nisn']) ?></td>
                            <?php $vals = $harianMatrix['values'][$stu['id']] ?? []; ?>
                            <?php foreach ($headers as $idx => $h): ?>
                                <?php $col = $idx + 1; $v = $vals[$col] ?? ''; ?>
                                <td class="px-2 py-3 text-center text-sm <?= $v!=='' ? 'text-gray-900' : 'text-gray-300' ?>"><?= $v!=='' ? esc($v) : '-' ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <i class="fas fa-info-circle text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-600">Belum ada nilai harian tersimpan untuk pilihan ini.</p>
            <div class="mt-4">
                <button onclick="openStudentModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-semibold">
                    <i class="fas fa-plus mr-2"></i>Input Nilai Harian
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $hasSelection = $selectedKelas && $selectedMapel; ?>
<div id="studentInputModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Input Nilai Harian</h3>
                <?php if ($hasSelection): ?>
                    <p class="text-sm text-gray-500 mt-1">Kelas <?= esc($selectedKelas) ?> • Mapel <?= esc($selectedMapel) ?></p>
                <?php endif; ?>
            </div>
            <button onclick="closeStudentModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <!-- Form atas -->
            <form id="bulkInputForm">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2 grid grid-cols-5 gap-2">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis</label>
                            <input type="text" id="kode_prefix" value="PH" readonly class="w-full px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-700" />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor</label>
                            <select id="kode_number" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white"></select>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kode</label>
                            <input type="text" id="kode_penilaian" readonly class="w-full px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-700" value="Memuat...">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="deskripsi_penilaian" class="block text-sm font-semibold text-gray-700 mb-2">Topik Penilaian</label>
                        <textarea id="deskripsi_penilaian" name="deskripsi_penilaian" rows="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Contoh: Bab Pecahan / Tema 3 Subtema 1"></textarea>
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </form>

            <!-- Tabel Siswa -->
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Nilai (1-100)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($hasSelection && !empty($students)): ?>
                            <?php foreach ($students as $index => $student): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $index + 1 ?></td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= esc($student['nama']) ?></td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-center">
                                    <div class="space-y-1">
                                        <input type="number" min="0" max="100" step="1"
                                               data-siswa-id="<?= $student['id'] ?>"
                                               class="nilai-input w-24 px-2 py-1.5 text-center border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="0-100">
                                        <p class="nilai-error hidden text-xs text-red-600"></p>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-4 sm:px-6 py-8 text-center text-gray-500">Tidak ada siswa ditemukan atau belum memilih kelas & mapel</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <button onclick="saveAllGrades()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 flex-1">
                    <i class="fas fa-save"></i>
                    <span>Simpan Semua Nilai</span>
                </button>
                <button onclick="clearAllGrades()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center space-x-2 flex-1">
                    <i class="fas fa-eraser"></i>
                    <span>Hapus Semua</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editHarianModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Edit Penilaian Harian</h3>
                <?php if ($hasSelection): ?>
                    <p class="text-sm text-gray-500 mt-1">Kelas <?= esc($selectedKelas) ?> • Mapel <?= esc($selectedMapel) ?></p>
                <?php endif; ?>
            </div>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih PH</label>
                    <?php $headersJson = json_encode($harianMatrix['headers'] ?? []); ?>
                    <select id="editPhSelect" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Pilih Penilaian Harian</option>
                        <?php if (!empty($harianMatrix['headers'])): foreach ($harianMatrix['headers'] as $i => $h): ?>
                            <option value="<?= $i+1 ?>"><?= esc($h['label']) ?><?= $h['date'] ? ' - ' . esc($h['date']) : '' ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="editTanggal" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <input type="text" id="editDeskripsi" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl" placeholder="Deskripsi / TP">
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NISN</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($hasSelection && !empty($students)): ?>
                            <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td class="px-4 sm:px-6 py-3 text-sm text-gray-900"><?= $index + 1 ?></td>
                                <td class="px-4 sm:px-6 py-3 text-sm text-gray-600"><?= esc($student['nisn']) ?></td>
                                <td class="px-4 sm:px-6 py-3 text-sm text-gray-900 font-medium"><?= esc($student['nama']) ?></td>
                                <td class="px-4 sm:px-6 py-3 text-center">
                                    <div class="space-y-1">
                                        <input type="number" min="0" max="100" step="1" data-edit-siswa-id="<?= $student['id'] ?>" class="edit-nilai-input w-20 px-2 py-1.5 text-center border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="0-100">
                                        <p class="nilai-error hidden text-xs text-red-600"></p>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-4 sm:px-6 py-8 text-center text-gray-500">Tidak ada siswa</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap gap-3">
                <button onclick="saveEditedGrades()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">Simpan Perubahan</button>
                <button onclick="deleteSelectedAssessment()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold">Hapus Penilaian</button>
                <button onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// ===== SweetAlert2 Helpers =====
function showFlash(message, type='success', opts={}){
    const iconMap = { success:'success', error:'error', warning:'warning', info:'info' };
    Swal.fire({
        toast: true,
        position: opts.position || 'top-end',
        icon: iconMap[type] || 'success',
        title: message,
        showConfirmButton: false,
        timer: opts.ttl ?? 3000,
        timerProgressBar: true
    });
}

async function confirmDialog(message, {title='Konfirmasi', icon='question', confirmText='Ya', cancelText='Batal'}={}){
    const res = await Swal.fire({
        title: title,
        text: message,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        focusCancel: true,
        reverseButtons: true,
        customClass:{
            confirmButton:'bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-400 px-4 py-2 rounded-md text-sm font-semibold',
            cancelButton:'bg-gray-400 hover:bg-gray-500 focus:ring-2 focus:ring-gray-300 px-4 py-2 rounded-md text-sm font-semibold'
        },
        buttonsStyling:false
    });
    return res.isConfirmed;
}

// ===== Inline Validation =====
function validateNilaiInput(el){
    const raw = el.value.trim();
    const wrapperErr = el.parentElement?.querySelector?.('.nilai-error');
    let msg = '';
    if(raw===''){ msg=''; }
    else if(isNaN(raw)){ msg='Harus angka'; }
    else {
        const v = parseFloat(raw);
        if(v < 0) msg='Tidak boleh < 0';
        else if(v > 100) msg='Tidak boleh > 100';
    }
    if(msg){
        el.classList.add('border-red-500','ring-1','ring-red-400');
        if(wrapperErr){ wrapperErr.textContent = msg; wrapperErr.classList.remove('hidden'); }
    } else {
        el.classList.remove('border-red-500','ring-1','ring-red-400');
        if(wrapperErr){ wrapperErr.textContent=''; wrapperErr.classList.add('hidden'); }
    }
    return !msg; // valid if no msg
}

function validateAll(containerSelector){
    let ok = true;
    document.querySelectorAll(containerSelector).forEach(inp=>{ if(!validateNilaiInput(inp)) ok=false; });
    return ok;
}

function handleOpenInputHarianClick() {
    const kelas = document.getElementById('kelas')?.value || '';
    const mapel = document.getElementById('mapel')?.value || '';
    if (!kelas || !mapel) {
        showFlash('Pilih kelas dan mata pelajaran terlebih dahulu','error');
        return;
    }
    // If selection present, open the modal directly
    openStudentModal();
}

function openStudentModal() {
    const modal = document.getElementById('studentInputModal');
    if (modal) {
        modal.classList.remove('hidden');
    document.body.classList.add('modal-open');
        fetchNextKode();
    }
}

function closeStudentModal() {
    const modal = document.getElementById('studentInputModal');
    if (modal) {
        modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
    }
}

function importFromExcel() {
    showFlash('Fitur import Excel akan segera tersedia','info');
}

// Edit modal handlers
function openEditModal() {
    const modal = document.getElementById('editHarianModal');
    if (modal) {
        modal.classList.remove('hidden');
    document.body.classList.add('modal-open');
    }
}
function closeEditModal() {
    const modal = document.getElementById('editHarianModal');
    if (modal) {
        modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
    }
}

// Prefill edit values when PH selection changes
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'editPhSelect') {
        const colIndex = parseInt(e.target.value, 10); // 1-based
        if (!colIndex) return;
        try {
            const matrix = <?= json_encode($harianMatrix ?? []) ?>;
            const headers = matrix.headers || [];
            const students = matrix.students || [];
            const values = matrix.values || {};
            const header = headers[colIndex-1] || null;
            if (header) {
                document.getElementById('editTanggal').value = header.date || '';
                document.getElementById('editDeskripsi').value = header.tp || '';
            }
            // Set student values
            const inputs = document.querySelectorAll('input[data-edit-siswa-id]');
            inputs.forEach(inp => {
                const sid = parseInt(inp.getAttribute('data-edit-siswa-id'), 10);
                const rowVals = values[sid] || {};
                inp.value = (rowVals[colIndex] !== undefined && rowVals[colIndex] !== null) ? rowVals[colIndex] : '';
            });
        } catch (err) {
            console.error(err);
        }
    }
});

async function saveEditedGrades() {
    const phSel = document.getElementById('editPhSelect');
    const tanggal = document.getElementById('editTanggal').value;
    const deskripsi = document.getElementById('editDeskripsi').value.trim();
    const colIndex = parseInt(phSel.value || '0', 10);
    if (!colIndex) { showFlash('Pilih Penilaian Harian dulu','error'); return; }
    if (!tanggal) { showFlash('Tanggal wajib diisi','error'); return; }
    const kelas = '<?= esc($selectedKelas ?? '') ?>';
    const mapel = '<?= esc($selectedMapel ?? '') ?>';
    // Ambil kode_penilaian dari header matrix agar insert siswa baru tetap pada kode yang sama
    let kodePenilaian = null;{
        try { const matrix = <?= json_encode($harianMatrix ?? []) ?>; if(matrix.headers && matrix.headers[colIndex-1]) { kodePenilaian = matrix.headers[colIndex-1].label; } } catch(e){}
    }
    const grades = [];
    document.querySelectorAll('input[data-edit-siswa-id]').forEach(inp => {
        if (inp.value !== '' && inp.value !== null) {
            grades.push({ siswa_id: parseInt(inp.getAttribute('data-edit-siswa-id'), 10), nilai: parseFloat(inp.value) });
        }
    });
    if (!grades.length) { showFlash('Isi minimal satu nilai','error'); return; }
    if(!validateAll('#editHarianModal input.edit-nilai-input')){ showFlash('Perbaiki nilai tidak valid terlebih dahulu','error'); return; }
    const okEdit = await confirmDialog('Simpan perubahan untuk '+grades.length+' siswa?', {icon:'warning', confirmText:'Simpan'});
    if(!okEdit) return;
    try {
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        const resp = await fetch('<?= base_url('admin/nilai/update-bulk-harian') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            },
            body: JSON.stringify({
                [csrfName]: csrfHash,
                kelas,
                mapel,
                tanggal,
                deskripsi,
                colIndex,
                kode_penilaian: kodePenilaian,
                grades
            })
        });
        const data = await resp.json();
        if (resp.ok && data.status === 'ok') {
            closeEditModal();
            showFlash('Perubahan tersimpan','success');
            setTimeout(()=>{ window.location.href = buildPrettyNilaiUrl(kelas,mapel); },600);
        } else {
            showFlash(data.message || 'Gagal menyimpan perubahan','error');
        }
    } catch (err) {
        console.error(err);
        showFlash('Terjadi kesalahan saat menyimpan','error');
    }
}

// Update grade display function
function updateGrade(input, gradeId) {
    const value = parseInt(input.value);
    const gradeSpan = document.getElementById(gradeId);
    
    if (value >= 80) {
        gradeSpan.textContent = 'A';
        gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
    } else if (value >= 70) {
        gradeSpan.textContent = 'B';
        gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
    } else if (value >= 60) {
        gradeSpan.textContent = 'C';
        gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
    } else if (value > 0) {
        gradeSpan.textContent = 'D';
        gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800';
    } else {
        gradeSpan.textContent = '-';
        gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
    }
}

async function saveAllGrades() {
    // Validate fields
    const tanggalEl = document.getElementById('tanggal');
    const tanggal = tanggalEl ? tanggalEl.value : '';
    const deskripsi = (document.getElementById('deskripsi_penilaian') || { value: '' }).value.trim();
    if (!tanggal) { showFlash('Silakan pilih tanggal terlebih dahulu','error'); return; }
    
    // Collect all grades
    const grades = [];
    const inputs = document.querySelectorAll('input[type="number"][data-siswa-id]');
    inputs.forEach((input) => {
        if (input.value !== '' && input.value !== null) {
            grades.push({
                siswa_id: parseInt(input.getAttribute('data-siswa-id'), 10),
                nilai: parseFloat(input.value)
            });
        }
    });
    
    if (grades.length === 0) { showFlash('Silakan input minimal satu nilai','error'); return; }
    if(!validateAll('#studentInputModal input.nilai-input')){ showFlash('Perbaiki nilai tidak valid terlebih dahulu','error'); return; }
    
    const kelas = '<?= esc($selectedKelas ?? '') ?>';
    const mapel = '<?= esc($selectedMapel ?? '') ?>';
    const kode = (document.getElementById('kode_penilaian')?.value || '').trim();
    const kodeNum = (() => {
        const m = kode.match(/^(?:PH|PTS|PAS)-(\d+)$/i);
        return m ? parseInt(m[1], 10) : null;
    })();
    const usedNums = Array.isArray(window.USED_KODE_NUMBERS) ? window.USED_KODE_NUMBERS : [];
    const isUsedKode = (kodeNum !== null) ? usedNums.includes(kodeNum) : false;
    const confirmText = isUsedKode ? 'Simpan (Update)' : 'Simpan';
    const msg = isUsedKode
        ? `Kode ${kode} sudah terpakai. Ini akan MENGUBAH penilaian yang sudah ada. Lanjutkan?`
        : `Simpan ${grades.length} nilai harian untuk Kelas ${kelas} - ${mapel}?`;
    const okStore = await confirmDialog(msg, {icon: isUsedKode ? 'warning' : 'question', confirmText});
    if(!okStore) return;

    try {
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        const endpoint = isUsedKode ? '<?= base_url('admin/nilai/update-bulk-harian') ?>' : '<?= base_url('admin/nilai/store-bulk-harian') ?>';
        const resp = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            },
            body: JSON.stringify({
                [csrfName]: csrfHash,
                kelas,
                mapel,
                tanggal,
                deskripsi,
                kode_penilaian: kode,
                grades
            })
        });
        const data = await resp.json();
        if (resp.ok && data.status === 'ok') {
            closeStudentModal();
            const extraInfo = (data.inserted?` (${data.inserted} baris, Kode ${data.kode_penilaian||'-'})`: (data.updated?` (${data.updated} baris)`: ''));
            const baseMsg = isUsedKode ? 'Perubahan tersimpan' : (data.message || 'Nilai berhasil disimpan');
            showFlash(baseMsg + extraInfo,'success');
            setTimeout(()=>{ window.location.href = buildPrettyNilaiUrl(kelas,mapel); },800);
        } else {
            showFlash(data.message || 'Gagal menyimpan nilai','error');
        }
    } catch (err) {
        console.error(err);
        showFlash('Terjadi kesalahan saat menyimpan','error');
    }
}

async function clearAllGrades() {
    const ok = await confirmDialog('Hapus semua input nilai?', {icon:'warning', confirmText:'Hapus'});
    if(ok){
        document.querySelectorAll('input[type="number"]').forEach(input => { input.value=''; });
        document.querySelectorAll('[id^="grade-"]').forEach(span => { span.textContent='-'; span.className='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'; });
    }
}

// Auto-update grade display when value changes
document.addEventListener('input', function(e) {
    if (e.target.type === 'number') {
        const value = parseInt(e.target.value);
        const row = e.target.closest('tr');
        const gradeSpan = row.querySelector('[id^="grade-"]');
        
        if (value >= 80) {
            gradeSpan.textContent = 'A';
            gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
        } else if (value >= 70) {
            gradeSpan.textContent = 'B';
            gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
        } else if (value >= 60) {
            gradeSpan.textContent = 'C';
            gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
        } else if (value > 0) {
            gradeSpan.textContent = 'D';
            gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800';
        } else {
            gradeSpan.textContent = '-';
            gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
        }
    }
});

// Auto-open modal when kelas & mapel ada di query
document.addEventListener('DOMContentLoaded', function () {
    const hasSelection = <?= ($selectedKelas && $selectedMapel) ? 'true' : 'false' ?>;
    // If URL contains open=1 and selection exists, open input modal automatically
    const params = new URLSearchParams(window.location.search);
    if (hasSelection && params.get('open') === '1') {
        openStudentModal();
    }

    // Auto-submit when both filters selected
    const form = document.querySelector('form[method="GET"]');
    const kelasSel = document.getElementById('kelas');
    const mapelSel = document.getElementById('mapel');
    function trySubmit() {
        if (form && kelasSel && mapelSel && kelasSel.value && mapelSel.value) {
            window.location.href = buildPrettyNilaiUrl(kelasSel.value, mapelSel.value);
        }
    }
    if (kelasSel) kelasSel.addEventListener('change', trySubmit);
    if (mapelSel) mapelSel.addEventListener('change', trySubmit);
});

async function fetchNextKode(){
    const kelas = '<?= esc($selectedKelas ?? '') ?>';
    const mapel = '<?= esc($selectedMapel ?? '') ?>';
    const prefixSel = document.getElementById('kode_prefix');
    const numberSel = document.getElementById('kode_number');
    const kodeInput = document.getElementById('kode_penilaian');
    if(!kelas || !mapel){ if(kodeInput) kodeInput.value='-'; return; }
    const prefix = prefixSel.value;
    // Load used numbers to disable
    try {
        const usedResp = await fetch(`<?= base_url('admin/nilai/used-kode-harian') ?>?kelas=${encodeURIComponent(kelas)}&mapel=${encodeURIComponent(mapel)}&jenis=${prefix==='PH'?'harian':prefix.toLowerCase()}&prefix=${prefix}`);
        const usedData = await usedResp.json();
        const used = (usedData.status==='ok') ? usedData.used : [];
        window.USED_KODE_NUMBERS = Array.isArray(used) ? used : [];
        const usedMax = (Array.isArray(used) && used.length) ? Math.max(...used) : 0;
        const upper = Math.max(15, usedMax + 5);
        // Build number options 1..upper (allow selecting used numbers for edit/update)
        numberSel.innerHTML='';
        for(let i=1;i<=upper;i++){
            const opt=document.createElement('option');
            opt.value=i; opt.textContent=i;
            if(used.includes(i)){ opt.textContent = i + ' (terpakai)'; }
            numberSel.appendChild(opt);
        }
        // Auto select first available not used
        let selected = null;
        for(const o of numberSel.options){
            const n = parseInt(o.value,10);
            if(!used.includes(n)) { selected=o.value; break; }
        }
        if(selected){
            numberSel.value=selected;
        } else {
            // if everything up to upper is used (unlikely), default to next number
            numberSel.value = String(usedMax + 1);
        }
        updateKodeCombined();
    } catch(e){ if(kodeInput) kodeInput.value=prefix+'-?'; }
}

function updateKodeCombined(){
    const prefixSel = document.getElementById('kode_prefix');
    const numberSel = document.getElementById('kode_number');
    const kodeInput = document.getElementById('kode_penilaian');
    if(kodeInput){
        if(prefixSel.value && numberSel.value){
            kodeInput.value = prefixSel.value + '-' + numberSel.value;
        }
    }
}

document.addEventListener('change', function(e){
    if(e.target && e.target.id==='kode_number'){
        updateKodeCombined();
    }
});

async function deleteSelectedAssessment(){
    const kelas = '<?= esc($selectedKelas ?? '') ?>';
    const mapel = '<?= esc($selectedMapel ?? '') ?>';
    const phSel = document.getElementById('editPhSelect');
    if(!phSel || !phSel.value){ showFlash('Pilih PH dulu','error'); return; }
    const colIndex = parseInt(phSel.value,10);
    const matrix = <?= json_encode($harianMatrix ?? []) ?>;
    const headers = matrix.headers || [];
    const header = headers[colIndex-1] || null;
    const kode = header ? header.label : null;
    if(!kode){ showFlash('Tidak bisa ambil kode','error'); return; }
    const okDel = await confirmDialog('Hapus seluruh nilai untuk '+ kode +'?', {icon:'warning', confirmText:'Hapus'});
    if(!okDel) return;
    try {
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        const resp = await fetch('<?= base_url('admin/nilai/delete-harian') ?>', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrfHash},
            body: JSON.stringify({[csrfName]:csrfHash, kelas, mapel, kode_penilaian: kode})
        });
        const data = await resp.json();
        if(resp.ok && data.status==='ok'){
            closeEditModal();
            showFlash('Penilaian dihapus ('+ (data.deleted||0)+' baris)','success');
            setTimeout(()=>window.location.href = buildPrettyNilaiUrl(kelas,mapel),600);
        } else { showFlash(data.message || 'Gagal menghapus','error'); }
    } catch(err){ console.error(err); showFlash('Error menghapus','error'); }
}

// Close modal when clicking backdrop
document.getElementById('studentInputModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeStudentModal();
});

// Live validation listeners
document.addEventListener('input', function(e){
    if(e.target.classList.contains('nilai-input') || e.target.classList.contains('edit-nilai-input')){
        validateNilaiInput(e.target);
    }
});
// Pretty URL helpers
function slugify(str){ return str.toString().toLowerCase().trim().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,''); }
function buildPrettyNilaiUrl(kelas,mapel){ if(!kelas||!mapel) return '/admin/nilai/input'; return '/admin/nilai/input/'+ slugify(kelas) + '/' + slugify(mapel); }
</script>

<?= $this->endSection() ?>
