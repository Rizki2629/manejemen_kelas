<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<style>
/* Custom styles for class promotion checkboxes */
.class-checkbox:checked + label {
    color: #2563eb;
    font-weight: 600;
}

.class-checkbox:checked ~ .text-center {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border-radius: 0.5rem;
    padding: 0.5rem;
}

[data-class-item]:has(.class-checkbox:checked) {
    border-color: #3b82f6 !important;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.validation-loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Preview animation */
#selectedPromotionPreview {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<!-- Page Header -->
<div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Naik Kelas Per Rombel</h1>
                <p class="text-gray-600">Kelola kenaikan kelas siswa secara otomatis per rombongan belajar</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900"><?= number_format($totalSiswa) ?></h3>
                    <p class="text-gray-500 text-sm">Total Siswa</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900"><?= count($kelasData) ?></h3>
                    <p class="text-gray-500 text-sm">Total Rombel</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Class Distribution with Promotion Checkboxes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Distribusi Kelas Saat Ini</h3>
            <div class="flex space-x-2">
                <button type="button" id="selectAllBtn" class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg transition-colors duration-200">
                    Pilih Semua
                </button>
                <button type="button" id="deselectAllBtn" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg transition-colors duration-200">
                    Hapus Pilihan
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <?php foreach ($kelasData as $index => $kelas): 
                $nextClass = '';
                $isGraduating = false;
                
                // Determine next class
                if (preg_match('/Kelas (\d+) ([A-Z])/i', $kelas['kelas'], $matches)) {
                    $level = intval($matches[1]);
                    $section = $matches[2];
                    if ($level >= 6) {
                        $nextClass = 'Lulus';
                        $isGraduating = true;
                    } else {
                        $nextClass = "Kelas " . ($level + 1) . " " . $section;
                    }
                } elseif (preg_match('/^(\d+) ([A-Z])$/i', $kelas['kelas'], $matches)) {
                    $level = intval($matches[1]);
                    $section = $matches[2];
                    if ($level >= 6) {
                        $nextClass = 'Lulus';
                        $isGraduating = true;
                    } else {
                        $nextClass = ($level + 1) . " " . $section;
                    }
                }
            ?>
            <div class="bg-gray-50 rounded-lg p-4 border-2 border-transparent hover:border-blue-200 transition-all duration-200" 
                 data-class-item="<?= esc($kelas['kelas']) ?>">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" 
                               id="class_checkbox_<?= $index ?>" 
                               name="selected_classes[]" 
                               value="<?= esc($kelas['kelas']) ?>"
                               data-from-class="<?= esc($kelas['kelas']) ?>"
                               data-to-class="<?= esc($nextClass) ?>"
                               data-is-graduating="<?= $isGraduating ? 'true' : 'false' ?>"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 class-checkbox">
                        <label for="class_checkbox_<?= $index ?>" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Naik Kelas
                        </label>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-900"><?= esc($kelas['kelas']) ?></div>
                    <div class="text-sm text-gray-500 mb-2"><?= number_format($kelas['jumlah']) ?> siswa</div>
                    
                    <?php if ($nextClass): ?>
                    <div class="text-xs text-blue-600 bg-blue-50 rounded px-2 py-1">
                        <?php if ($isGraduating): ?>
                            🎓 → <?= esc($nextClass) ?>
                        <?php else: ?>
                            📚 → <?= esc($nextClass) ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div id="validation_<?= $index ?>" class="mt-2 text-xs hidden"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="border-t pt-4">
            <div class="flex items-center justify-between mb-4">
                <div id="selectedClassesInfo" class="text-sm text-gray-600">
                    Pilih kelas yang akan dinaikkan menggunakan checkbox di atas
                </div>
                <button type="button" id="executeSelectedPromotions" 
                        class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200" 
                        disabled>
                    Naik Kelas Terpilih
                </button>
            </div>
            
            <div id="selectedPromotionPreview" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-medium text-yellow-900 mb-2">Preview Naik Kelas Terpilih:</h4>
                <div id="previewList" class="space-y-1 text-sm text-yellow-800"></div>
                <div class="mt-3 text-xs text-yellow-700">
                    ⚠ Pastikan semua kelas tujuan dalam kondisi kosong sebelum melanjutkan.
                </div>
            </div>
        </div>
    </div>

    <!-- Single Class Promotion -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Naik Kelas Individual</h3>
        <form id="singlePromotionForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="from_kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas Asal</label>
                <select id="from_kelas" name="from_kelas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Kelas Asal</option>
                    <?php foreach ($kelasData as $kelas): ?>
                        <option value="<?= esc($kelas['kelas']) ?>"><?= esc($kelas['kelas']) ?> (<?= $kelas['jumlah'] ?> siswa)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="to_kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas Tujuan</label>
                <input type="text" id="to_kelas" name="to_kelas" placeholder="Contoh: Kelas 2 A" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required readonly>
                <div id="to_kelas_validation" class="mt-1 text-sm hidden"></div>
            </div>
            
            <div class="flex items-end">
                <button type="button" id="previewBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Preview
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Section -->
    <div id="previewSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6" style="display: none;">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview Naik Kelas</h3>
        <div id="previewContent"></div>
        <div class="mt-4 flex space-x-3">
            <button type="button" id="executeBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Eksekusi Naik Kelas
            </button>
            <button type="button" id="cancelBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Batal
            </button>
        </div>
    </div>

    <!-- Batch Class Promotion -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Naik Kelas Batch (Otomatis)</h3>
        <p class="text-gray-600 mb-4">Sistem akan otomatis menaikkan kelas berdasarkan pola standar:</p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h4 class="font-medium text-blue-900 mb-2">Pola Kenaikan Otomatis:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-800">
                <div>• Kelas 1 A → Kelas 2 A</div>
                <div>• Kelas 1 B → Kelas 2 B</div>
                <div>• Kelas 2 A → Kelas 3 A</div>
                <div>• Kelas 2 B → Kelas 3 B</div>
                <div>• Dan seterusnya...</div>
                <div>• Kelas 6 (Lulus/Arsip)</div>
            </div>
        </div>

        <div class="flex space-x-3">
            <button type="button" id="batchPromotionBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Eksekusi Naik Kelas Batch Otomatis
            </button>
            <button type="button" id="graduateClass6Btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                Luluskan Semua Kelas 6
            </button>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" style="display: none;">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Eksekusi</h3>
        <div id="resultsContent"></div>
    </div>
</div>

<!-- JavaScript for Auto-execution -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('previewBtn');
    const executeBtn = document.getElementById('executeBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const batchPromotionBtn = document.getElementById('batchPromotionBtn');
    const graduateClass6Btn = document.getElementById('graduateClass6Btn');
    const previewSection = document.getElementById('previewSection');
    const resultsSection = document.getElementById('resultsSection');

    // Auto-preview on form change
    const fromKelas = document.getElementById('from_kelas');
    const toKelas = document.getElementById('to_kelas');
    
    // Checkbox functionality for class distribution
    const classCheckboxes = document.querySelectorAll('.class-checkbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const executeSelectedPromotions = document.getElementById('executeSelectedPromotions');
    const selectedClassesInfo = document.getElementById('selectedClassesInfo');
    const selectedPromotionPreview = document.getElementById('selectedPromotionPreview');
    const previewList = document.getElementById('previewList');
    
    // Select All functionality
    selectAllBtn.addEventListener('click', function() {
        selectAllBtn.disabled = true;
        selectAllBtn.textContent = 'Memvalidasi...';
        
        const promises = [];
        
        classCheckboxes.forEach((checkbox, index) => {
            // Only auto-select non-graduating classes
            if (!checkbox.dataset.isGraduating || checkbox.dataset.isGraduating === 'false') {
                checkbox.checked = true;
                promises.push(validateSingleClass(checkbox, parseInt(checkbox.id.split('_')[2])));
            }
        });
        
        Promise.all(promises).then(() => {
            updateSelectedClassesInfo();
            validateSelectedClasses();
            selectAllBtn.disabled = false;
            selectAllBtn.textContent = 'Pilih Semua';
        });
    });
    
    // Deselect All functionality
    deselectAllBtn.addEventListener('click', function() {
        classCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedClassesInfo();
        executeSelectedPromotions.disabled = true;
        selectedPromotionPreview.classList.add('hidden');
    });
    
    // Individual checkbox change
    classCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const checkboxIndex = parseInt(this.id.split('_')[2]);
            
            if (this.checked) {
                validateSingleClass(this, checkboxIndex).then((isValid) => {
                    updateSelectedClassesInfo();
                    validateSelectedClasses();
                });
            } else {
                // Clear validation for unchecked
                const validationDiv = document.getElementById(`validation_${checkboxIndex}`);
                validationDiv.classList.add('hidden');
                updateSelectedClassesInfo();
                validateSelectedClasses();
            }
        });
    });
    
    function updateSelectedClassesInfo() {
        const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count === 0) {
            selectedClassesInfo.textContent = 'Pilih kelas yang akan dinaikkan menggunakan checkbox di atas';
            executeSelectedPromotions.disabled = true;
            selectedPromotionPreview.classList.add('hidden');
        } else {
            selectedClassesInfo.textContent = `${count} kelas dipilih untuk naik kelas`;
            executeSelectedPromotions.disabled = false;
            
            // Show preview
            updatePromotionPreview(checkedBoxes);
        }
    }
    
    function updatePromotionPreview(checkedBoxes) {
        previewList.innerHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const fromClass = checkbox.dataset.fromClass;
            const toClass = checkbox.dataset.toClass;
            const isGraduating = checkbox.dataset.isGraduating === 'true';
            
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between bg-white rounded px-3 py-2';
            div.innerHTML = `
                <span class="font-medium">${fromClass}</span>
                <span class="text-gray-500">→</span>
                <span class="font-medium ${isGraduating ? 'text-red-600' : 'text-blue-600'}">${toClass}</span>
                ${isGraduating ? '<span class="text-xs text-red-500">🎓</span>' : '<span class="text-xs text-blue-500">📚</span>'}
            `;
            previewList.appendChild(div);
        });
        
        selectedPromotionPreview.classList.remove('hidden');
    }
    
    function validateSingleClass(checkbox, index) {
        const toClass = checkbox.dataset.toClass;
        const validationDiv = document.getElementById(`validation_${index}`);
        
        // Skip validation for graduation
        if (checkbox.dataset.isGraduating === 'true') {
            validationDiv.innerHTML = '<span class="text-green-600">✓ Siap untuk lulus</span>';
            validationDiv.classList.remove('hidden', 'text-red-600');
            validationDiv.classList.add('text-green-600');
            return Promise.resolve(true);
        }
        
        // Show loading state
        validationDiv.innerHTML = '<span class="text-blue-600 validation-loading">🔄 Memvalidasi...</span>';
        validationDiv.classList.remove('hidden', 'text-red-600', 'text-green-600');
        validationDiv.classList.add('text-blue-600');
        
        // Check if target class is empty
        return fetch('/admin/naik-kelas/check-target-class', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `target_class=${encodeURIComponent(toClass)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.isEmpty) {
                    validationDiv.innerHTML = '<span class="text-green-600">✓ Kelas tujuan kosong</span>';
                    validationDiv.classList.remove('text-red-600', 'text-blue-600');
                    validationDiv.classList.add('text-green-600');
                    return true;
                } else {
                    validationDiv.innerHTML = `<span class="text-red-600">✗ ${data.message}</span>`;
                    validationDiv.classList.remove('text-green-600', 'text-blue-600');
                    validationDiv.classList.add('text-red-600');
                    checkbox.checked = false; // Uncheck if validation fails
                    updateSelectedClassesInfo();
                    return false;
                }
            }
            return false;
        })
        .catch(error => {
            console.error('Validation error:', error);
            validationDiv.innerHTML = '<span class="text-red-600">✗ Error validasi</span>';
            validationDiv.classList.remove('text-green-600', 'text-blue-600');
            validationDiv.classList.add('text-red-600');
            checkbox.checked = false;
            updateSelectedClassesInfo();
            return false;
        });
    }
    
    function validateSelectedClasses() {
        const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');
        let allValid = true;
        
        checkedBoxes.forEach((checkbox, idx) => {
            const index = checkbox.id.split('_')[2];
            const validationDiv = document.getElementById(`validation_${index}`);
            
            if (validationDiv.classList.contains('text-red-600')) {
                allValid = false;
            }
        });
        
        executeSelectedPromotions.disabled = !allValid || checkedBoxes.length === 0;
    }
    
    // Execute selected promotions
    executeSelectedPromotions.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu kelas untuk dinaikkan');
            return;
        }
        
        const mappings = [];
        checkedBoxes.forEach(checkbox => {
            mappings.push({
                from: checkbox.dataset.fromClass,
                to: checkbox.dataset.toClass
            });
        });
        
        const confirmMsg = `Apakah Anda yakin ingin menaikkan ${checkedBoxes.length} kelas?\n\nKelas yang akan dinaikkan:\n` + 
                          mappings.map(m => `- ${m.from} → ${m.to}`).join('\n');
        
        if (!confirm(confirmMsg)) {
            return;
        }
        
        executeSelectedPromotions.disabled = true;
        executeSelectedPromotions.textContent = 'Memproses...';
        
        fetch('/admin/naik-kelas/batch-naik-kelas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                mappings: mappings,
                confirm: 'yes'
            })
        })
        .then(response => response.json())
        .then(data => {
            executeSelectedPromotions.disabled = false;
            executeSelectedPromotions.textContent = 'Naik Kelas Terpilih';
            
            if (data.success) {
                let resultsHtml = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-green-900 mb-2">✅ Naik Kelas Terpilih Berhasil!</h4>
                        <p class="text-green-800">${data.message}</p>
                    </div>
                    <div class="space-y-2">
                `;
                
                data.results.forEach(result => {
                    resultsHtml += `
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <span class="font-medium">${result.from}</span> → 
                            <span class="font-medium text-blue-600">${result.to}</span>
                            <span class="text-sm text-gray-600">(${result.affected} siswa)</span>
                        </div>
                    `;
                });
                
                resultsHtml += '</div>';
                
                document.getElementById('resultsContent').innerHTML = resultsHtml;
                resultsSection.style.display = 'block';
                
                // Clear checkboxes and reset
                classCheckboxes.forEach(checkbox => checkbox.checked = false);
                updateSelectedClassesInfo();
                
                // Auto-reload page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            executeSelectedPromotions.disabled = false;
            executeSelectedPromotions.textContent = 'Naik Kelas Terpilih';
            console.error('Error:', error);
            alert('Terjadi kesalahan saat eksekusi naik kelas terpilih');
        });
    });
    
    // Cancel functionality
    cancelBtn.addEventListener('click', function() {
        previewSection.style.display = 'none';
    });

    // Batch promotion functionality
    batchPromotionBtn.addEventListener('click', function() {
        // Generate automatic mappings first
        const mappings = generateAutomaticMappings();
        
        if (mappings.length === 0) {
            alert('Tidak ada mapping kelas yang dapat dibuat secara otomatis. Pastikan format kelas adalah "Kelas X Y".');
            return;
        }
        
        // Show confirmation with details
        let confirmMsg = 'Apakah Anda yakin ingin melaksanakan naik kelas batch otomatis?\n\nMapping yang akan dijalankan:\n';
        mappings.forEach(mapping => {
            confirmMsg += `- ${mapping.from} → ${mapping.to}\n`;
        });
        
        if (!confirm(confirmMsg)) {
            return;
        }

        batchPromotionBtn.disabled = true;
        batchPromotionBtn.textContent = 'Memproses Batch...';

        fetch('/admin/naik-kelas/batch-naik-kelas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                mappings: mappings,
                confirm: 'yes'
            })
        })
        .then(response => response.json())
        .then(data => {
            batchPromotionBtn.disabled = false;
            batchPromotionBtn.textContent = 'Eksekusi Naik Kelas Batch Otomatis';
            
            if (data.success) {
                let resultsHtml = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-green-900 mb-2">✅ Batch Naik Kelas Berhasil!</h4>
                        <p class="text-green-800">${data.message}</p>
                    </div>
                    <div class="space-y-2">
                `;
                
                data.results.forEach(result => {
                    resultsHtml += `
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <span class="font-medium">${result.from}</span> → 
                            <span class="font-medium text-blue-600">${result.to}</span>
                            <span class="text-sm text-gray-600">(${result.affected} siswa)</span>
                        </div>
                    `;
                });
                
                resultsHtml += '</div>';
                
                document.getElementById('resultsContent').innerHTML = resultsHtml;
                resultsSection.style.display = 'block';
                
                // Auto-reload page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            batchPromotionBtn.disabled = false;
            batchPromotionBtn.textContent = 'Eksekusi Naik Kelas Batch Otomatis';
            console.error('Error:', error);
            alert('Terjadi kesalahan saat eksekusi batch');
        });
    });

    // Graduate Class 6 functionality
    graduateClass6Btn.addEventListener('click', function() {
        if (!confirm('Apakah Anda yakin ingin meluluskan SEMUA siswa kelas 6?\n\nSemua siswa kelas 6 akan dipindahkan ke status "Lulus". Aksi ini tidak dapat dibatalkan!')) {
            return;
        }

        graduateClass6Btn.disabled = true;
        graduateClass6Btn.textContent = 'Memproses Lulusan...';

        fetch('/admin/naik-kelas/graduate-class-6', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                confirm: 'yes'
            })
        })
        .then(response => response.json())
        .then(data => {
            graduateClass6Btn.disabled = false;
            graduateClass6Btn.textContent = 'Luluskan Semua Kelas 6';
            
            if (data.success) {
                let resultsHtml = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-green-900 mb-2">🎓 Kelulusan Berhasil!</h4>
                        <p class="text-green-800">${data.message}</p>
                    </div>
                `;
                
                document.getElementById('resultsContent').innerHTML = resultsHtml;
                resultsSection.style.display = 'block';
                
                // Auto-reload page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            graduateClass6Btn.disabled = false;
            graduateClass6Btn.textContent = 'Luluskan Semua Kelas 6';
            console.error('Error:', error);
            alert('Terjadi kesalahan saat proses kelulusan');
        });
    });

    function generateAutomaticMappings() {
        const mappings = [];
        const kelasData = <?= json_encode($kelasData) ?>;
        
        kelasData.forEach(kelas => {
            const currentClass = kelas.kelas;
            const nextClass = getNextClass(currentClass);
            
            if (nextClass) {
                mappings.push({
                    from: currentClass,
                    to: nextClass
                });
            }
        });
        
        return mappings;
    }

    function getNextClass(currentClass) {
        // Try different patterns
        
        // Pattern 1: "Kelas X Y" (e.g., "Kelas 1 A")
        let match = currentClass.match(/Kelas (\d+) ([A-Z])/i);
        if (match) {
            const level = parseInt(match[1]);
            const section = match[2];
            
            // If level 6, graduate (or return null for no promotion)
            if (level >= 6) {
                return null; // Graduates
            }
            
            // Promote to next level
            return `Kelas ${level + 1} ${section}`;
        }
        
        // Pattern 2: "X Y" (e.g., "1 A")
        match = currentClass.match(/^(\d+) ([A-Z])$/i);
        if (match) {
            const level = parseInt(match[1]);
            const section = match[2];
            
            if (level >= 6) {
                return null; // Graduates
            }
            
            return `${level + 1} ${section}`;
        }
        
        // Pattern 3: "XY" (e.g., "1A")
        match = currentClass.match(/^(\d+)([A-Z])$/i);
        if (match) {
            const level = parseInt(match[1]);
            const section = match[2];
            
            if (level >= 6) {
                return null; // Graduates
            }
            
            return `${level + 1}${section}`;
        }
        
        // Pattern 4: Just number "X" (e.g., "1")
        match = currentClass.match(/^(\d+)$/);
        if (match) {
            const level = parseInt(match[1]);
            
            if (level >= 6) {
                return null; // Graduates
            }
            
            return `${level + 1}`;
        }
        
        // If no pattern matches, return null
        return null;
    }
});
</script>

<?= $this->endSection() ?>
