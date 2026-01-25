<?php

namespace App\Models;

use CodeIgniter\Model;

class HabitLogModel extends Model
{
    protected $table            = 'habit_logs';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'student_id','habit_id','log_date','value_bool','value_time','value_number','notes','value_json','created_by'
    ];
    protected $useTimestamps    = false;

    /**
     * Upsert habit log with array data or individual parameters
     */
    public function upsertLog(...$args)
    {
        // Check if first argument is array (old signature) or individual parameters (new signature)
        if (count($args) === 1 && is_array($args[0])) {
            // Old signature: upsertLog(array $data)
            $data = $args[0];
        } else {
            // New signature: upsertLog(int $studentId, int $habitId, string $logDate, bool $completed, ?string $time, ?float $duration, ?string $notes)
            list($studentId, $habitId, $logDate, $completed, $time, $duration, $notes) = array_pad($args, 7, null);
            
            $data = [
                'student_id' => $studentId,
                'habit_id' => $habitId,
                'log_date' => $logDate,
                'value_bool' => $completed ? 1 : 0,
                'value_time' => $time,
                'value_number' => $duration,
                'notes' => $notes
            ];
        }

        // Upsert by unique key (student_id, habit_id, log_date)
        $existing = $this->where([
            'student_id' => $data['student_id'],
            'habit_id' => $data['habit_id'],
            'log_date' => $data['log_date'],
        ])->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        
        return $this->insert($data, true);
    }

    public function getDailySummary(int $studentId, string $date)
    {
        return $this->select('habit_id, value_bool, value_time, value_number, notes, value_json')
            ->where('student_id', $studentId)
            ->where('log_date', $date)
            ->findAll();
    }

    /**
     * Save complex habit data with JSON support
     */
    public function saveHabitData(int $studentId, int $habitId, string $date, array $data)
    {
        $logData = [
            'student_id' => $studentId,
            'habit_id' => $habitId,
            'log_date' => $date,
            'value_bool' => $data['value_bool'] ?? null,
            'value_time' => $data['value_time'] ?? null,
            'value_number' => $data['value_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? null
        ];

        // If complex data exists, store as JSON
        if (isset($data['complex_data']) && !empty($data['complex_data'])) {
            $logData['value_json'] = json_encode($data['complex_data']);
        }

        return $this->upsertLog($logData);
    }

    /**
     * Get habit data with JSON parsing
     */
    public function getHabitData(int $studentId, int $habitId, string $date)
    {
        $result = $this->where([
            'student_id' => $studentId,
            'habit_id' => $habitId,
            'log_date' => $date
        ])->first();

        if ($result && !empty($result['value_json'])) {
            $result['complex_data'] = json_decode($result['value_json'], true);
        }

        return $result;
    }
}
