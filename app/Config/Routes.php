<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('/test', 'Test::index');

// Buku Kasus Routes
$routes->get('/buku-kasus', 'BukuKasus::index');
$routes->get('/buku-kasus/tambah', 'BukuKasus::tambah');
$routes->post('/buku-kasus/simpan', 'BukuKasus::simpan');
$routes->get('/buku-kasus/detail/(:num)', 'BukuKasus::detail/$1');
$routes->get('/buku-kasus/edit/(:num)', 'BukuKasus::edit/$1');
$routes->post('/buku-kasus/update/(:num)', 'BukuKasus::update/$1');
$routes->get('/buku-kasus/hapus/(:num)', 'BukuKasus::hapus/$1');
$routes->post('/buku-kasus/get-siswa-by-kelas', 'BukuKasus::getSiswaByKelas');
$routes->get('/buku-kasus/cetak/(:num)', 'BukuKasus::cetak/$1');

// Test route tanpa database
$routes->get('/test-rekap', function () {
    return view('test-index');
});

// Test layout route
$routes->get('/test-layout', function () {
    return view('test-layout');
});

// Test login route
$routes->get('/test-login', function () {
    return '<h1>Test Login Page</h1><p>This is a simple test page</p>';
});

// Login test routes
$routes->get('/login-test', 'LoginTest::index');
$routes->post('/login-test', 'LoginTest::authenticate');

// Test route for holiday integration
$routes->get('/test-holiday-integration', 'TestController::holidayIntegration');

// Enhanced Absensi Public Access (for demo/testing)
$routes->get('/rekap-enhanced', 'Admin\AbsensiEnhanced::rekap');
$routes->get('/rekap-clean', 'Admin\AbsensiEnhanced::rekapClean');
$routes->get('/rekap-enhanced/export-excel', 'Admin\AbsensiEnhanced::exportExcel');

$routes->get('/login', 'Login::index');
$routes->post('/login', 'Login::authenticate');

// Absensi Mobile (Public - tanpa login)
$routes->get('absensi-mobile', 'AbsensiMobileController::index');
$routes->get('absensi-mobile/lookup', 'AbsensiMobileController::lookup');
$routes->get('absensi-mobile/reverse-geocode', 'AbsensiMobileController::reverseGeocode');
$routes->post('absensi-mobile/submit', 'AbsensiMobileController::submit');

// Rekap Absensi Mobile (Public - untuk monitoring)
$routes->get('rekap-absen-mobile', 'AbsensiMobileController::rekap');
$routes->get('rekap-absen-mobile/photo/(:num)', 'AbsensiMobileController::photo/$1');
$routes->post('rekap-absen-mobile/delete/(:num)', 'AbsensiMobileController::delete/$1');

