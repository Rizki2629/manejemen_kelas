<?php
namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table = 'nilai';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    // Legacy + new columns
    protected $allowedFields = [
        'siswa_id',        // existing
        'subject_id',      // new FK to subjects
        'mata_pelajaran',  // existing text subject
        'jenis_nilai',     // harian|pts|pas
        'kode_penilaian',  // PH-1, PH-2, ...
        'nilai',
        'tp_materi',
        'tanggal',
        'kelas',
        'semester',        // optional future
        'tahun_ajar'
    ];

    /* ================= Performance Helpers (Caching) ================= */
    private function cacheKeyMatrix(string $kelas,string $mapel): string { return 'harian_matrix_'.$kelas.'_'.$mapel; }
    private function cacheKeyUsedKode(string $kelas,string $mapel,string $jenis,string $prefix): string { return 'used_kode_'.$jenis.'_'.$prefix.'_'.$kelas.'_'.$mapel; }

    public function getCachedNilaiHarianMatrix(string $kelas,string $mapel,int $ttl=60): array
    {
        $cache = cache();
        $key = $this->cacheKeyMatrix($kelas,$mapel);
        $data = $cache->get($key);
        if($data!==null) return $data;
        $data = $this->getNilaiHarianMatrix($kelas,$mapel); // existing builder
        $cache->save($key,$data,$ttl);
        return $data;
    }

    public function getCachedUsedKodeNumbers(string $kelas,string $mapel,string $jenis='harian',string $prefix='PH',int $ttl=60): array
    {
        $cache = cache();
        $key = $this->cacheKeyUsedKode($kelas,$mapel,$jenis,$prefix);
        $data = $cache->get($key);
        if($data!==null) return $data;
        $data = $this->listUsedKodeNumbers($kelas,$mapel,$jenis,$prefix);
        $cache->save($key,$data,$ttl);
        return $data;
    }

    public function invalidateHarianCaches(string $kelas,string $mapel): void
    {
        $cache = cache();
        $cache->delete($this->cacheKeyMatrix($kelas,$mapel));
        // Invalidate possible prefixes (PH only now) & jenis harian
        $cache->delete($this->cacheKeyUsedKode($kelas,$mapel,'harian','PH'));
    }

    public function byStudentAndSubject($siswaId, $subjectId)
    {
        return $this->where('siswa_id',$siswaId)->where('subject_id',$subjectId)->findAll();
    }

    public function averageBySubject($subjectId)
    {
        return $this->selectAvg('nilai','avg_nilai')->where('subject_id',$subjectId)->first();
    }

    /**
     * Generate kode penilaian berikutnya (PH-n) per kelas + mapel.
     */
    public function getNextKodeHarian(string $kelas, string $mapel): string
    {
        // Jika kolom belum ada (migration belum dijalankan) kembalikan PH-1 agar tidak error
        try {
            $db = \Config\Database::connect();
            if (! $db->fieldExists('kode_penilaian', $this->table)) {
                return 'PH-1';
            }
        } catch (\Throwable $e) {
            return 'PH-1';
        }
        // Hitung kode yang sudah terisi (non-null). Jika belum ada satupun (semua NULL karena belum dibackfill), fallback hitung distinct grup tanggal+tp_materi.
        $builder = $this->where('kelas',$kelas)
            ->where('mata_pelajaran',$mapel)
            ->where('jenis_nilai','harian')
            ->where('deleted_at IS NULL', null, false);
        $row = $builder->select('COUNT(DISTINCT kode_penilaian) as cnt_kode, SUM(CASE WHEN kode_penilaian IS NULL THEN 1 ELSE 0 END) as null_rows')
            ->first();
        $cntKode = (int)($row['cnt_kode'] ?? 0);
        if ($cntKode > 0) {
            // Ada kode valid -> next = jumlah distinct kode + 1
            return 'PH-' . ($cntKode + 1);
        }
        // Tidak ada kode non-null, fallback kelompokkan berdasarkan tanggal + tp_materi untuk memperkirakan jumlah PH yang sudah pernah diinput.
        $db = \Config\Database::connect();
        $res = $db->table($this->table)
            ->select('DATE(tanggal) as tgl, COALESCE(tp_materi,"") as tp')
            ->where('kelas',$kelas)
            ->where('mata_pelajaran',$mapel)
            ->where('jenis_nilai','harian')
            ->where('deleted_at IS NULL')
            ->groupBy('DATE(tanggal), COALESCE(tp_materi,"")')
            ->get()->getResultArray();
        $countGroups = count($res);
        return 'PH-' . ($countGroups + 1);
    }

    /**
     * List nomor yang sudah dipakai untuk kode penilaian (PH/PTS/PAS) per kelas + mapel + jenis.
     * Return array of integers e.g. [1,2,3]. Jika kolom belum ada -> []
     */
    public function listUsedKodeNumbers(string $kelas, string $mapel, string $jenis = 'harian', string $prefix = 'PH'): array
    {
        try {
            $db = \Config\Database::connect();
            if (! $db->fieldExists('kode_penilaian', $this->table)) {
                return [];
            }
            // Ambil distinct kode yang sudah ada (non-null)
            $kodeRows = $db->table($this->table)
                ->select('DISTINCT kode_penilaian')
                ->where('kelas',$kelas)
                ->where('mata_pelajaran',$mapel)
                ->where('jenis_nilai',$jenis)
                ->where('deleted_at IS NULL')
                ->where('kode_penilaian IS NOT NULL')
                ->get()->getResultArray();
            $used = [];
            foreach($kodeRows as $r){
                $kp = $r['kode_penilaian'] ?? '';
                if(!$kp) continue;
                if(stripos($kp, $prefix.'-') === 0){
                    $num = (int)substr($kp, strlen($prefix)+1);
                    if($num>0) $used[$num]=true;
                }
            }
            if ($used) { // Sudah ada kode eksplisit -> kembalikan daftar ini
                $nums = array_keys($used); sort($nums, SORT_NUMERIC); return $nums;
            }
            // Fallback: belum ada kode terisi (semua NULL). Gunakan kelompok tanggal+tp_materi untuk menghasilkan nomor 1..n
            $groups = $db->table($this->table)
                ->select('DATE(tanggal) as tgl, COALESCE(tp_materi,"") as tp')
                ->where('kelas',$kelas)
                ->where('mata_pelajaran',$mapel)
                ->where('jenis_nilai',$jenis)
                ->where('deleted_at IS NULL')
                ->groupBy('DATE(tanggal), COALESCE(tp_materi,"")')
                ->orderBy('DATE(tanggal)','ASC')
                ->get()->getResultArray();
            $out=[]; $i=1; foreach($groups as $g){ $out[] = $i++; }
            return $out; // e.g. [1,2,3]
        } catch (\Throwable $e) {
            return [];
        }
    }

    /* ================= Helper Methods referenced by Controller ================= */

    public function canAccessClass($userId, $kelas, $userRole): bool
    {
        if ($userRole === 'admin') return true;
        if ($userRole === 'wali_kelas' || $userRole === 'walikelas') {
            $db = \Config\Database::connect();
            $q = $db->query("SELECT COUNT(*) as c FROM users u JOIN walikelas w ON u.walikelas_id=w.id WHERE u.id=? AND w.kelas=?", [$userId, $kelas])->getRowArray();
            return ($q['c'] ?? 0) > 0;
        }
        return false;
    }

    public function getMataPelajaranList(): array
    {
        // Canonical ordered list requested by user (fixed order)
        $canonical = [
            'Pendidikan Agama',
            'Pendidikan Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'Ilmu Pengetahuan Alam dan Sosial',
            'Seni Rupa',
            'Pendidikan Jasmani Olahraga dan Kesenian', // as requested (note: DB might store "Kesehatan")
            'Pendidikan Lingkungan dan Budaya Jakarta',
            'Bahasa Inggris',
            'Coding'
        ];

        // Aliases -> canonical
        $aliases = [
            'Agama' => 'Pendidikan Agama',
            'PKn' => 'Pendidikan Pancasila',
            'PPKN' => 'Pendidikan Pancasila',
            'IPA' => 'Ilmu Pengetahuan Alam dan Sosial',
            'IPS' => 'Ilmu Pengetahuan Alam dan Sosial',
            'IPAS' => 'Ilmu Pengetahuan Alam dan Sosial',
            'Seni Budaya' => 'Seni Rupa',
            'Olahraga' => 'Pendidikan Jasmani Olahraga dan Kesenian',
            'PJOK' => 'Pendidikan Jasmani Olahraga dan Kesenian',
            'Pendidikan Jasmani Olahraga dan Kesehatan' => 'Pendidikan Jasmani Olahraga dan Kesenian',
            'PLBJ' => 'Pendidikan Lingkungan dan Budaya Jakarta'
        ];

        $extra = [];
        try {
            $db = \Config\Database::connect();
            // collect distinct mapel from subjects table
            if ($db->tableExists('subjects')) {
                $rows = $db->table('subjects')->select('DISTINCT name')->where('deleted_at IS NULL')->get()->getResultArray();
                foreach ($rows as $r) {
                    $raw = $r['name'];
                    $canon = $aliases[$raw] ?? $raw;
                    if (!in_array($canon, $canonical, true) && !in_array($canon, $extra, true)) $extra[] = $canon;
                }
            }
            // collect distinct legacy mapel from nilai table
            $rows2 = $db->query("SELECT DISTINCT mata_pelajaran FROM nilai WHERE mata_pelajaran IS NOT NULL AND mata_pelajaran<>''")->getResultArray();
            foreach ($rows2 as $r) {
                $raw = $r['mata_pelajaran'];
                $canon = $aliases[$raw] ?? $raw;
                if (!in_array($canon, $canonical, true) && !in_array($canon, $extra, true)) $extra[] = $canon;
            }
        } catch (\Throwable $e) {}

        $ordered = $canonical; // base
        foreach ($extra as $x) { $ordered[] = $x; }

        $out = []; foreach ($ordered as $n) { $out[$n] = $n; }
        return $out;
    }

    public function getJenisNilaiList(): array
    {
        return [ 'harian' => 'Penilaian Harian', 'pts' => 'PTS', 'pas' => 'PAS' ];
    }

    public function getNilaiDetailSiswa(int $siswaId, string $mapel): array
    {
        return $this->where('siswa_id',$siswaId)
            ->where('mata_pelajaran',$mapel)
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('tanggal','ASC')
            ->findAll();
    }

    public function getOrderedMapelList(): array
    {
        // Could implement custom ordering; fallback alphabetical
        $list = array_keys($this->getMataPelajaranList());
        return $list;
    }

    public function getNilaiRekap(string $kelas, string $mapel): array
    {
        // Simple average per student for index page (can be extended)
        $db = \Config\Database::connect();
        $sql = "SELECT s.id as siswa_id, s.nama, AVG(n.nilai) as rata_rata FROM tb_siswa s LEFT JOIN nilai n ON n.siswa_id=s.id AND n.mata_pelajaran=? AND n.kelas=? AND n.jenis_nilai='harian' AND n.deleted_at IS NULL WHERE s.kelas=? AND s.deleted_at IS NULL GROUP BY s.id ORDER BY s.nama";
        return $db->query($sql, [$mapel,$kelas,$kelas])->getResultArray();
    }

    /**
     * Build matrix untuk rekap nilai harian digunakan di view input.
     * Struktur return:
     * [ 'headers'=>[ ['label'=>'PH-1','date'=>'2025-08-27','tp'=>'Topik'] ... ], 'values'=>[ siswa_id => [1=>80,2=>90] ], 'students'=>[ ... ] ]
     */
    public function getNilaiHarianMatrix(string $kelas, string $mapel): array
    {
        $db = \Config\Database::connect();
        $hasKode = false;
        try { $hasKode = $db->fieldExists('kode_penilaian', $this->table); } catch(\Throwable $e) { $hasKode = false; }

        // Ambil semua kombinasi penilaian harian (urut berdasarkan tanggal lalu kode)
        $select = ($hasKode ? 'kode_penilaian, ' : '') . 'tanggal, tp_materi';
        $builder = $db->table($this->table)
            ->select($select)
            ->where('kelas', $kelas)
            ->where('mata_pelajaran', $mapel)
            ->where('jenis_nilai','harian')
            ->where('deleted_at IS NULL');
        if ($hasKode) {
            $builder->groupBy('kode_penilaian, tanggal, tp_materi')
                ->orderBy('tanggal','ASC')
                ->orderBy('kode_penilaian','ASC');
        } else {
            $builder->groupBy('tanggal, tp_materi')->orderBy('tanggal','ASC');
        }
        $rows = $builder->get()->getResultArray();
        // Safeguard: jika hanya 1 siswa muncul di UI biasanya karena headers map->index tidak konsisten
        // Pastikan header unik & terurut stabil
        $seen = [];
        $filtered = [];
        foreach($rows as $r){
            $key = ($hasKode?($r['kode_penilaian']??''):($r['tanggal'].'|'.$r['tp_materi']));
            if(isset($seen[$key])) continue; $seen[$key]=true; $filtered[]=$r; }
        $rows = $filtered;

    $headers = [];
    $kodeIndexMap = []; // kode_penilaian OR synthetic label => column number
    $signatureIndexMap = []; // for fallback mode: signature (tanggal|tp_materi) => column number
        $colNo = 1;
        foreach ($rows as $r) {
            $kode = $hasKode ? ($r['kode_penilaian'] ?? null) : null;
            $label = $kode ?: ('PH-' . $colNo);
            $headers[] = [ 'label' => $label, 'date' => $r['tanggal'], 'tp' => $r['tp_materi'] ];
            $kodeIndexMap[$kode ?: $label] = $colNo;
            if(!$hasKode){
                $sig = $r['tanggal'].'|'.$r['tp_materi'];
                $signatureIndexMap[$sig] = $colNo;
            }
            $colNo++;
        }

        // Ambil semua nilai harian
    // Selalu ambil tp_materi agar fallback (tanpa kode_penilaian) bisa mapping kolom dengan signature tanggal|tp
    $nilaiSelect = 'siswa_id, nilai' . ($hasKode ? ', kode_penilaian' : '') . ', tanggal, tp_materi';
        $nilaiBuilder = $db->table($this->table)
            ->select($nilaiSelect)
            ->where('kelas', $kelas)
            ->where('mata_pelajaran', $mapel)
            ->where('jenis_nilai','harian')
            ->where('deleted_at IS NULL');
        if($hasKode){
            $nilaiBuilder->orderBy('kode_penilaian','ASC')->orderBy('siswa_id','ASC');
        } else {
            $nilaiBuilder->orderBy('tanggal','ASC')->orderBy('siswa_id','ASC');
        }
    $nilaiRows = $nilaiBuilder->get()->getResultArray();

        $values = [];
        foreach ($nilaiRows as $nr) {
            if($hasKode){
                $k = $nr['kode_penilaian'] ?? null;
                $idx = $k ? ($kodeIndexMap[$k] ?? null) : null;
            } else {
                $tp = $nr['tp_materi'] ?? '';
                $sig = ($nr['tanggal'] ?? '') . '|' . $tp;
                $idx = $signatureIndexMap[$sig] ?? null;
            }
            if ($idx === null) continue;
            $values[$nr['siswa_id']][$idx] = isset($nr['nilai']) ? (float)$nr['nilai'] : null;
        }

        // Students list
        $students = $db->table('tb_siswa')
            ->select('id, nama, nisn')
            ->where('kelas',$kelas)
            ->where('deleted_at IS NULL')
            ->orderBy('nama','ASC')
            ->get()->getResultArray();

        return [
            'headers' => $headers,
            'values' => $values,
            'students' => $students,
        ];
    }
}
