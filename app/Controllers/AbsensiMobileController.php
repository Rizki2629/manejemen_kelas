<?php

namespace App\Controllers;

use App\Models\AbsensiMobileModel;
use App\Models\SiswaModel;
use CodeIgniter\I18n\Time;

class AbsensiMobileController extends BaseController
{
    public function index()
    {
        return view('absensi_mobile/index');
    }

    public function lookup()
    {
        $nisn = trim((string) $this->request->getGet('nisn'));

        if ($nisn === '' || strlen($nisn) < 5) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'NISN tidak valid.',
            ])->setStatusCode(400);
        }

        $siswaModel = new SiswaModel();
        $student = $siswaModel
            ->select('id, nama, nisn, kelas')
            ->where('nisn', $nisn)
            ->where('deleted_at IS NULL')
            ->first();

        if (!$student) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'NISN tidak ditemukan.',
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'ok' => true,
            'student' => [
                'id' => (int) $student['id'],
                'nama' => (string) $student['nama'],
                'nisn' => (string) ($student['nisn'] ?? ''),
                'kelas' => (string) ($student['kelas'] ?? ''),
            ],
        ]);
    }

    public function reverseGeocode()
    {
        $lat = $this->request->getGet('lat');
        $lon = $this->request->getGet('lon');

        if (!is_numeric($lat) || !is_numeric($lon)) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Koordinat tidak valid.',
            ])->setStatusCode(400);
        }

        $latF = (float) $lat;
        $lonF = (float) $lon;

        $cacheKey = 'absensi_mobile_geocode_' . md5(number_format($latF, 5, '.', '') . ',' . number_format($lonF, 5, '.', ''));
        $cache = cache();
        $cached = $cache->get($cacheKey);
        if (is_array($cached)) {
            return $this->response->setJSON(['ok' => true] + $cached);
        }

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => 6,
                'http_errors' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                    'User-Agent' => 'manajemen_kelas/absensi-mobile',
                ],
            ]);

            $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&zoom=18&lat=' . rawurlencode((string) $latF) . '&lon=' . rawurlencode((string) $lonF);
            $res = $client->get($url);

            $body = (string) $res->getBody();
            $json = json_decode($body, true);

            $display = is_array($json) ? (string) ($json['display_name'] ?? '') : '';

            $payload = $this->formatNominatimPayload($json, $display);

            $cache->save($cacheKey, $payload, 60 * 60 * 12);

            return $this->response->setJSON(['ok' => true] + $payload);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'ok' => true,
                'displayName' => '',
                'lines' => [],
                'road' => '',
            ]);
        }
    }

    /**
     * Submit foto absensi mobile.
     *
     * Expected multipart/form-data:
     * - nisn
     * - device_taken_at (ISO string, optional)
     * - latitude, longitude, accuracy_m (optional)
     * - address (optional)
     * - photo (jpeg/png)
     */
    public function submit()
    {
        $nisn = trim((string) $this->request->getPost('nisn'));
        if ($nisn === '' || strlen($nisn) < 5) {
            return $this->response->setJSON(['ok' => false, 'message' => 'NISN tidak valid.'])->setStatusCode(400);
        }

        $siswaModel = new SiswaModel();
        $student = $siswaModel
            ->select('id, nama, nisn, kelas')
            ->where('nisn', $nisn)
            ->where('deleted_at IS NULL')
            ->first();

        if (!$student) {
            return $this->response->setJSON(['ok' => false, 'message' => 'NISN tidak ditemukan.'])->setStatusCode(404);
        }

        $photo = $this->request->getFile('photo');
        if (!$photo || !$photo->isValid()) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Foto tidak ditemukan / gagal diupload.'])->setStatusCode(400);
        }

        if (!$photo->isValid() || $photo->hasMoved()) {
            return $this->response->setJSON(['ok' => false, 'message' => 'File foto tidak valid.'])->setStatusCode(400);
        }

        // CI4 UploadedFile tidak selalu punya isImage(); validasi pakai MIME.
        $mime = '';
        try {
            $mime = (string) $photo->getMimeType();
        } catch (\Throwable $e) {
            $mime = '';
        }
        if ($mime === '') {
            try {
                $mime = (string) $photo->getClientMimeType();
            } catch (\Throwable $e) {
                $mime = '';
            }
        }
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if ($mime === '' || !in_array(strtolower($mime), $allowedMimes, true)) {
            return $this->response->setJSON(['ok' => false, 'message' => 'File harus berupa gambar (JPG/PNG/WebP).'])->setStatusCode(400);
        }

        if ($photo->getSize() > 4 * 1024 * 1024) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Ukuran foto terlalu besar (maks 4MB).'])->setStatusCode(400);
        }

        $today = Time::now(app_timezone())->toDateString();

        $lat = $this->request->getPost('latitude');
        $lon = $this->request->getPost('longitude');
        $acc = $this->request->getPost('accuracy_m');
        $address = trim((string) $this->request->getPost('address'));
        $deviceTakenAt = trim((string) $this->request->getPost('device_taken_at'));

        $latF = is_numeric($lat) ? (float) $lat : null;
        $lonF = is_numeric($lon) ? (float) $lon : null;
        $accF = is_numeric($acc) ? (float) $acc : null;

        $deviceTakenAtDb = null;
        if ($deviceTakenAt !== '') {
            try {
                $dt = new \DateTime($deviceTakenAt);
                $dt->setTimezone(new \DateTimeZone(app_timezone()));
                $deviceTakenAtDb = $dt->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $deviceTakenAtDb = null;
            }
        }

        $absensiMobileModel = new AbsensiMobileModel();

        // 1 NISN hanya boleh absen 1 kali per tanggal
        $existing = $absensiMobileModel
            ->where('siswa_id', (int) $student['id'])
            ->where('tanggal', $today)
            ->first();
        if ($existing) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'NISN ini sudah absen hari ini.',
            ])->setStatusCode(409);
        }

        // folder: writable/uploads/absensi-mobile-YYYYMMDD
        $subdir = 'absensi-mobile-' . date('Ymd');
        $targetDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subdir);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $safeNisn = preg_replace('/[^0-9A-Za-z_-]/', '', $nisn);
        $safeNama = preg_replace('/[^0-9A-Za-z_-]+/', '-', (string) ($student['nama'] ?? ''));
        $safeNama = trim($safeNama, '-');
        if ($safeNama === '') {
            $safeNama = 'siswa';
        }
        $safeKelas = preg_replace('/[^0-9A-Za-z_-]+/', '-', (string) ($student['kelas'] ?? ''));
        $safeKelas = trim($safeKelas, '-');
        if ($safeKelas === '') {
            $safeKelas = 'kelas';
        }

        // nama file: nama-siswa-kelas-nisn.jpg
        $filenameBase = $safeNama . '-' . $safeKelas . '-' . $safeNisn;
        $filenameBase = substr($filenameBase, 0, 140);
        $filename = $filenameBase . '.jpg';

        try {
            $photo->move($targetDir, $filename, true);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Gagal menyimpan foto di server.'])->setStatusCode(500);
        }

        $relativePath = 'uploads/' . $subdir . '/' . $filename;

        $payload = [
            'siswa_id' => (int) $student['id'],
            'nisn' => (string) ($student['nisn'] ?? $nisn),
            'nama' => (string) $student['nama'],
            'kelas' => (string) ($student['kelas'] ?? ''),
            'tanggal' => $today,
            'device_taken_at' => $deviceTakenAtDb,
            'latitude' => $latF,
            'longitude' => $lonF,
            'accuracy_m' => $accF,
            'address' => $address !== '' ? $address : null,
            'photo_path' => $relativePath,
            'ip_address' => (string) ($this->request->getIPAddress() ?? ''),
            'user_agent' => substr((string) ($this->request->getUserAgent() ?? ''), 0, 255),
        ];

        $id = (int) $absensiMobileModel->insert($payload, true);

        return $this->response->setJSON([
            'ok' => true,
            'message' => 'Absensi berhasil disimpan.',
            'id' => $id,
        ]);
    }

    public function rekap()
    {
        $model = new AbsensiMobileModel();

        $date = trim((string) $this->request->getGet('date'));
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = '';
        }

        $q = $model->orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC');
        if ($date !== '') {
            $q->where('tanggal', $date);
        }

        // Batasi agar halaman tidak berat
        $rows = $q->findAll(500);

        $grouped = [];
        foreach ($rows as $r) {
            $d = (string) ($r['tanggal'] ?? '');
            if ($d === '') {
                $d = 'unknown';
            }
            if (!isset($grouped[$d])) {
                $grouped[$d] = [];
            }
            $grouped[$d][] = $r;
        }

        return view('absensi_mobile/rekap', [
            'date' => $date,
            'grouped' => $grouped,
            'total' => count($rows),
        ]);
    }

    public function photo($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $model = new AbsensiMobileModel();
        $row = $model->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $photoPath = (string) ($row['photo_path'] ?? '');
        $photoPath = str_replace('\\', '/', $photoPath);
        if ($photoPath === '' || !str_starts_with($photoPath, 'uploads/')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $relative = substr($photoPath, strlen('uploads/'));
        $filepath = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowedExts, true) || !is_file($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($filepath))
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody((string) file_get_contents($filepath));
    }

    public function delete($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return $this->response->setJSON(['ok' => false, 'message' => 'ID tidak valid.'])->setStatusCode(400);
        }

        $model = new AbsensiMobileModel();
        $row = $model->find($id);
        if (!$row) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Data tidak ditemukan.'])->setStatusCode(404);
        }

        $photoPath = (string) ($row['photo_path'] ?? '');
        $photoPath = str_replace('\\', '/', $photoPath);
        $filepath = null;
        if ($photoPath !== '' && str_starts_with($photoPath, 'uploads/')) {
            $relative = substr($photoPath, strlen('uploads/'));
            $filepath = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        }

        if (!$model->delete($id)) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Gagal menghapus data.'])->setStatusCode(500);
        }

        if ($filepath && is_file($filepath)) {
            @unlink($filepath);
        }

        return $this->response->setJSON(['ok' => true, 'message' => 'Data berhasil dihapus.']);
    }

    private function formatAddressLines(string $displayName): array
    {
        $displayName = trim($displayName);
        if ($displayName === '') {
            return [];
        }

        // Nominatim display_name biasanya comma-separated; ambil 3-4 segmen paling relevan.
        $parts = array_values(array_filter(array_map('trim', explode(',', $displayName)), fn($p) => $p !== ''));

        // Prioritaskan bagian awal (jalan/area kecil) hingga provinsi.
        $lines = [];
        if (count($parts) > 0) {
            $lines[] = implode(', ', array_slice($parts, 0, min(2, count($parts))));
        }
        if (count($parts) > 2) {
            $lines[] = implode(', ', array_slice($parts, 2, min(2, count($parts) - 2)));
        }
        if (count($parts) > 4) {
            $lines[] = implode(', ', array_slice($parts, 4, min(2, count($parts) - 4)));
        }

        // Batasi 3 baris.
        return array_slice($lines, 0, 3);
    }

    private function formatNominatimPayload($json, string $displayName): array
    {
        $address = (is_array($json) && isset($json['address']) && is_array($json['address'])) ? $json['address'] : [];

        $road = (string) (
            $address['road']
            ?? $address['pedestrian']
            ?? $address['footway']
            ?? $address['path']
            ?? $address['residential']
            ?? $address['service']
            ?? $address['locality']
            ?? $address['quarter']
            ?? $address['neighbourhood']
            ?? ''
        );

        $houseNumber = (string) ($address['house_number'] ?? '');
        if ($road !== '' && $houseNumber !== '') {
            $road = $road . ' No ' . $houseNumber;
        }

        if ($road === '') {
            $name = is_array($json) ? (string) ($json['name'] ?? '') : '';
            $place = (string) (
                $address['amenity']
                ?? $address['shop']
                ?? $address['tourism']
                ?? $address['building']
                ?? ''
            );
            $road = $name !== '' ? $name : $place;
        }

        if ($road === '' && $displayName !== '') {
            // Fallback: bagian pertama dari display_name biasanya nama jalan/area kecil
            $parts = array_values(array_filter(array_map('trim', explode(',', $displayName)), fn($p) => $p !== ''));
            $road = (string) ($parts[0] ?? '');
        }

        // Struktur alamat Indonesia (best-effort dari field Nominatim)
        $kelurahan = (string) (
            $address['village']
            ?? $address['hamlet']
            ?? $address['neighbourhood']
            ?? $address['suburb']
            ?? $address['quarter']
            ?? ''
        );
        $kecamatan = (string) (
            $address['city_district']
            ?? $address['district']
            ?? $address['municipality']
            ?? $address['suburb']
            ?? ''
        );
        $kota = (string) (
            $address['city']
            ?? $address['town']
            ?? $address['county']
            ?? ''
        );
        $provinsi = (string) (
            $address['state']
            ?? $address['region']
            ?? ''
        );
        $postcode = (string) ($address['postcode'] ?? '');

        // Dedupe agar tidak tampil ganda
        $norm = static fn($v) => strtolower(trim((string) $v));
        if ($kelurahan !== '' && $kecamatan !== '' && $norm($kelurahan) === $norm($kecamatan)) {
            $kecamatan = '';
        }
        if ($kota !== '' && $kecamatan !== '' && $norm($kota) === $norm($kecamatan)) {
            $kecamatan = '';
        }
        if ($kota !== '' && $kelurahan !== '' && $norm($kota) === $norm($kelurahan)) {
            $kota = '';
        }

        $lines = [];
        if ($road !== '') {
            $lines[] = $road;
        }

        $line2Parts = array_values(array_filter([$kelurahan, $kecamatan], fn($v) => is_string($v) && trim($v) !== ''));
        if (!empty($line2Parts)) {
            $lines[] = implode(', ', $line2Parts);
        }

        $line3Parts = array_values(array_filter([$kota, $provinsi], fn($v) => is_string($v) && trim($v) !== ''));
        if ($postcode !== '') {
            $line3Parts[] = $postcode;
        }
        if (!empty($line3Parts)) {
            $lines[] = implode(', ', $line3Parts);
        }

        if (empty($lines)) {
            $lines = $this->formatAddressLines($displayName);
        }

        return [
            'displayName' => $displayName,
            'road' => $road,
            'kelurahan' => $kelurahan,
            'kecamatan' => $kecamatan,
            'kota' => $kota,
            'provinsi' => $provinsi,
            'postcode' => $postcode,
            'lines' => array_slice($lines, 0, 3),
        ];
    }
}