// Admin Routes Group
$routes->group('admin', function ($routes) {
    // Default admin route - redirect to dashboard
    $routes->get('/', 'Admin\Dashboard::index');

    // Main Dashboard
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('dashboard/refresh', 'Admin\Dashboard::refresh');

    // Data Siswa Routes
    $routes->get('data-siswa', 'Admin\DataSiswa::index');
    $routes->get('data-siswa/detail/(:num)', 'Admin\DataSiswa::detail/$1');
    $routes->get('data-siswa/export', 'Admin\DataSiswa::export');
    $routes->get('data-siswa/export-excel', 'Admin\DataSiswa::exportExcel');
    $routes->get('data-siswa/create', 'Admin\DataSiswa::create');
    $routes->post('data-siswa/store', 'Admin\DataSiswa::store');
    $routes->get('data-siswa/edit/(:num)', 'Admin\DataSiswa::edit/$1');
    $routes->post('data-siswa/update/(:num)', 'Admin\DataSiswa::update/$1');
    $routes->post('data-siswa/delete/(:num)', 'Admin\DataSiswa::delete/$1');
    $routes->get('data-siswa/view/(:num)', 'Admin\DataSiswa::view/$1');

    // Profil Sekolah Routes
    $routes->get('profil-sekolah', 'Admin\ProfilSekolah::index');
    $routes->post('profil-sekolah/save', 'Admin\ProfilSekolah::save');

    // User Management Routes
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/create', 'Admin\Users::create');
    $routes->post('users/store', 'Admin\Users::store');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\Users::delete/$1');
    $routes->post('users/toggle-status/(:num)', 'Admin\Users::toggleStatus/$1');
    $routes->post('users/reset-password/(:num)', 'Admin\Users::resetPassword/$1');
    $routes->post('users/generate-accounts', 'Admin\Users::generateAccounts');
    // Split generate actions
    $routes->post('users/generate-walikelas', 'Admin\Users::generateWalikelasAccounts');
    $routes->post('users/generate-siswa', 'Admin\Users::generateSiswaAccounts');

    // Profile Routes
    $routes->get('profile', 'Admin\Profile::index');
    $routes->get('profile/edit', 'Admin\Profile::edit');
    $routes->post('profile/update', 'Admin\Profile::update');
    $routes->post('profile/change-password', 'Admin\Profile::changePassword');
    $routes->post('profile/upload-avatar', 'Admin\Profile::uploadAvatar');

    // Settings Routes
    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/update', 'Admin\Settings::update');
    $routes->post('settings/update-school-info', 'Admin\Settings::updateSchoolInfo');
    $routes->post('settings/update-system', 'Admin\Settings::updateSystem');

    // Berita Routes (existing - keeping for backward compatibility)
    $routes->get('berita', 'Admin\Berita::index');
    $routes->get('berita/create', 'Admin\Berita::create');
    $routes->post('berita/store', 'Admin\Berita::store');
    $routes->get('berita/edit/(:num)', 'Admin\Berita::edit/$1');
    $routes->post('berita/update/(:num)', 'Admin\Berita::update/$1');
    $routes->post('berita/delete/(:num)', 'Admin\Berita::delete/$1');
    $routes->post('berita/upload-image', 'Admin\Berita::uploadImage');

    // Naik Kelas Routes
    $routes->get('naik-kelas', 'Admin\NaikKelas::index');
    $routes->post('naik-kelas/preview', 'Admin\NaikKelas::preview');
    $routes->post('naik-kelas/execute', 'Admin\NaikKelas::execute');
    $routes->post('naik-kelas/batch-naik-kelas', 'Admin\NaikKelas::batchNaikKelas');
    $routes->post('naik-kelas/graduate-class-6', 'Admin\NaikKelas::graduateClass6');
    $routes->post('naik-kelas/check-target-class', 'Admin\NaikKelas::checkTargetClass');

    // Kalender Akademik Routes
    $routes->get('kalender-akademik', 'Admin\KalenderAkademik::index');
    $routes->post('kalender-akademik/get-events-by-date', 'Admin\KalenderAkademik::getEventsByDate');
    $routes->post('kalender-akademik/save-event', 'Admin\KalenderAkademik::saveEvent');
    $routes->post('kalender-akademik/update-event', 'Admin\KalenderAkademik::updateEvent');
    $routes->post('kalender-akademik/delete-event', 'Admin\KalenderAkademik::deleteEvent');

    // Nilai Routes
    $routes->get('nilai', 'Admin\Nilai::index');
    $routes->get('nilai/data-tp', 'Admin\Nilai::dataTP');
    // Pretty URL: /admin/nilai/input/{kelasSlug}/{mapelSlug}
    $routes->get('nilai/input/(:segment)/(:segment)', 'Admin\Nilai::inputNilai/$1/$2');
    $routes->get('nilai/input', 'Admin\Nilai::inputNilai');
    $routes->get('nilai/cetak', 'Admin\Nilai::cetakNilai');
    $routes->get('nilai/export-excel', 'Admin\Nilai::exportExcel');
    $routes->post('nilai/store-bulk-harian', 'Admin\Nilai::storeBulkHarian');
    $routes->post('nilai/update-bulk-harian', 'Admin\Nilai::updateBulkHarian');
    $routes->get('nilai/next-kode-harian', 'Admin\Nilai::nextKodeHarian');
    $routes->get('nilai/used-kode-harian', 'Admin\Nilai::usedKodeHarian');
    $routes->post('nilai/delete-harian', 'Admin\Nilai::deleteHarianAssessment');
    // PTS & PAS pages
    $routes->get('nilai/pts', 'Admin\Nilai::pts');
    $routes->get('nilai/pas', 'Admin\Nilai::pas');
    $routes->post('nilai/store-bulk-exam', 'Admin\Nilai::storeBulkExam');
    $routes->get('nilai/create', 'Admin\Nilai::create');
    $routes->post('nilai/store', 'Admin\Nilai::store');
    $routes->get('nilai/detail/(:num)', 'Admin\Nilai::detail/$1');
    $routes->get('nilai/edit/(:num)', 'Admin\Nilai::edit/$1');
    $routes->post('nilai/update/(:num)', 'Admin\Nilai::update/$1');
    $routes->post('nilai/delete/(:num)', 'Admin\Nilai::delete/$1');

    // Absensi Routes
    $routes->get('absensi/input', 'Admin\Absensi::input');
    $routes->post('absensi/input', 'Admin\Absensi::input'); // For AJAX requests
    $routes->get('absensi/rekap', 'Admin\Absensi::rekap');
    $routes->get('absensi/persentase', 'Admin\Absensi::persentaseKelas');
    $routes->get('absensi/rekap-test', 'Admin\AbsensiTest::rekap'); // Testing route tanpa database

    // Enhanced Absensi with real Excel export
    $routes->get('absensi-enhanced/rekap', 'Admin\AbsensiEnhanced::rekap');
    $routes->get('absensi-enhanced/rekap-clean', 'Admin\AbsensiEnhanced::rekapClean');
    $routes->get('absensi-enhanced/export-excel', 'Admin\AbsensiEnhanced::exportExcel');

    $routes->post('absensi/save', 'Admin\Absensi::save');
    $routes->post('absensi/save_all', 'Admin\Absensi::save_all');
    $routes->post('absensi/getDetailData', 'Admin\Absensi::getDetailData');
    $routes->get('absensi/getSummary', 'Admin\Absensi::getSummary');
    $routes->get('absensi/export', 'Admin\Absensi::export');

    // Data Guru Routes
    $routes->get('guru', 'Admin\Guru::index');
    $routes->get('guru/detail/(:num)', 'Admin\Guru::detail/$1');
    $routes->get('guru/create', 'Admin\Guru::create');
    $routes->post('guru/store', 'Admin\Guru::store');
    $routes->get('guru/edit/(:num)', 'Admin\Guru::edit/$1');
    $routes->post('guru/update/(:num)', 'Admin\Guru::update/$1');
    $routes->get('guru/delete/(:num)', 'Admin\Guru::delete/$1');
    $routes->get('guru/import', 'Admin\Guru::import');
    $routes->get('guru/check-duplicates', 'Admin\Guru::checkDuplicates');
    $routes->post('guru/clean-duplicates', 'Admin\Guru::cleanDuplicates');

    // 7 Kebiasaan - Monthly Recap (Admin)
    $routes->get('habits/monthly', 'Admin\HabitMonthlyController::index');
    $routes->get('habits/monthly/students/(:num)', 'Admin\HabitMonthlyController::students/$1');
    $routes->get('habits/monthly/data', 'Admin\HabitMonthlyController::data');
    $routes->get('habits/monthly/export', 'Admin\HabitMonthlyController::export');
    $routes->get('habits/monthly/export-pdf', 'Admin\HabitMonthlyController::exportPdf');

    // Mapel (Subjects) Management
    $routes->get('mapel', 'Admin\MapelController::index');
    $routes->get('mapel/json', 'Admin\MapelController::json'); // list for AJAX duplicate disable
    $routes->post('mapel/store', 'Admin\MapelController::store');
    $routes->get('mapel/edit/(:num)', 'Admin\MapelController::edit/$1');
    $routes->post('mapel/update/(:num)', 'Admin\MapelController::update/$1');
    $routes->post('mapel/delete/(:num)', 'Admin\MapelController::delete/$1');
});

