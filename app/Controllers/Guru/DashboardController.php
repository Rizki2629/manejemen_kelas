<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\HabitModel;
use App\Models\HabitLogModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use CodeIgniter\I18n\Time;

class DashboardController extends BaseController
{
    protected $habitModel;
    protected $habitLogModel;
    protected $studentModel;
    protected $classModel;

    public function __construct()
    {
        helper(['form','date']);
        $this->habitModel = new HabitModel();
        $this->habitLogModel = new HabitLogModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
    }

    public function index()
    {
        $classes = $this->classModel->findAll();
        $habits = $this->habitModel->findAll();
        $today = date('Y-m-d');
        $weekAgo = Time::parse($today)->subDays(6)->toDateString();
        return view('guru/dashboard', compact('classes','habits','today','weekAgo'));
    }

    public function stats()
    {
        $classId = $this->request->getGet('classId');
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        if (!$from || !$to) {
            $to = date('Y-m-d');
            $from = (new Time($to))->subDays(6)->toDateString();
        }
        $habitId = $this->request->getGet('habitId');

        // basic aggregation: percentage of value_bool=1 over available logs
        $builder = db_connect()->table('habit_logs hl')
            ->select('hl.log_date, hl.habit_id, COUNT(*) as total, SUM(CASE WHEN hl.value_bool=1 THEN 1 ELSE 0 END) as yes_count')
            ->join('siswa s', 's.id=hl.student_id')
            ->where('hl.log_date >=', $from)
            ->where('hl.log_date <=', $to)
            ->groupBy('hl.log_date, hl.habit_id')
            ->orderBy('hl.log_date','ASC');
        if ($classId) $builder->where('s.kelas_id', (int)$classId);
        if ($habitId) $builder->where('hl.habit_id', (int)$habitId);
        $rows = $builder->get()->getResultArray();

        return $this->response->setJSON(['data' => $rows]);
    }

    public function logs()
    {
        $classId = $this->request->getGet('classId');
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-d');

        $builder = db_connect()->table('habit_logs hl')
            ->select('hl.log_date, s.nama as siswa_nama, k.nama as kelas_nama, h.name as habit_nama, hl.value_bool, hl.value_time, hl.value_number, hl.notes')
            ->join('siswa s', 's.id=hl.student_id')
            ->join('kelas k', 'k.id=s.kelas_id')
            ->join('habits h', 'h.id=hl.habit_id')
            ->where('hl.log_date >=', $from)
            ->where('hl.log_date <=', $to)
            ->orderBy('hl.log_date','DESC');
        if ($classId) $builder->where('s.kelas_id', (int)$classId);

        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $perPage = 20;
        $offset = ($page-1)*$perPage;

        $total = (clone $builder)->select('COUNT(*) as cnt')->get()->getRowArray()['cnt'] ?? 0;
        $rows = $builder->limit($perPage, $offset)->get()->getResultArray();

        $classes = $this->classModel->findAll();

        return view('guru/logs', [
            'rows' => $rows,
            'total' => (int)$total,
            'page' => $page,
            'perPage' => $perPage,
            'classId' => $classId,
            'from' => $from,
            'to' => $to,
            'classes' => $classes,
        ]);
    }

    public function exportCsv()
    {
        $classId = $this->request->getGet('classId');
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-d');

        $builder = db_connect()->table('habit_logs hl')
            ->select('hl.log_date, s.nama as siswa_nama, k.nama as kelas_nama, h.name as habit_nama, hl.value_bool, hl.value_time, hl.value_number, hl.notes')
            ->join('siswa s', 's.id=hl.student_id')
            ->join('kelas k', 'k.id=s.kelas_id')
            ->join('habits h', 'h.id=hl.habit_id')
            ->where('hl.log_date >=', $from)
            ->where('hl.log_date <=', $to)
            ->orderBy('hl.log_date','DESC');
        if ($classId) $builder->where('s.kelas_id', (int)$classId);

        $rows = $builder->get()->getResultArray();

        $filename = 'habit_logs_'.date('Ymd_His').'.csv';
        $header = ['Tanggal','Siswa','Kelas','Kebiasaan','Ya','Waktu','Angka','Catatan'];

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $header);
        foreach ($rows as $r) {
            fputcsv($csv, [
                $r['log_date'],
                $r['siswa_nama'],
                $r['kelas_nama'],
                $r['habit_nama'],
                (string)($r['value_bool']==1 ? 'Ya' : 'Tidak'),
                $r['value_time'],
                $r['value_number'],
                $r['notes'],
            ]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $this->response->setHeader('Content-Type','text/csv')
            ->setHeader('Content-Disposition','attachment; filename="'.$filename.'"')
            ->setBody($content);
    }
}
