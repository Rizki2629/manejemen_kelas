<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Data Guru</h1>
                    <p class="text-gray-600">Perbarui informasi data guru: <span class="font-semibold text-blue-600"><?= esc($guru['nama']); ?></span></p>
                </div>
                <a href="<?= base_url('admin/guru'); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <form action="<?= base_url('admin/guru/update/' . $guru['id']); ?>" method="post" class="space-y-8">
            <?= csrf_field(); ?>
            
            <!-- Progress Indicator -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between text-sm font-medium text-gray-500 mb-4">
                    <span class="flex items-center">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs mr-2">1</span>
                        Data Pribadi
                    </span>
                    <span class="flex items-center">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs mr-2">2</span>
                        Kepegawaian
                    </span>
                    <span class="flex items-center">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs mr-2">3</span>
                        Kontak & Alamat
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full w-full transition-all duration-300"></div>
                </div>
            </div>

            <!-- Data Pribadi Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Data Pribadi</h2>
                            <p class="text-blue-100 mt-1">Informasi dasar tentang guru</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="lg:col-span-2">
                            <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('nama')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                   id="nama" name="nama" value="<?= old('nama', $guru['nama']); ?>" required
                                   placeholder="Masukkan nama lengkap">
                            <?php if($validation->hasError('nama')): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <?= $validation->getError('nama'); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="jk" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('jk')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                    id="jk" name="jk" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" <?= old('jk', $guru['jk']) == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?= old('jk', $guru['jk']) == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                            <?php if($validation->hasError('jk')): ?>
                                <p class="mt-2 text-sm text-red-600"><?= $validation->getError('jk'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('tempat_lahir')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                   id="tempat_lahir" name="tempat_lahir" value="<?= old('tempat_lahir', $guru['tempat_lahir']); ?>" required
                                   placeholder="Contoh: Jakarta">
                            <?php if($validation->hasError('tempat_lahir')): ?>
                                <p class="mt-2 text-sm text-red-600"><?= $validation->getError('tempat_lahir'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('tanggal_lahir')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                   id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir', $guru['tanggal_lahir']); ?>" required>
                            <?php if($validation->hasError('tanggal_lahir')): ?>
                                <p class="mt-2 text-sm text-red-600"><?= $validation->getError('tanggal_lahir'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Agama -->
                        <div>
                            <label for="agama" class="block text-sm font-semibold text-gray-700 mb-2">Agama</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                    id="agama" name="agama">
                                <option value="">Pilih Agama</option>
                                <option value="Islam" <?= old('agama', $guru['agama']) == 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                <option value="Kristen" <?= old('agama', $guru['agama']) == 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                                <option value="Katolik" <?= old('agama', $guru['agama']) == 'Katolik' ? 'selected' : ''; ?>>Katolik</option>
                                <option value="Hindu" <?= old('agama', $guru['agama']) == 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                <option value="Buddha" <?= old('agama', $guru['agama']) == 'Buddha' ? 'selected' : ''; ?>>Buddha</option>
                                <option value="Konghucu" <?= old('agama', $guru['agama']) == 'Konghucu' ? 'selected' : ''; ?>>Konghucu</option>
                            </select>
                        </div>

                        <!-- NIK -->
                        <div class="lg:col-span-3">
                            <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">NIK</label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('nik')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                   id="nik" name="nik" value="<?= old('nik', $guru['nik']); ?>"
                                   placeholder="16 digit nomor induk kependudukan">
                            <?php if($validation->hasError('nik')): ?>
                                <p class="mt-2 text-sm text-red-600"><?= $validation->getError('nik'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Kepegawaian Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-8 py-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Data Kepegawaian</h2>
                            <p class="text-green-100 mt-1">Informasi status dan identitas kepegawaian</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- NIP -->
                        <div>
                            <label for="nip" class="block text-sm font-semibold text-gray-700 mb-2">NIP</label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('nip')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                   id="nip" name="nip" value="<?= old('nip', $guru['nip']); ?>"
                                   placeholder="Nomor Induk Pegawai">
                            <?php if($validation->hasError('nip')): ?>
                                <p class="mt-2 text-sm text-red-600"><?= $validation->getError('nip'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- NUPTK -->
                        <div>
                            <label for="nuptk" class="block text-sm font-semibold text-gray-700 mb-2">NUPTK</label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('nuptk')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                   id="nuptk" name="nuptk" value="<?= old('nuptk', $guru['nuptk']); ?>"
                                   placeholder="Nomor Unik Pendidik dan Tenaga Kependidikan">
                            <?php if($validation->hasError('nuptk')): ?>
                                <p class="mt-2 text-sm text-red-600"><?= $validation->getError('nuptk'); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Status Kepegawaian -->
                        <div>
                            <label for="status_kepegawaian" class="block text-sm font-semibold text-gray-700 mb-2">Status Kepegawaian</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                    id="status_kepegawaian" name="status_kepegawaian">
                                <option value="">Pilih Status</option>
                                <option value="PNS" <?= old('status_kepegawaian', $guru['status_kepegawaian']) == 'PNS' ? 'selected' : ''; ?>>PNS</option>
                                <option value="PPPK" <?= old('status_kepegawaian', $guru['status_kepegawaian']) == 'PPPK' ? 'selected' : ''; ?>>PPPK</option>
                                <option value="Honorer" <?= old('status_kepegawaian', $guru['status_kepegawaian']) == 'Honorer' ? 'selected' : ''; ?>>Honorer</option>
                                <option value="GTT" <?= old('status_kepegawaian', $guru['status_kepegawaian']) == 'GTT' ? 'selected' : ''; ?>>GTT</option>
                                <option value="GTY" <?= old('status_kepegawaian', $guru['status_kepegawaian']) == 'GTY' ? 'selected' : ''; ?>>GTY</option>
                            </select>
                        </div>

                        <!-- Jenis PTK -->
                        <div>
                            <label for="jenis_ptk" class="block text-sm font-semibold text-gray-700 mb-2">Jenis PTK</label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                   id="jenis_ptk" name="jenis_ptk" value="<?= old('jenis_ptk', $guru['jenis_ptk']); ?>"
                                   placeholder="Contoh: Guru, Kepala Sekolah">
                        </div>

                        <!-- Tugas Mengajar -->
                        <div class="lg:col-span-2">
                            <label for="tugas_mengajar" class="block text-sm font-semibold text-gray-700 mb-2">Tugas Mengajar</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                                    id="tugas_mengajar" name="tugas_mengajar">
                                <option value="">Pilih Kelas</option>
                                <?php if (isset($allKelas) && !empty($allKelas)): ?>
                                    <?php foreach ($allKelas as $kelas): ?>
                                        <?php 
                                        $isAssigned = in_array($kelas['kelas'], $assignedKelasNames ?? []);
                                        $isCurrentSelection = old('tugas_mengajar', $guru['tugas_mengajar']) == $kelas['kelas'];
                                        $selectedValue = $isCurrentSelection ? 'selected' : '';
                                        // Allow current selection even if assigned to show current value
                                        $isDisabled = $isAssigned && !$isCurrentSelection;
                                        ?>
                                        <option value="<?= esc($kelas['kelas']) ?>" 
                                                <?= $selectedValue ?> 
                                                <?= $isDisabled ? 'disabled' : '' ?>
                                                <?= $isDisabled ? 'class="text-gray-400 bg-gray-100"' : '' ?>>
                                            <?= esc($kelas['kelas']) ?>
                                            <?= $isDisabled ? ' - ' . esc($kelas['nama']) . ' (Tidak bisa dipilih)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <p class="mt-2 text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                Kelas yang sudah memiliki guru tidak dapat dipilih
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak & Alamat Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-8 py-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Kontak & Alamat</h2>
                            <p class="text-purple-100 mt-1">Informasi kontak dan tempat tinggal</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Kontak -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Informasi Kontak
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="telepon" class="block text-sm font-semibold text-gray-700 mb-2">Telepon</label>
                                <input type="text" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                       id="telepon" name="telepon" value="<?= old('telepon', $guru['telepon']); ?>"
                                       placeholder="Nomor telepon rumah">
                            </div>
                            <div>
                                <label for="hp" class="block text-sm font-semibold text-gray-700 mb-2">HP/WhatsApp</label>
                                <input type="text" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('hp')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                       id="hp" name="hp" value="<?= old('hp', $guru['hp']); ?>"
                                       placeholder="Nomor handphone/WhatsApp">
                                <?php if($validation->hasError('hp')): ?>
                                    <p class="mt-2 text-sm text-red-600"><?= $validation->getError('hp'); ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 <?= ($validation->hasError('email')) ? 'border-red-500 bg-red-50' : ''; ?>" 
                                       id="email" name="email" value="<?= old('email', $guru['email']); ?>"
                                       placeholder="alamat@email.com">
                                <?php if($validation->hasError('email')): ?>
                                    <p class="mt-2 text-sm text-red-600"><?= $validation->getError('email'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Alamat Tempat Tinggal
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <label for="alamat_jalan" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Jalan</label>
                                <textarea id="alamat_jalan" name="alamat_jalan" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                          placeholder="Jalan, nomor rumah, komplek, dll"><?= old('alamat_jalan', $guru['alamat_jalan']); ?></textarea>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="rt" class="block text-sm font-semibold text-gray-700 mb-2">RT</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="rt" name="rt" value="<?= old('rt', $guru['rt']); ?>"
                                           placeholder="001">
                                </div>
                                <div>
                                    <label for="rw" class="block text-sm font-semibold text-gray-700 mb-2">RW</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="rw" name="rw" value="<?= old('rw', $guru['rw']); ?>"
                                           placeholder="001">
                                </div>
                                <div>
                                    <label for="desa_kelurahan" class="block text-sm font-semibold text-gray-700 mb-2">Desa/Kelurahan</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="desa_kelurahan" name="desa_kelurahan" value="<?= old('desa_kelurahan', $guru['desa_kelurahan']); ?>"
                                           placeholder="Nama desa/kelurahan">
                                </div>
                                <div>
                                    <label for="kecamatan" class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="kecamatan" name="kecamatan" value="<?= old('kecamatan', $guru['kecamatan']); ?>"
                                           placeholder="Nama kecamatan">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Fields Toggle -->
                    <div class="mt-8">
                        <button type="button" onclick="toggleAdditionalFields()" 
                                class="w-full p-4 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl hover:from-gray-100 hover:to-gray-200 transition-all duration-200 flex items-center justify-center text-gray-700 font-medium">
                            <svg id="toggleIcon" class="w-5 h-5 mr-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span id="toggleText">Tampilkan</span> Field Tambahan
                        </button>
                        <div id="additionalFields" class="hidden mt-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="kode_pos" class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="kode_pos" name="kode_pos" value="<?= old('kode_pos', $guru['kode_pos']); ?>"
                                           placeholder="12345">
                                </div>
                                <div>
                                    <label for="pangkat_golongan" class="block text-sm font-semibold text-gray-700 mb-2">Pangkat Golongan</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="pangkat_golongan" name="pangkat_golongan" value="<?= old('pangkat_golongan', $guru['pangkat_golongan']); ?>"
                                           placeholder="Contoh: III/a">
                                </div>
                                <div>
                                    <label for="sumber_gaji" class="block text-sm font-semibold text-gray-700 mb-2">Sumber Gaji</label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" 
                                           id="sumber_gaji" name="sumber_gaji" value="<?= old('sumber_gaji', $guru['sumber_gaji']); ?>"
                                           placeholder="APBN/APBD/Swasta">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="<?= site_url('admin/guru') ?>" 
                   class="px-8 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 text-center font-medium flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl font-medium flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Data Guru
                </button>
            </div>
        </form>
    </div>

    <!-- Success Animation (hidden by default) -->
    <div id="successAnimation" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl p-8 text-center max-w-sm mx-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Data Berhasil Diupdate!</h3>
                <p class="text-gray-600">Data guru telah berhasil diperbarui.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle additional fields
function toggleAdditionalFields() {
    const fields = document.getElementById('additionalFields');
    const icon = document.getElementById('toggleIcon');
    const text = document.getElementById('toggleText');
    
    if (fields.classList.contains('hidden')) {
        fields.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
        text.textContent = 'Sembunyikan';
    } else {
        fields.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
        text.textContent = 'Tampilkan';
    }
}

// Form submission with loading state
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const btnText = submitBtn.querySelector('span') || submitBtn;
    const btnIcon = submitBtn.querySelector('svg');
    
    // Add loading state
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75');
    btnIcon.style.animation = 'spin 1s linear infinite';
    btnText.textContent = 'Menyimpan...';
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Smooth scroll behavior for form sections
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Form focus animations */
.form-input:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions for all interactive elements */
* {
    transition: all 0.2s ease-in-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?= $this->endSection() ?>