// Walikelas Routes Group (without /admin prefix)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Dashboard untuk walikelas
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Data Siswa (view only untuk walikelas)
    $routes->get('data-siswa', 'Admin\DataSiswa::index');
    $routes->get('data-siswa/detail/(:num)', 'Admin\DataSiswa::detail/$1');
    $routes->get('data-siswa/view/(:num)', 'Admin\DataSiswa::view/$1');

    // Daftar Hadir
    $routes->get('daftar-hadir', 'Admin\Absensi::input');
    $routes->get('absensi/input', 'Admin\Absensi::input');
    $routes->post('absensi/input', 'Admin\Absensi::input');
    $routes->get('absensi/rekap', 'Admin\Absensi::rekap');
    $routes->get('absensi/persentase', 'Admin\Absensi::persentaseKelas');
    $routes->post('absensi/save', 'Admin\Absensi::save');
    $routes->post('absensi/save_all', 'Admin\Absensi::save_all');

    // Nilai Siswa - disable for walikelas -> show under development
    // Allow PTS & PAS specifically
    $routes->get('nilai/pts', 'Admin\Nilai::pts');
    $routes->get('nilai/pas', 'Admin\Nilai::pas');
    $routes->post('nilai/store-bulk-exam', 'Admin\Nilai::storeBulkExam');
    $routes->get('nilai-siswa', function () {
        return view('admin/under_development');
    });
    $routes->get('nilai', function () {
        return view('admin/under_development');
    });
    $routes->get('nilai/input', function () {
        return view('admin/under_development');
    });
    $routes->get('nilai/create', function () {
        return view('admin/under_development');
    });
    $routes->post('nilai/store', function () {
        return view('admin/under_development');
    });
    $routes->get('nilai/detail/(:num)', function () {
        return view('admin/under_development');
    });
    $routes->get('nilai/edit/(:num)', function () {
        return view('admin/under_development');
    });
    $routes->post('nilai/update/(:num)', function () {
        return view('admin/under_development');
    });

    // Buku Kasus - disable for walikelas -> show under development
    $routes->get('buku-kasus', function () {
        return view('admin/under_development');
    });
    $routes->get('buku-kasus/(:any)', function () {
        return view('admin/under_development');
    });

    // Profile untuk walikelas
    $routes->get('profile', 'Admin\Profile::index');
    $routes->get('profile/edit', 'Admin\Profile::edit');
    $routes->post('profile/update', 'Admin\Profile::update');

    // 7 Kebiasaan - Monthly Recap (Walikelas limited view)
    $routes->get('habits/monthly', 'Admin\HabitMonthlyController::index');
    $routes->get('habits/monthly/students/(:num)', 'Admin\HabitMonthlyController::students/$1');
    $routes->get('habits/monthly/data', 'Admin\HabitMonthlyController::data');
    $routes->get('habits/monthly/export', 'Admin\HabitMonthlyController::export');
    $routes->get('habits/monthly/export-pdf', 'Admin\HabitMonthlyController::exportPdf');
});

