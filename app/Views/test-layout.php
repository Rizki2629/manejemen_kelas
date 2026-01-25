<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Test Layout - SDN Grogol Utara 09<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="text-center">
            <div class="mb-6">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Layout Admin Berhasil!</h1>
                <p class="text-lg text-gray-600">File layout/admin.php telah dibuat dan berfungsi dengan baik.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-blue-100 rounded-lg p-4">
                    <i class="fas fa-school text-2xl text-blue-600 mb-2"></i>
                    <h3 class="font-semibold text-gray-800">SDN Grogol Utara 09</h3>
                    <p class="text-sm text-gray-600">Sekolah Dasar</p>
                </div>
                
                <div class="bg-green-100 rounded-lg p-4">
                    <i class="fas fa-users text-2xl text-green-600 mb-2"></i>
                    <h3 class="font-semibold text-gray-800">Kelola User</h3>
                    <p class="text-sm text-gray-600">Manajemen Pengguna</p>
                </div>
                
                <div class="bg-purple-100 rounded-lg p-4">
                    <i class="fas fa-cogs text-2xl text-purple-600 mb-2"></i>
                    <h3 class="font-semibold text-gray-800">Sistem</h3>
                    <p class="text-sm text-gray-600">Aplikasi Pengelolaan</p>
                </div>
            </div>
            
            <div class="flex justify-center space-x-4">
                <a href="<?= base_url('admin/users') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-users mr-2"></i>
                    Ke Halaman Kelola User
                </a>
                
                <a href="<?= base_url('admin/dashboard') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Ke Dashboard
                </a>
            </div>
            
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-gray-800 mb-2">Status Sistem:</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-green-600">✓</span> Layout Admin: <strong>Aktif</strong>
                    </div>
                    <div>
                        <span class="text-green-600">✓</span> Tailwind CSS: <strong>Loaded</strong>
                    </div>
                    <div>
                        <span class="text-green-600">✓</span> Font Awesome: <strong>Loaded</strong>
                    </div>
                    <div>
                        <span class="text-green-600">✓</span> CodeIgniter 4: <strong>Running</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
