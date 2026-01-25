<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="min-h-screen bg-gray-50 p-4 lg:p-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="rounded-2xl p-6 text-white shadow-2xl" style="background: linear-gradient(135deg, #4f46e5, #06b6d4);">
            <div class="text-center">
                <h1 class="text-2xl lg:text-3xl font-bold mb-2 drop-shadow-lg">
                    📈 Persentase Kehadiran Per Kelas
                </h1>
                <p class="text-sm lg:text-base opacity-90">
                    Bulan <?= strtoupper($bulan_nama ?? '') ?> <?= $tahun ?? '' ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-6">
        <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-school mr-2 text-indigo-600"></i>Kelas
                    </label>
                    <?php if ($userRole === 'admin'): ?>
                        <select id="kelas" name="kelas" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($allKelas as $kelasItem): ?>
                                <?php
                                $displayText = (strpos($kelasItem['kelas'], 'Kelas') === 0)
                                    ? $kelasItem['kelas']
                                    : 'Kelas ' . $kelasItem['kelas'];
                                ?>
                                <option value="<?= $kelasItem['kelas'] ?>" <?= $filterKelas === $kelasItem['kelas'] ? 'selected' : '' ?>>
                                    <?= $displayText ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <?php
                        $displayUserKelas = (strpos($userKelas, 'Kelas') === 0)
                            ? $userKelas
                            : 'Kelas ' . $userKelas;
                        ?>
                        <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100" value="<?= $displayUserKelas ?>" readonly>
                        <input type="hidden" name="kelas" value="<?= $userKelas ?>">
                    <?php endif; ?>
                </div>

                <div>
                    <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>Bulan & Tahun
                    </label>
                    <input type="month" id="bulan" name="bulan" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500" value="<?= $filterBulan ?>" required>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                        <i class="fas fa-search mr-2"></i>Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Section -->
    <?php if (!empty($classAttendancePercentages)): ?>
        <div x-data="{ tab: 'presentase' }" class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 p-4">
                <button type="button"
                    class="px-4 py-2 rounded-lg text-sm font-semibold"
                    :class="tab === 'presentase' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    @click="tab = 'presentase'">
                    <i class="fas fa-table mr-2"></i>Presentase
                </button>
                <button type="button"
                    class="px-4 py-2 rounded-lg text-sm font-semibold"
                    :class="tab === 'grafik' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    @click="tab = 'grafik'; $nextTick(() => window.initAttendanceChart && window.initAttendanceChart());">
                    <i class="fas fa-chart-column mr-2"></i>Grafik
                </button>
            </div>

            <!-- Tab: Presentase (Table) -->
            <div x-show="tab === 'presentase'" class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-indigo-50 text-indigo-800">
                                <th class="px-4 py-3 text-left font-semibold">Kelas</th>
                                <th class="px-4 py-3 text-center font-semibold">Jumlah Siswa</th>
                                <th class="px-4 py-3 text-center font-semibold">Hari Efektif</th>
                                <th class="px-4 py-3 text-center font-semibold">Total Hadir</th>
                                <th class="px-4 py-3 text-center font-semibold">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classAttendancePercentages as $row): ?>
                                <tr class="border-t border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800"><?= esc($row['kelas']) ?></td>
                                    <td class="px-4 py-3 text-center text-gray-700"><?= esc($row['total_students']) ?></td>
                                    <td class="px-4 py-3 text-center text-gray-700"><?= esc($row['effective_days']) ?></td>
                                    <td class="px-4 py-3 text-center text-gray-700"><?= esc($row['total_hadir']) ?></td>
                                    <td class="px-4 py-3 text-center font-semibold text-indigo-700"><?= number_format($row['percentage'], 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Grafik -->
            <div x-show="tab === 'grafik'" class="p-6" x-cloak>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <h2 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-chart-column mr-2 text-indigo-500"></i>
                        Grafik Persentase Kehadiran
                    </h2>
                    <div class="flex items-center gap-3">
                        <label for="chartType" class="text-xs font-semibold text-gray-600">Tipe Grafik</label>
                        <select id="chartType" class="text-xs px-2 py-1 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                            <option value="bar" selected>Batang</option>
                            <option value="pie">Pie</option>
                            <option value="line">Line</option>
                        </select>
                        <div class="text-xs text-gray-500">Satuan: %</div>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="attendanceChart" aria-label="Grafik persentase kehadiran per kelas" role="img"></canvas>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="max-w-md mx-auto">
                <i class="fas fa-info-circle text-5xl text-gray-400 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500">Tidak ada data kehadiran untuk filter yang dipilih.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($classAttendancePercentages)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            const chartData = <?= json_encode(array_map(function ($row) {
                                    return [
                                        'kelas' => $row['kelas'],
                                        'percentage' => round((float)$row['percentage'], 1)
                                    ];
                                }, $classAttendancePercentages), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

            const labels = chartData.map(item => item.kelas.replace(/^Kelas\s+/i, ''));
            const values = chartData.map(item => item.percentage);

            window.initAttendanceChart = function() {
                const chartEl = document.getElementById('attendanceChart');
                if (!chartEl || !chartEl.getContext) return;

                const chartTypeSelect = document.getElementById('chartType');
                const chartType = chartTypeSelect ? chartTypeSelect.value : 'bar';

                if (window.attendanceChartInstance) {
                    window.attendanceChartInstance.destroy();
                }

                const palette = [
                    '#4f46e5', '#06b6d4', '#22c55e', '#f59e0b', '#ef4444',
                    '#8b5cf6', '#14b8a6', '#f97316', '#0ea5e9', '#6366f1'
                ];

                const backgroundColors = labels.map((_, i) => {
                    const base = palette[i % palette.length];
                    return chartType === 'line' ? base : `${base}99`;
                });

                const borderColors = labels.map((_, i) => palette[i % palette.length]);

                const ctx = chartEl.getContext('2d');
                window.attendanceChartInstance = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels,
                        datasets: [{
                            label: 'Persentase Kehadiran',
                            data: values,
                            backgroundColor: backgroundColors,
                            borderColor: chartType === 'line' ? borderColors : borderColors,
                            borderWidth: chartType === 'line' ? 2 : 1,
                            borderRadius: chartType === 'bar' ? 8 : 0,
                            maxBarThickness: chartType === 'bar' ? 36 : undefined,
                            pointRadius: chartType === 'line' ? 4 : 0,
                            pointHoverRadius: chartType === 'line' ? 6 : 0,
                            tension: chartType === 'line' ? 0.35 : 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: chartType === 'pie'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const value = chartType === 'pie' ? ctx.parsed : ctx.parsed.y;
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        scales: chartType === 'pie' ? {} : {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Persentase (%)'
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: false
                                }
                            }
                        }
                    }
                });
            };

            document.addEventListener('change', function(event) {
                if (event.target && event.target.id === 'chartType') {
                    window.initAttendanceChart();
                }
            });
        })();
    </script>
<?php endif; ?>

<?= $this->endSection() ?>