// Logout Route (accessible from anywhere)
$routes->get('logout', 'Login::logout');

// Demo stubs to set role quickly
$routes->get('as-siswa', 'StubAuth::asSiswa');
$routes->get('as-guru', 'StubAuth::asGuru');
$routes->get('as-walikelas', 'StubAuth::asWalikelas');

// Debug auth
$routes->get('debug/auth/user', 'Debug\\AuthDebug::checkUser');
$routes->get('debug/auth/check', 'Debug\\AuthDebug::checkPassword');

// 7 Kebiasaan Anak Indonesia Hebat - Routes
// Siswa role
$routes->group('siswa', ['filter' => 'role:siswa'], function ($routes) {
    $routes->get('/', 'Siswa\\HabitController::index');
    $routes->get('habits', 'Siswa\\HabitController::habits'); // Habit input page
    $routes->get('habits/monthly-report', 'Siswa\\HabitController::monthlyReport'); // Monthly report page
    $routes->get('habits/monthly-data', 'Siswa\\HabitController::monthlyData'); // Monthly data API
    $routes->post('habits/save', 'Siswa\\HabitController::saveHabitData'); // Save habit data
    $routes->post('habits/delete', 'Siswa\\HabitController::deleteHabitData'); // Delete habit data
    $routes->post('logs', 'Siswa\\HabitController::store');
    $routes->get('today', 'Siswa\\HabitController::today');
    $routes->get('summary', 'Siswa\\HabitController::summary');
    $routes->get('stats', 'Siswa\\HabitController::getStats');
    $routes->get('profile', 'Siswa\\ProfileController::index');
    // Basic JSON name endpoint for dashboard lazy refresh
    $routes->get('profile/json-basic', 'Siswa\\ProfileController::basicJson');
    // Classroom (materi & tugas terpublish untuk kelas siswa)
    $routes->get('classroom', 'Siswa\\ClassroomController::index');
});

