<?= $this->extend('admin/layout'); ?>

<?= $this->section('content'); ?>

<div class="max-w-7xl mx-auto p-6 space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Header Info -->
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold"><?= esc($guru['nama']); ?></h1>
                    <p class="text-white/90 text-lg"><?= esc($guru['jenis_ptk'] ?? 'Guru'); ?></p>
                    <div class="flex items-center gap-4 mt-2">
                        <?php if ($guru['nip']): ?>
                            <span class="bg-white/15 backdrop-blur-sm rounded-lg px-3 py-1 text-sm">
                                <i class="fas fa-id-card mr-1"></i>
                                NIP: <?= esc($guru['nip']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($guru['tugas_mengajar']): ?>
                            <span class="bg-white/15 backdrop-blur-sm rounded-lg px-3 py-1 text-sm">
                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                <?= esc($guru['tugas_mengajar']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <a href="<?= base_url('admin/guru/edit/' . $guru['id']); ?>" 
                   class="bg-yellow-500 hover:bg-yellow-600 px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit Data
                </a>
                <a href="<?= base_url('admin/guru'); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Data Pribadi -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Data Pribadi
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-signature text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nama Lengkap</p>
                                    <p class="text-gray-900 font-semibold"><?= esc($guru['nama']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-venus-mars text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Jenis Kelamin</p>
                                    <?php if ($guru['jk'] == 'L'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-mars mr-1"></i> Laki-laki
                                        </span>
                                    <?php elseif ($guru['jk'] == 'P'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-800">
                                            <i class="fas fa-venus mr-1"></i> Perempuan
                                        </span>
                                    <?php else: ?>
                                        <p class="text-gray-400">-</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tempat Lahir</p>
                                    <p class="text-gray-900"><?= esc($guru['tempat_lahir']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-calendar text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tanggal Lahir</p>
                                    <p class="text-gray-900"><?= $guru['tanggal_lahir'] ? date('d F Y', strtotime($guru['tanggal_lahir'])) : '-'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-pray text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Agama</p>
                                    <p class="text-gray-900"><?= esc($guru['agama']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-id-card text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">NIK</p>
                                    <p class="text-gray-900 font-mono"><?= esc($guru['nik']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">No. KK</p>
                                    <p class="text-gray-900 font-mono"><?= esc($guru['no_kk']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-flag text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Kewarganegaraan</p>
                                    <p class="text-gray-900"><?= esc($guru['kewarganegaraan']) ?: 'Indonesia'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Kepegawaian -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-briefcase text-green-600"></i>
                        Data Kepegawaian
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-id-badge text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">NIP</p>
                                    <p class="text-gray-900 font-mono"><?= esc($guru['nip']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-certificate text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">NUPTK</p>
                                    <p class="text-gray-900 font-mono"><?= esc($guru['nuptk']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-user-tie text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status Kepegawaian</p>
                                    <?php if ($guru['status_kepegawaian']): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <?= esc($guru['status_kepegawaian']); ?>
                                        </span>
                                    <?php else: ?>
                                        <p class="text-gray-400">-</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-chalkboard-teacher text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tugas Mengajar</p>
                                    <?php if ($guru['tugas_mengajar']): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            <?= esc($guru['tugas_mengajar']); ?>
                                        </span>
                                    <?php else: ?>
                                        <p class="text-gray-400">-</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-medal text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Pangkat/Golongan</p>
                                    <p class="text-gray-900"><?= esc($guru['pangkat_golongan']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Sumber Gaji</p>
                                    <p class="text-gray-900"><?= esc($guru['sumber_gaji']) ?: '-'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-home text-purple-600"></i>
                        Alamat Lengkap
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-road text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Alamat Jalan</p>
                                <p class="text-gray-900"><?= esc($guru['alamat_jalan']) ?: '-'; ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-start gap-2">
                                <div class="w-6 h-6 bg-purple-100 rounded flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-hashtag text-purple-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">RT</p>
                                    <p class="text-gray-900 text-sm"><?= esc($guru['rt']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <div class="w-6 h-6 bg-purple-100 rounded flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-hashtag text-purple-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">RW</p>
                                    <p class="text-gray-900 text-sm"><?= esc($guru['rw']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <div class="w-6 h-6 bg-purple-100 rounded flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-map-pin text-purple-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Kode Pos</p>
                                    <p class="text-gray-900 text-sm"><?= esc($guru['kode_pos']) ?: '-'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-building text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Desa/Kelurahan</p>
                                    <p class="text-gray-900"><?= esc($guru['desa_kelurahan']) ?: '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-city text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Kecamatan</p>
                                    <p class="text-gray-900"><?= esc($guru['kecamatan']) ?: '-'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Contact & Additional Info -->
        <div class="space-y-6">
            
            <!-- Contact Info -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-phone text-orange-600"></i>
                        Kontak
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-phone text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Telepon</p>
                            <p class="text-gray-900"><?= esc($guru['telepon']) ?: '-'; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">HP/WhatsApp</p>
                            <p class="text-gray-900"><?= esc($guru['hp']) ?: '-'; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900 break-all"><?= esc($guru['email']) ?: '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-info-circle text-teal-600"></i>
                        Status
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Status Keaktifan</span>
                        <?php if ($guru['status_keaktifan']): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                <?= esc($guru['status_keaktifan']); ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($guru['tmt_pns']): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">TMT PNS</span>
                        <span class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($guru['tmt_pns'])); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($guru['tmt_pengangkatan']): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">TMT Pengangkatan</span>
                        <span class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($guru['tmt_pengangkatan'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bank Info -->
            <?php if ($guru['bank'] || $guru['nomor_rekening']): ?>
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-50 to-green-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-university text-emerald-600"></i>
                        Informasi Bank
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building-columns text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Bank</p>
                            <p class="text-gray-900"><?= esc($guru['bank']) ?: '-'; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-credit-card text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nomor Rekening</p>
                            <p class="text-gray-900 font-mono"><?= esc($guru['nomor_rekening']) ?: '-'; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Atas Nama</p>
                            <p class="text-gray-900"><?= esc($guru['rekening_atas_nama']) ?: '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Family Info -->
            <?php if ($guru['nama_suami_istri'] || $guru['nip_suami_istri'] || $guru['pekerjaan_suami_istri']): ?>
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-heart text-pink-600"></i>
                        Data Keluarga
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <?php if ($guru['nama_suami_istri']): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-friends text-pink-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nama Suami/Istri</p>
                            <p class="text-gray-900"><?= esc($guru['nama_suami_istri']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($guru['nip_suami_istri']): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-id-badge text-pink-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">NIP Suami/Istri</p>
                            <p class="text-gray-900 font-mono"><?= esc($guru['nip_suami_istri']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($guru['pekerjaan_suami_istri']): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-pink-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pekerjaan</p>
                            <p class="text-gray-900"><?= esc($guru['pekerjaan_suami_istri']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
