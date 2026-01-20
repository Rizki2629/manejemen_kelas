<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\HabitLogModel;
use App\Models\HabitModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HabitMonthlyController extends BaseController
{
    protected $kelasModel;
    protected $siswaModel;
    protected $habitLogModel;
    protected $habitModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
    $this->siswaModel = new SiswaModel();
        $this->habitLogModel = new HabitLogModel();
        $this->habitModel = new HabitModel();
    }

    public function index()
    {
        // Normalize session user data (legacy sessions may not set 'user')
        $sess = session();
        $user = $sess->get('user');
        if(!$user){
            $user = [
                'id' => $sess->get('user_id'), // user table id
                'role' => $sess->get('role'),
                'walikelas_id' => $sess->get('walikelas_id'), // reference to walikelas table
            ];
        }
        $role = $user['role'] ?? 'guest';

        // Walikelas: auto lock to their class(es) (filter by walikelas_id from walikelas table, not user id)
        if($role === 'walikelas' && !empty($user['walikelas_id'])) {
            $kelas = [];
            try {
                $kelasQuery = $this->kelasModel->where('walikelas_id', $user['walikelas_id']);
                $fields = $this->kelasModel->db->getFieldNames($this->kelasModel->table);
                if(in_array('status',$fields)) $kelasQuery = $kelasQuery->where('status','aktif');
                $kelas = $kelasQuery->findAll();
            } catch(\Throwable $e) { }

            // Fallback: if no row in kelas table, derive from walikelas mapping (users.walikelas_id -> walikelas.kelas)
            if(empty($kelas)) {
                try {
                    $db = \Config\Database::connect();
                    $row = $db->query("SELECT w.kelas FROM users u JOIN walikelas w ON u.walikelas_id = w.id WHERE u.id = ?", [$sess->get('user_id')])->getRowArray();
                    if($row && !empty($row['kelas'])) {
                        $kelas[] = [ 'id' => 0, 'nama' => trim($row['kelas']) ]; // synthetic id 0 (no edit ops needed)
                    }
                } catch(\Throwable $e) { }
            }

            foreach($kelas as &$c){
                if(isset($c['nama']) && preg_match('/^(Kelas)\s*([1-6])\s*([A-D])$/i',$c['nama'],$m)){
                    $c['nama'] = $m[1].' '.$m[2].' '.$m[3];
                } elseif(isset($c['nama']) && preg_match('/^(Kelas)\s*([1-6])([A-D])$/i',$c['nama'],$m)) {
                    $c['nama'] = $m[1].' '.$m[2].' '.$m[3];
                } elseif(isset($c['nama']) && preg_match('/^([1-6])\s*([A-D])$/i',$c['nama'],$m)) {
                    $c['nama'] = 'Kelas '.$m[1].' '.$m[2];
                }
            }
            // Ubah: walikelas sekarang boleh memilih (dropdown aktif) tetapi hanya melihat kelas miliknya
            return view('admin/habits/monthly_admin', [ 'classes'=>$kelas, 'restricted'=>false ]);
        }
        if(method_exists($this->kelasModel,'ensureStandardClasses')){ $this->kelasModel->ensureStandardClasses(); }
        $classes = $this->kelasModel->getKelasAktif();
        // Format display name: "Kelas 5A" => "Kelas 5 A"
        foreach($classes as &$c){
            if(isset($c['nama']) && preg_match('/^(Kelas)\s*([1-6])\s*([A-D])$/i',$c['nama'],$m)){
                $c['nama'] = $m[1].' '.$m[2].' '.$m[3];
            } elseif(isset($c['nama']) && preg_match('/^(Kelas)\s*([1-6])([A-D])$/i',$c['nama'],$m)) {
                $c['nama'] = $m[1].' '.$m[2].' '.$m[3];
            }
        }
        return view('admin/habits/monthly_admin', [ 'classes'=>$classes, 'restricted'=>false ]);
    }

    public function students($kelasId)
    {
        // Rehydrate user session like in index() to avoid missing role causing 404 fallback failure
        $user = session('user');
        if(!$user){
            $user = [
                'id' => session()->get('user_id'),
                'role' => session()->get('role'),
                'walikelas_id' => session()->get('walikelas_id'),
            ];
        }
        $role = $user['role'] ?? (session()->get('role') ?: 'guest');
        if($role === 'walikelas') {
            // Use walikelas_id (relation to walikelas table) not user id
            $walikelasRelation = $user['walikelas_id'] ?? session()->get('walikelas_id');
            $allowed = [];
            if($walikelasRelation){
                $allowed = $this->kelasModel->where('walikelas_id',$walikelasRelation)->findColumn('id');
            }
            $kelasIdInt = (int)$kelasId;
            if(empty($allowed)) {
                // No kelas rows mapped; allow fallback only for id 0
                if($kelasIdInt !== 0) {
                    return $this->response->setStatusCode(403)->setJSON(['message'=>'Tidak diizinkan (kelas tidak terdaftar untuk walikelas)']);
                }
            } else {
                if(!in_array($kelasIdInt, array_map('intval',$allowed))) {
                    return $this->response->setStatusCode(403)->setJSON(['message'=>'Tidak diizinkan']);
                }
            }
        }

        $kelasRow = $this->kelasModel->find($kelasId);
        $kelasNama = null;
        $fallbackUsed = false;
        if(!$kelasRow){
            // Fallback for walikelas with synthetic id 0 (no kelas row) -> fetch kelas name via walikelas table
            if($role === 'walikelas' && (int)$kelasId === 0) {
                try {
                    $dbTmp = db_connect();
                    $row = $dbTmp->query("SELECT w.kelas FROM users u JOIN walikelas w ON u.walikelas_id = w.id WHERE u.id = ?", [$user['id'] ?? session()->get('user_id')])->getRowArray();
                    if($row && !empty($row['kelas'])) {
                        $kelasNama = trim($row['kelas']);
                        $fallbackUsed = true;
                    }
                } catch(\Throwable $e) {}
            }
            if(!$kelasNama){
                return $this->response->setStatusCode(404)->setJSON(['message'=>'Kelas tidak ditemukan']);
            }
        } else {
            $kelasNama = trim($kelasRow['nama']); // contoh: "Kelas 5 A" atau "Kelas 5A"
        }

        // Ekstrak token angka & huruf (misal 5A)
        $token = null; $digit = null; $huruf = null;
        if(preg_match('/([1-6])\s*([A-D])/i', $kelasNama, $m)) {
            $digit = $m[1];
            $huruf = strtoupper($m[2]);
            $token = $digit.$huruf; // 5A
        }

        $db = db_connect();
        $debug = [
            'kelas_nama' => $kelasNama,
            'token' => $token,
            'fallback' => $fallbackUsed,
        ];
        $students = [];
        try {
                        // Normalisasi nama kelas agar variasi seperti "Kelas 5A", "5 A", "KELAS 5 A" cocok.
                        $normalize = function($s){
                            $s = strtoupper($s);
                            // Hilangkan kata KELAS jika ada
                            $s = preg_replace('/\bKELAS\b/','',$s);
                            // Hilangkan semua karakter non alfanumerik (spasi, strip, dsb)
                            $s = preg_replace('/[^A-Z0-9]/','',$s);
                            return trim($s);
                        };

                        $targetNorm = $normalize($kelasNama); // contoh: "KELAS 5 A" -> "5A"
                        $debug['target_norm'] = $targetNorm;

                        $rows = [];
                        $candidateRows = [];
                        $patternUsed = null;
                        if($token){
                            // Batasi kandidat berdasar token digit+huruf agar query tidak terlalu luas
                            $pattern1 = "%$digit%$huruf%";            // misal %5%A%
                            $pattern2 = "%KELAS%$digit%$huruf%";      // %KELAS%5%A%
                            $pattern3 = "%$digit$huruf%";             // %5A%
                            $sqlCandidates = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                    FROM tb_siswa t
                                    LEFT JOIN siswa s ON s.nisn = t.nisn
                                    WHERE (UPPER(t.kelas) LIKE ? OR UPPER(t.kelas) LIKE ? OR UPPER(t.kelas) LIKE ?)
                                      AND UPPER(t.kelas) <> 'LULUS'
                                    ORDER BY t.nama ASC";
                            $candidateRows = $db->query($sqlCandidates, [$pattern1,$pattern2,$pattern3])->getResultArray();
                            $patternUsed = [$pattern1,$pattern2,$pattern3];
                        } else {
                            // Jika tidak bisa ekstrak token, ambil seluruh (dibatasi) untuk tetap fungsional.
                            $sqlAll = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                FROM tb_siswa t
                                LEFT JOIN siswa s ON s.nisn = t.nisn
                                WHERE UPPER(t.kelas) <> 'LULUS'
                                ORDER BY t.nama ASC"; // tidak LIMIT dulu supaya normalisasi tetap bisa memilih semua, asumsikan jumlah manageable
                            $candidateRows = $db->query($sqlAll)->getResultArray();
                            $patternUsed = 'FULL';
                        }

                        $debug['candidates_initial'] = count($candidateRows);
                        $debug['pattern_used'] = $patternUsed;

                        $sampleNorms = [];
                        // Langkah 1: coba exact match TRIM + case-insensitive (tanpa menghapus kata KELAS atau spasi internal)
                        $exactTarget = strtoupper(trim($kelasNama));
                        $exactRows = [];
                        foreach($candidateRows as $cr){
                            $kelasRaw = strtoupper(trim($cr['kelas']));
                            if($kelasRaw === $exactTarget){
                                $exactRows[] = $cr;
                            }
                            if(count($sampleNorms) < 5){
                                $sampleNorms[] = $cr['kelas']."=>".$kelasRaw;
                            }
                        }
                        $debug['exact_target'] = $exactTarget;
                        $debug['matched_exact'] = count($exactRows);

                        if($exactRows){
                            // Jika ada exact match gunakan itu saja (lebih presisi, tidak mengikutkan variasi seperti 5A)
                            $rows = $exactRows;
                        } else {
                            // Langkah 2: fallback ke normalisasi agresif (hapus kata KELAS & spasi) seperti sebelumnya
                            foreach($candidateRows as $cr){
                                $norm = $normalize($cr['kelas']);
                                if($norm === $targetNorm){
                                    $rows[] = $cr;
                                }
                            }
                        }
                        $debug['sample_norms'] = $sampleNorms;
                        $debug['matched_after_norm'] = count($rows);

                        // Jika tidak ada setelah normalisasi, coba fallback exact (kadang data sudah benar namun token gagal diekstrak)
                        if(!$rows){
                            $sqlExact = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                FROM tb_siswa t
                                LEFT JOIN siswa s ON s.nisn = t.nisn
                                WHERE UPPER(TRIM(t.kelas)) = ? AND UPPER(t.kelas) <> 'LULUS'
                                ORDER BY t.nama ASC";
                            $rows = $db->query($sqlExact,[strtoupper(trim($kelasNama))])->getResultArray();
                            $debug['fallback_exact_used'] = count($rows);
                        }

                        // Fallback terakhir: ambil sebagian daftar untuk dianalisa (limit) bila tetap kosong
                        if(!$rows){
                            $sqlLast = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                FROM tb_siswa t
                                LEFT JOIN siswa s ON s.nisn = t.nisn
                                WHERE UPPER(t.kelas) <> 'LULUS'
                                ORDER BY t.nama ASC LIMIT 40";
                            $tmp = $db->query($sqlLast)->getResultArray();
                            $debug['fallback_sample_count'] = count($tmp);
                            // Tidak menimpa $rows (tetap kosong) tapi kirim sample untuk debugging front-end
                            $debug['fallback_sample'] = array_map(function($r){return $r['kelas'];}, $tmp);
                        }

                        $mapped=0; $unmapped=0; $students=[];
                        foreach($rows as $r){
                            if($r['mapped_id']) $mapped++; else $unmapped++;
                            $students[] = [
                                'id'=>(int)$r['tb_id'],
                                'tb_id'=>(int)$r['tb_id'],
                                'legacy_id'=>$r['mapped_id'] ? (int)$r['mapped_id'] : null,
                                'nama'=>$r['nama'],
                                'kelas'=>$r['kelas'],
                                'mapping_status'=>$r['mapped_id'] ? 'mapped':'tb_only'
                            ];
                        }
                        $debug['final_count'] = count($students);
                        $debug['mapped'] = $mapped;
                        $debug['unmapped'] = $unmapped;
        } catch(\Throwable $e){
            $debug['error'] = $e->getMessage();
        }

        // Fallback tambahan jika tetap kosong: lakukan pencarian lebih longgar langsung pada kolom tb_siswa.kelas
        if (empty($students)) {
            try {
                $db2 = db_connect();
                $digit = $digit ?? null; // dari ekstraksi sebelumnya (bisa null)
                $huruf = $huruf ?? null;
                $fallbackStudents = [];
                $fallbackDebug = [];

                // 1) Jika ada field kelas_id di tb_siswa dan $kelasRow tersedia, coba gunakan kelas_id langsung
                try {
                    $fieldsTb = $db2->getFieldNames('tb_siswa');
                } catch(\Throwable $e) { $fieldsTb = []; }
                if (!empty($kelasRow) && in_array('kelas_id', $fieldsTb)) {
                    $sqlByKelasId = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                     FROM tb_siswa t
                                     LEFT JOIN siswa s ON s.nisn = t.nisn
                                     WHERE t.kelas_id = ? AND (t.deleted_at IS NULL OR t.deleted_at = '')
                                     ORDER BY t.nama ASC";
                    $resByKelasId = $db2->query($sqlByKelasId, [$kelasRow['id']])->getResultArray();
                    $fallbackDebug['by_kelas_id_count'] = count($resByKelasId);
                    if ($resByKelasId) {
                        foreach($resByKelasId as $r){
                            $fallbackStudents[] = [ 'id'=>(int)$r['tb_id'], 'tb_id'=>(int)$r['tb_id'], 'legacy_id'=>$r['mapped_id']?(int)$r['mapped_id']:null, 'nama'=>$r['nama'], 'kelas'=>$r['kelas'], 'mapping_status'=>$r['mapped_id']?'mapped':'tb_only' ];
                        }
                    }
                }

                // 2) Jika masih kosong, gunakan LIKE longgar dengan pola digit+huruf (misal %5%A% atau %5A%)
                if (empty($fallbackStudents) && $digit && $huruf) {
                    $like1 = "%$digit$huruf%";        // %5A%
                    $like2 = "%$digit%$huruf%";       // %5%A%
                    $like3 = "%KELAS%$digit$huruf%";  // %KELAS%5A%
                    $sqlLoose = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                  FROM tb_siswa t
                                  LEFT JOIN siswa s ON s.nisn = t.nisn
                                  WHERE (UPPER(t.kelas) LIKE ? OR UPPER(t.kelas) LIKE ? OR UPPER(t.kelas) LIKE ?)
                                    AND UPPER(t.kelas) <> 'LULUS'
                                  ORDER BY t.nama ASC";
                    $rowsLoose = $db2->query($sqlLoose, [$like1,$like2,$like3])->getResultArray();
                    $fallbackDebug['loose_like_count'] = count($rowsLoose);
                    foreach($rowsLoose as $r){
                        $fallbackStudents[] = [ 'id'=>(int)$r['tb_id'], 'tb_id'=>(int)$r['tb_id'], 'legacy_id'=>$r['mapped_id']?(int)$r['mapped_id']:null, 'nama'=>$r['nama'], 'kelas'=>$r['kelas'], 'mapping_status'=>$r['mapped_id']?'mapped':'tb_only' ];
                    }
                }

                // 3) Jika masih kosong, coba exact match case-insensitive setelah trim (tanpa normalisasi rumit)
                if (empty($fallbackStudents)) {
                    $sqlExactSimple = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                                      FROM tb_siswa t
                                      LEFT JOIN siswa s ON s.nisn = t.nisn
                                      WHERE UPPER(TRIM(t.kelas)) = ? AND UPPER(t.kelas) <> 'LULUS'
                                      ORDER BY t.nama ASC";
                    $rowsExactSimple = $db2->query($sqlExactSimple, [strtoupper(trim($kelasNama))])->getResultArray();
                    $fallbackDebug['exact_simple_count'] = count($rowsExactSimple);
                    foreach($rowsExactSimple as $r){
                        $fallbackStudents[] = [ 'id'=>(int)$r['tb_id'], 'tb_id'=>(int)$r['tb_id'], 'legacy_id'=>$r['mapped_id']?(int)$r['mapped_id']:null, 'nama'=>$r['nama'], 'kelas'=>$r['kelas'], 'mapping_status'=>$r['mapped_id']?'mapped':'tb_only' ];
                    }
                }

                if (!empty($fallbackStudents)) {
                    $students = $fallbackStudents; // pakai hasil fallback
                    $debug['fallback_final_count'] = count($students);
                } else {
                    $debug['fallback_final_count'] = 0;
                }
                $debug['fallback_chain'] = $fallbackDebug;
            } catch(\Throwable $e) {
                $debug['fallback_error'] = $e->getMessage();
            }
        }

        // Extra fallback terakhir: bandingkan nama kelas dengan menghapus spasi (REPLACE) dan tanpa kata 'KELAS'
        if (empty($students)) {
            try {
                $db3 = db_connect();
                $rawNama = strtoupper($kelasNama);
                $noSpace = preg_replace('/\s+/','', $rawNama);          // KELAS5A atau 5A
                $noKelas = preg_replace('/KELAS/i','', $rawNama);         // 5 A / 5A
                $noKelasNoSpace = preg_replace('/\s+/','', $noKelas);    // 5A
                // Gunakan pola LIKE longgar terhadap versi tanpa spasi
                $patternMain = '%'.$noKelasNoSpace.'%';
                $sqlExtra = "SELECT COALESCE(s.id, -t.id) AS effective_id, s.id AS mapped_id, t.id AS tb_id, t.nama, t.kelas
                              FROM tb_siswa t
                              LEFT JOIN siswa s ON s.nisn = t.nisn
                              WHERE REPLACE(UPPER(t.kelas),' ','') LIKE ?
                                AND UPPER(t.kelas) <> 'LULUS'
                              ORDER BY t.nama ASC";
                $rowsExtra = $db3->query($sqlExtra, [$patternMain])->getResultArray();
                $debug['extra_fallback_pattern'] = $patternMain;
                $debug['extra_fallback_count'] = count($rowsExtra);
                if ($rowsExtra) {
                    foreach($rowsExtra as $r){
                        $students[] = [ 'id'=>(int)$r['tb_id'], 'tb_id'=>(int)$r['tb_id'], 'legacy_id'=>$r['mapped_id']?(int)$r['mapped_id']:null, 'nama'=>$r['nama'], 'kelas'=>$r['kelas'], 'mapping_status'=>$r['mapped_id']?'mapped':'tb_only' ];
                    }
                }
            } catch(\Throwable $e) {
                $debug['extra_fallback_error'] = $e->getMessage();
            }
        }

        return $this->response->setJSON([
            'data' => $students,
            'count' => count($students),
            'debug' => $debug,
        ]);
    }

    public function data()
    {
        $month = $this->request->getGet('month');
        $inputId = (int)$this->request->getGet('student_id');
        if(!$month || !preg_match('/^\d{4}-\d{2}$/',$month) || $inputId===0){
            return $this->response->setStatusCode(422)->setJSON(['message'=>'Param tidak valid']);
        }
        $db = db_connect();
        $mappingStatus = 'tb_direct';
        $legacyId = null;
        // 1. Direct match in tb_siswa?
        $rowTb = $db->query('SELECT id FROM tb_siswa WHERE id=? LIMIT 1', [abs($inputId)])->getRowArray();
        if($rowTb){
            $tbId = (int)$rowTb['id'];
            if($inputId < 0){
                $mappingStatus = 'negative_abs_canonical';
            }
        } else {
            // 2. Try legacy siswa.id -> tb via nisn
            $rowMap = $db->query('SELECT t.id AS tb_id FROM siswa s JOIN tb_siswa t ON t.nisn=s.nisn WHERE s.id=? LIMIT 1', [$inputId])->getRowArray();
            if($rowMap){
                $tbId = (int)$rowMap['tb_id'];
                $legacyId = $inputId;
                $mappingStatus = 'legacy_mapped';
            } else {
                // 3. Fallback: treat absolute as canonical (no validation)
                $tbId = abs($inputId);
                $mappingStatus = 'assumed_tb';
            }
        }
        $start = $month.'-01';
        $end = date('Y-m-t', strtotime($start));
        $rows = $db->query("SELECT hl.log_date, hl.habit_id, hl.value_bool, hl.value_time, hl.value_number, hl.notes, h.name as habit_name FROM habit_logs hl JOIN habits h ON h.id=hl.habit_id WHERE hl.student_id=? AND hl.log_date BETWEEN ? AND ? ORDER BY hl.log_date, hl.habit_id", [$tbId,$start,$end])->getResultArray();
        $monthly=[];
        foreach($rows as $r){
            $d=$r['log_date']; $key='habit_'.$r['habit_id'];
            if(!isset($monthly[$d])) $monthly[$d]=[];
            $completed = ($r['value_bool']==1) || $r['value_time'] || ($r['value_number'] && $r['value_number']>0) || !empty($r['notes']);
            $monthly[$d][$key]=[
                'completed'=>$completed,
                'time'=>$r['value_time'],
                'duration'=>$r['value_number'],
                'notes'=>$r['notes'],
                'habit_name'=>$r['habit_name']
            ];
        }
        return $this->response->setJSON([
            'status'=>'success',
            'data'=>$monthly,
            'month'=>$month,
            'input_id'=>$inputId,
            'tb_id'=>$tbId,
            'legacy_id'=>$legacyId,
            'mapping_status'=>$mappingStatus,
            'row_count'=>count($rows)
        ]);
    }

    public function export()
    {
        $month = $this->request->getGet('month');
        $inputId = (int)$this->request->getGet('student_id');
        if(!$month || !preg_match('/^\d{4}-\d{2}$/',$month) || $inputId===0){
            return $this->response->setStatusCode(422)->setBody('Parameter tidak valid');
        }
        $db = db_connect();
        $rowTb = $db->query('SELECT id,nama FROM tb_siswa WHERE id=? LIMIT 1',[abs($inputId)])->getRowArray();
        if($rowTb){
            $tbId = (int)$rowTb['id'];
            $studentName = $rowTb['nama'];
        } else {
            $rowMap = $db->query('SELECT t.id AS tb_id, t.nama FROM siswa s JOIN tb_siswa t ON t.nisn=s.nisn WHERE s.id=? LIMIT 1', [$inputId])->getRowArray();
            if($rowMap){
                $tbId = (int)$rowMap['tb_id'];
                $studentName = $rowMap['nama'];
            } else {
                $tbId = abs($inputId);
                $studentName = 'Siswa '.$tbId;
            }
        }
        $start = $month.'-01';
        $end = date('Y-m-t', strtotime($start));
        $rows = $db->query("SELECT hl.log_date, hl.habit_id, hl.value_bool, hl.value_time, hl.value_number, hl.notes, h.name as habit_name FROM habit_logs hl JOIN habits h ON h.id=hl.habit_id WHERE hl.student_id=? AND hl.log_date BETWEEN ? AND ? ORDER BY hl.log_date, hl.habit_id", [$tbId,$start,$end])->getResultArray();
        $byDate=[]; foreach($rows as $r){ $d=$r['log_date']; if(!isset($byDate[$d])) $byDate[$d]=[]; $byDate[$d]['habit_'.$r['habit_id']]=$r; }
        $ss = new Spreadsheet(); $sheet=$ss->getActiveSheet(); $sheet->setTitle('Rekap '.$month);
        $headers = ['Tanggal','Status (x/7)','Bangun Pagi','Beribadah','Berolahraga','Makan Sehat','Gemar Belajar','Bermasyarakat','Tidur Cepat'];
        // Title section rows 1-3
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A1','Rekapitulasi Gerakan 7 Kebiasaan Anak Indonesia Hebat');
        $sheet->setCellValue('A2','SDN Grogol Utara 09');
        $sheet->setCellValue('A3','Bulan : '.$this->formatIndonesianMonth($month));
        $sheet->getStyle('A1:A3')->applyFromArray([
            'font'=>['bold'=>true,'size'=>14,'color'=>['rgb'=>'4C1D95']],
            'alignment'=>['horizontal'=>'center']
        ]);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(12)->setBold(false);
        $headerRow = 5; // leaving row 4 as spacer
        // Merge untuk nama siswa (H4:I4) sesuai permintaan
        $sheet->mergeCells('H4:I4');
        $sheet->setCellValue('H4',$studentName);
        $sheet->getStyle('H4')->applyFromArray([
            'font'=>['bold'=>true,'color'=>['rgb'=>'000000']],'alignment'=>['horizontal'=>'center','vertical'=>'center'],
            'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'F59E0B']]
        ]);
        $sheet->getRowDimension(4)->setRowHeight(24);
        $colIndex = 1;
        foreach($headers as $h){
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter.$headerRow,$h);
            $colIndex++;
        }
        $dataStartRow = $headerRow + 1;
        $rowIdx=$dataStartRow; $period = new \DatePeriod(new \DateTime($start), new \DateInterval('P1D'), (new \DateTime($end))->modify('+1 day'));
        foreach($period as $dt){
            // Simpan sebagai nilai tanggal Excel (bukan string) agar format kustom bisa diterapkan
            $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dt);
            $dateStr=$dt->format('Y-m-d'); // original string (jika dibutuhkan logika lain)
            $sheet->setCellValue('A'.$rowIdx,$excelDate);
            $done=0; for($i=1;$i<=7;$i++){ if(!empty($byDate[$dateStr]['habit_'.$i])){ $r=$byDate[$dateStr]['habit_'.$i]; if($r['value_bool']==1 || $r['value_time'] || ($r['value_number'] && $r['value_number']>0) || !empty($r['notes'])) $done++; }}
            $sheet->setCellValue('B'.$rowIdx,$done.'/7');
            $sheet->setCellValue('C'.$rowIdx,$byDate[$dateStr]['habit_1']['value_time'] ?? '');
            $ib=$byDate[$dateStr]['habit_2'] ?? null; $sheet->setCellValue('D'.$rowIdx,$ib?($ib['notes']??''):'');
            $sheet->setCellValue('E'.$rowIdx,$this->formatCell($byDate,$dateStr,3));
            $sheet->setCellValue('F'.$rowIdx,$this->formatCell($byDate,$dateStr,4));
            $sheet->setCellValue('G'.$rowIdx,$this->formatCell($byDate,$dateStr,5));
            $sheet->setCellValue('H'.$rowIdx,$this->formatCell($byDate,$dateStr,6));
            $sheet->setCellValue('I'.$rowIdx,$byDate[$dateStr]['habit_7']['value_time'] ?? '');
            $rowIdx++;
        }
        // Styling modern & full color (adjusted rows)
        $lastDataRow = $rowIdx - 1;
        $sheet->getStyle('A'.$headerRow.':I'.$headerRow)->applyFromArray([
            'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'6D28D9']],
            'alignment'=>['horizontal'=>'center','vertical'=>'center']
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);
        $sheet->getStyle('A'.$headerRow.':I'.$lastDataRow)->applyFromArray([
            'borders'=>[
                'allBorders'=>['borderStyle'=>'thin','color'=>['rgb'=>'D1D5DB']]
            ],
            'alignment'=>['vertical'=>'center']
        ]);
        // Date column format
    // Format tanggal dd-mm-yyyy sesuai permintaan
    $sheet->getStyle('A'.$dataStartRow.':A'.$lastDataRow)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        // Alignment & wrap text untuk seluruh data (center + middle, wrap)
        $sheet->getStyle('A'.$dataStartRow.':I'.$lastDataRow)->applyFromArray([
            'alignment'=>[
                'horizontal'=>'center','vertical'=>'center','wrapText'=>true
            ]
        ]);
        // Zebra striping
        for($r=$dataStartRow;$r<=$lastDataRow;$r++){
            if($r % 2 === 0){
                $sheet->getStyle('A'.$r.':I'.$r)->getFill()->setFillType('solid')->getStartColor()->setRGB('F5F3FF');
            }
        }
        for($r=$dataStartRow;$r<=$lastDataRow;$r++){
            $val = $sheet->getCell('B'.$r)->getValue();
            if($val === '7/7'){
                $sheet->getStyle('A'.$r.':I'.$r)->getFill()->setFillType('solid')->getStartColor()->setRGB('ECFDF5');
            }
        }
        $sheet->setAutoFilter('A'.$headerRow.':I'.$lastDataRow);
        $sheet->freezePane('A'.$dataStartRow);
        // Fixed column widths (character units) per permintaan:
        // Tanggal 14, Status 11, Bangun Pagi 10, Beribadah 25, Berolahraga 15, Makan Sehat 15, Gemar Belajar 15, Bermasyarakat 15, Tidur Cepat 10
        // Perlu lebih lebar lagi (round 2)
        $colWidths = [
            'A'=>16,'B'=>13,'C'=>12,'D'=>32,'E'=>19,'F'=>19,'G'=>19,'H'=>19,'I'=>12
        ];
        foreach($colWidths as $col=>$w){
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        // Page setup: F4 (gunakan FOLIO sebagai pendekatan) landscape & fit 1 halaman lebar
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO); // F4 approx
        $pageSetup->setFitToWidth(1); $pageSetup->setFitToHeight(0);
        // Center horizontally & vertically on page
        $pageSetup->setHorizontalCentered(true);
        $pageSetup->setVerticalCentered(false);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.3)->setRight(0.3)->setHeader(0.2)->setFooter(0.2);
        // Set print area agar hanya area terpakai dicetak satu halaman
        $sheet->getPageSetup()->setPrintArea('A1:I'.$lastDataRow);
        // Set tinggi baris data & header 60px (~45pt)
        $rowHeightPoints = 45; // 60px @96dpi => 60/96*72 = 45
        for($r=$headerRow;$r<=$lastDataRow;$r++){
            $sheet->getRowDimension($r)->setRowHeight($rowHeightPoints);
        }
        $writer = new Xlsx($ss); ob_start(); $writer->save('php://output'); $data = ob_get_clean();
        return $this->response->setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition','attachment; filename="rekap_habits_'.$month.'_s'.$tbId.'.xlsx"')
            ->setBody($data);
    }

    private function formatCell($byDate,$date,$habitId){
        if(empty($byDate[$date]['habit_'.$habitId])) return '';
        $r=$byDate[$date]['habit_'.$habitId];
        // Khusus untuk Berolahraga (3): gabungkan notes + total menit
        if($habitId === 3){
            $notes = trim($r['notes'] ?? '');
            $minutes = $r['value_number'];
            if($notes && $minutes){
                return $notes.' (total : '.$minutes.' Menit)';
            }
            if($notes) return $notes;
            if($minutes) return $minutes.' Menit';
            if($r['value_time']) return $r['value_time'];
            if($r['value_bool']) return '✓';
            return '';
        }
        // Khusus untuk habit Makan Sehat (4), Gemar Belajar (5), Bermasyarakat (6): tampilkan input / notes nya, bukan ceklis
        if(in_array($habitId,[4,5,6])){
            // Urutan prioritas: number -> time -> notes (input teks). Abaikan tanda ceklis boolean.
            if($r['value_number']) return $r['value_number'];
            if($r['value_time']) return $r['value_time'];
            if(!empty($r['notes'])) return $r['notes'];
            // Jika tidak ada input lain, dan hanya boolean true, biarkan kosong (tanpa ✓)
            return '';
        }
        if($r['value_number']) return $r['value_number'];
        if($r['value_time']) return $r['value_time'];
        if($r['value_bool']) return '✓';
        return $r['notes'] ?? '';
    }

    private function formatIndonesianMonth(string $ym){
        // ym format: YYYY-MM
        [$y,$m] = explode('-', $ym);
        $nama = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $mi = (int)$m; return $nama[$mi].' '.$y;
    }

    public function exportPdf(){
        $month = $this->request->getGet('month');
        $inputId = (int)$this->request->getGet('student_id');
        if(!$month || !preg_match('/^\d{4}-\d{2}$/',$month) || $inputId===0){
            return $this->response->setStatusCode(422)->setBody('Parameter tidak valid');
        }
        $db = db_connect();
        $rowTb = $db->query('SELECT id,nama FROM tb_siswa WHERE id=? LIMIT 1',[abs($inputId)])->getRowArray();
        if($rowTb){
            $tbId = (int)$rowTb['id'];
            $studentName = $rowTb['nama'];
        } else {
            $rowMap = $db->query('SELECT t.id AS tb_id, t.nama FROM siswa s JOIN tb_siswa t ON t.nisn=s.nisn WHERE s.id=? LIMIT 1', [$inputId])->getRowArray();
            if($rowMap){
                $tbId = (int)$rowMap['tb_id'];
                $studentName = $rowMap['nama'];
            } else {
                $tbId = abs($inputId);
                $studentName = 'Siswa '.$tbId;
            }
        }
        $start = $month.'-01';
        $end = date('Y-m-t', strtotime($start));
        $rows = $db->query("SELECT hl.log_date, hl.habit_id, hl.value_bool, hl.value_time, hl.value_number, hl.notes FROM habit_logs hl WHERE hl.student_id=? AND hl.log_date BETWEEN ? AND ? ORDER BY hl.log_date, hl.habit_id", [$tbId,$start,$end])->getResultArray();
        $byDate=[]; foreach($rows as $r){ $d=$r['log_date']; if(!isset($byDate[$d])) $byDate[$d]=[]; $byDate[$d]['habit_'.$r['habit_id']]=$r; }
        $period = new \DatePeriod(new \DateTime($start), new \DateInterval('P1D'), (new \DateTime($end))->modify('+1 day'));
        $html = '<html><head><meta charset="UTF-8"><style>
            @page { margin:20px 25px 25px 25px; }
            body{font-family: DejaVu Sans, Arial, sans-serif; font-size:11px;}
            h1,h2,h3{margin:4px 0; text-align:center;}
            .wrapper{width:100%; margin:0 auto;}
            table{width:100%; border-collapse:collapse; margin-top:8px; table-layout:fixed;}
            th,td{border:1px solid #666; padding:4px 5px; text-align:center; vertical-align:middle; word-wrap:break-word;}
            thead th{background:#6D28D9; color:#fff; font-weight:bold;}
            thead{display:table-header-group;}
            tbody tr:nth-child(even) td{background:#f5f3ff;}
            .name-row td{background:#F59E0B; font-weight:bold; color:#000;}
        </style></head><body><div class="wrapper">';
        $html .= '<h1>Rekapitulasi Gerakan 7 Kebiasaan Anak Indonesia Hebat</h1>';
        $html .= '<h2>SDN Grogol Utara 09</h2>';
        $html .= '<h3>Bulan : '.$this->formatIndonesianMonth($month).'</h3>';
        $html .= '<table>';
        $html .= '<tr class="name-row"><td colspan="9">Nama Siswa: '.htmlspecialchars($studentName).' | Bulan: '.htmlspecialchars($this->formatIndonesianMonth($month)).'</td></tr>';
        $html .= '<thead><tr><th>Tanggal</th><th>Status (x/7)</th><th>Bangun Pagi</th><th>Beribadah</th><th>Berolahraga</th><th>Makan Sehat</th><th>Gemar Belajar</th><th>Bermasyarakat</th><th>Tidur Cepat</th></tr></thead><tbody>';
    foreach($period as $dt){
            $dateStrYmd=$dt->format('Y-m-d'); // key format in $byDate
            $dateStr=$dt->format('d-m-Y');    // display format
            $done=0; for($i=1;$i<=7;$i++){ if(!empty($byDate[$dateStrYmd]['habit_'.$i])){ $r=$byDate[$dateStrYmd]['habit_'.$i]; if($r['value_bool']==1 || $r['value_time'] || ($r['value_number'] && $r['value_number']>0) || !empty($r['notes'])) $done++; }}
        $isFull = ($done===7);
        $cellStyle = $isFull ? ' style="background:#ecfdf5;"' : '';
        $html .= '<tr>';
        $html .= '<td'.$cellStyle.'>'.$dateStr.'</td>';
        $html .= '<td'.$cellStyle.'>'.$done.'/7</td>';
            // Gunakan index array berdasarkan format Y-m-d (asli) sementara tampilan dd-mm-yyyy
            $bgp = $byDate[$dateStrYmd]['habit_1']['value_time'] ?? '';
            $ib = $byDate[$dateStrYmd]['habit_2']['notes'] ?? '';
            $bol = $this->formatCell($byDate,$dateStrYmd,3);
            $ms = $this->formatCell($byDate,$dateStrYmd,4);
            $gb = $this->formatCell($byDate,$dateStrYmd,5);
            $bm = $this->formatCell($byDate,$dateStrYmd,6);
            $tc = $byDate[$dateStrYmd]['habit_7']['value_time'] ?? '';
            $cells = [$bgp,$ib,$bol,$ms,$gb,$bm,$tc];
        foreach($cells as $c){ $html .= '<td'.$cellStyle.'>'.htmlspecialchars($c).'</td>'; }
            $html .= '</tr>';
        }
    $html .= '</tbody></table></div></body></html>';
        // Dompdf render
        try {
            $dompdf = new \Dompdf\Dompdf([ 'isRemoteEnabled'=>true ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('folio','landscape');
            $dompdf->render();
            $output = $dompdf->output();
            return $this->response->setHeader('Content-Type','application/pdf')
                ->setHeader('Content-Disposition','attachment; filename="rekap_habits_'.$month.'_s'.$tbId.'.pdf"')
                ->setBody($output);
        } catch(\Throwable $e){
            return $this->response->setStatusCode(500)->setBody('PDF error: '.$e->getMessage());
        }
    }
}