// Admin, Guru & Walikelas share access
$routes->group('guru', ['filter' => 'role:guru,walikelas,admin'], function ($routes) {
    $routes->get('dashboard', 'Guru\\DashboardController::index');
    $routes->get('stats.json', 'Guru\\DashboardController::stats');
    $routes->get('logs', 'Guru\\DashboardController::logs');
    $routes->get('logs/export', 'Guru\\DashboardController::exportCsv');
});

// Classroom Stage 1 Routes (basic lessons & assignments)
$routes->group('classroom', ['filter' => 'auth'], function ($routes) {
    // Lessons
    $routes->get('lessons', 'Classroom\\LessonController::index');
    $routes->get('lessons/create', 'Classroom\\LessonController::create');
    $routes->post('lessons/store', 'Classroom\\LessonController::store');
    $routes->get('lessons/(:num)', 'Classroom\\LessonController::show/$1');
    $routes->post('lessons/(:num)/publish', 'Classroom\\LessonController::publish/$1');
    // Assignments
    $routes->get('assignments', 'Classroom\\AssignmentController::index');
    $routes->get('assignments/create', 'Classroom\\AssignmentController::create');
    $routes->post('assignments/store', 'Classroom\\AssignmentController::store');
    $routes->get('assignments/(:num)/edit', 'Classroom\AssignmentController::edit/$1');
    $routes->post('assignments/(:num)/update', 'Classroom\AssignmentController::update/$1');
    $routes->post('assignments/(:num)/delete', 'Classroom\AssignmentController::delete/$1');
    $routes->get('assignments/question-image/(:any)', 'Classroom\AssignmentController::questionImage/$1');
    $routes->get('assignments/(:num)', 'Classroom\AssignmentController::show/$1');
    $routes->post('assignments/(:num)/submit-attempt', 'Classroom\\AssignmentController::submitAttempt/$1');
    $routes->post('assignments/(:num)/start', 'Classroom\AssignmentController::startAttempt/$1');
    $routes->post('assignments/(:num)/ajax-save', 'Classroom\AssignmentController::ajaxSaveAnswers/$1');
    $routes->get('assignments/(:num)/debug-questions', 'Classroom\\AssignmentController::debugQuestions/$1');
    $routes->match(['get', 'post'], 'assignments/test-upload', 'Classroom\\AssignmentController::testUpload');


    $routes->post('assignments/(:num)/publish', 'Classroom\\AssignmentController::publish/$1');
    // Submissions Stage 2
    $routes->get('assignments/(:num)/submit', 'Classroom\\SubmissionController::create/$1');
    $routes->post('assignments/(:num)/submit', 'Classroom\\SubmissionController::store/$1');
    $routes->get('assignments/(:num)/submission', 'Classroom\\SubmissionController::showOwn/$1');
    $routes->get('assignments/(:num)/submissions', 'Classroom\\SubmissionController::listForAssignment/$1');
    $routes->get('submissions/(:num)/grade', 'Classroom\\SubmissionController::gradeForm/$1');
    $routes->post('submissions/(:num)/grade', 'Classroom\\SubmissionController::grade/$1');
    // New: reset attempt & auto-grade
    $routes->post('assignments/(:num)/submissions/(:num)/reset', 'Classroom\\SubmissionController::resetAttempt/$1/$2');
    $routes->post('assignments/(:num)/submissions/(:num)/auto-grade', 'Classroom\\SubmissionController::autoGrade/$1/$2');
    $routes->post('assignments/(:num)/submissions/(:num)/delete', 'Classroom\\SubmissionController::deleteSubmission/$1/$2');
    // Attachments download
    $routes->get('attachment/(:num)/download', 'Classroom\\AttachmentController::download/$1');
});
