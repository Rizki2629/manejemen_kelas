<!-- No Data Component -->
<div class="bg-white rounded-xl shadow-lg p-8 text-center fade-in-up">
    <div class="max-w-md mx-auto">
        <!-- Icon -->
        <div class="mb-6">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">
                Belum Ada Data Absensi
            </h3>
            <p class="text-gray-500 leading-relaxed">
                <?php if (!$filterKelas): ?>
                    Silakan pilih kelas dan periode untuk menampilkan data absensi siswa.
                <?php else: ?>
                    Tidak ada data absensi untuk kelas <strong><?= esc($filterKelas) ?></strong> 
                    pada bulan <strong><?= isset($filterBulan) ? date('F Y', strtotime($filterBulan . '-01')) : date('F Y') ?></strong>.
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="space-y-3">
            <button 
                onclick="document.getElementById('kelas')?.focus() || document.getElementById('bulan')?.focus()" 
                class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                <i class="fas fa-search mr-2"></i>Pilih Data untuk Ditampilkan
            </button>
            
            <button 
                onclick="window.location.href='<?= base_url('admin/absensi/input') ?>'"
                class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            >
                <i class="fas fa-plus mr-2"></i>Input Data Absensi
            </button>
        </div>
        
        <!-- Help Text -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h4 class="font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>Informasi
            </h4>
            <ul class="text-sm text-blue-700 text-left space-y-1">
                <li>• Pastikan sudah memilih kelas dan bulan yang benar</li>
                <li>• Data absensi harus diinput terlebih dahulu</li>
                <li>• Hubungi admin jika ada masalah dengan data</li>
            </ul>
        </div>
    </div>
</div>
