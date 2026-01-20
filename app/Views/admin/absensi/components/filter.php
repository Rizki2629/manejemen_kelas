<!-- Filter Component -->
<div class="mb-6 fade-in-up" style="animation-delay: 0.2s;">
    <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 backdrop-blur-sm">
        <form id="filterForm" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Kelas Filter -->
                <div class="space-y-2">
                    <label for="kelas" class="block text-gray-700 font-semibold text-sm">
                        <i class="fas fa-users mr-2 text-blue-500"></i>Kelas
                    </label>
                    <?php if ($userRole === 'admin'): ?>
                        <select 
                            id="kelas" 
                            name="kelas" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 font-medium hover:border-blue-300"
                            required
                        >
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($allKelas as $kelasItem): ?>
                                <option value="<?= esc($kelasItem['kelas']) ?>" 
                                        <?= $filterKelas === $kelasItem['kelas'] ? 'selected' : '' ?>>
                                    Kelas <?= esc($kelasItem['kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input 
                            type="text" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 shadow-sm font-medium" 
                            value="Kelas <?= esc($userKelas) ?>" 
                            readonly
                        >
                        <input type="hidden" name="kelas" value="<?= esc($userKelas) ?>">
                    <?php endif; ?>
                </div>

                <!-- Bulan Filter -->
                <div class="space-y-2">
                    <label for="bulan" class="block text-gray-700 font-semibold text-sm">
                        <i class="fas fa-calendar mr-2 text-green-500"></i>Bulan & Tahun
                    </label>
                    <input 
                        type="month" 
                        id="bulan" 
                        name="bulan" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300 font-medium hover:border-green-300"
                        value="<?= esc($filterBulan) ?>" 
                        required
                    >
                </div>

                <!-- Action Button -->
                <div class="space-y-2">
                    <label class="block text-transparent text-sm">Action</label>
                    <button 
                        type="submit" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-wide focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <i class="fas fa-search mr-2"></i>Tampilkan
                    </button>
                </div>

                <!-- Excel Export Button -->
                <div class="space-y-2">
                    <label class="block text-transparent text-sm">Export</label>
                    <button 
                        type="button" 
                        id="downloadExcel" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-wide disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        <?= empty($attendanceData) || empty($attendanceData['students']) ? 'disabled' : '' ?>
                    >
                        <i class="fas fa-file-excel mr-2"></i>Excel
                    </button>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="document.getElementById('bulan').value = '<?= date('Y-m') ?>'; document.getElementById('filterForm').submit();"
                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200"
                >
                    <i class="fas fa-calendar-day mr-1"></i>Bulan Ini
                </button>
                <button 
                    type="button" 
                    onclick="document.getElementById('bulan').value = '<?= date('Y-m', strtotime('-1 month')) ?>'; document.getElementById('filterForm').submit();"
                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200"
                >
                    <i class="fas fa-calendar-minus mr-1"></i>Bulan Lalu
                </button>
                <?php if ($userRole === 'admin'): ?>
                <button 
                    type="button" 
                    onclick="document.getElementById('kelas').selectedIndex = 0; document.getElementById('filterForm').submit();"
                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200"
                >
                    <i class="fas fa-refresh mr-1"></i>Reset
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
