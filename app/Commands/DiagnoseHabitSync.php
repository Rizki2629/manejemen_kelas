<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DiagnoseHabitSync extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:habit-sync';
    protected $description = 'Diagnose habit data synchronization issues between localStorage and database';

    public function run(array $params)
    {
        $studentId = $params[0] ?? '5';
        $date = $params[1] ?? date('Y-m-d');
        
        CLI::write("🔍 Diagnosing habit sync issues for student ID: {$studentId} on date: {$date}", 'yellow');
        
        $db = \Config\Database::connect();
        
        // 1. Check if data exists in database
        CLI::write("\n1. 📊 Checking database data...", 'cyan');
        $query = $db->query("
            SELECT hl.*, h.name as habit_name 
            FROM habit_logs hl 
            JOIN habits h ON h.id = hl.habit_id 
            WHERE hl.student_id = ? AND hl.log_date = ?
            ORDER BY hl.habit_id
        ", [$studentId, $date]);
        
        $dbData = $query->getResultArray();
        
        if (empty($dbData)) {
            CLI::write("❌ No data found in database for this date", 'red');
            CLI::write("   This explains why data doesn't appear in other browsers", 'red');
        } else {
            CLI::write("✅ Found " . count($dbData) . " habit entries in database:", 'green');
            foreach ($dbData as $entry) {
                CLI::write("   - {$entry['habit_name']}: " . 
                          ($entry['value_bool'] ? 'completed' : 'not completed'), 'white');
            }
        }
        
        // 2. Check API endpoints
        CLI::write("\n2. 🌐 Testing API endpoints...", 'cyan');
        
        // Test store endpoint
        CLI::write("   Testing store endpoint: POST /siswa/logs", 'white');
        $storeRoute = base_url('siswa/logs');
        CLI::write("   URL: {$storeRoute}", 'blue');
        
        // Test summary endpoint  
        CLI::write("   Testing summary endpoint: GET /siswa/summary", 'white');
        $summaryRoute = base_url("siswa/summary?date={$date}");
        CLI::write("   URL: {$summaryRoute}", 'blue');
        
        // 3. Check recent submissions
        CLI::write("\n3. 📅 Checking recent submissions...", 'cyan');
        $recentQuery = $db->query("
            SELECT log_date, COUNT(*) as habit_count, MAX(created_at) as last_update
            FROM habit_logs 
            WHERE student_id = ? AND log_date >= DATE_SUB(?, INTERVAL 7 DAY)
            GROUP BY log_date
            ORDER BY log_date DESC
        ", [$studentId, $date]);
        
        $recentData = $recentQuery->getResultArray();
        
        if (empty($recentData)) {
            CLI::write("❌ No recent submissions found", 'red');
        } else {
            CLI::write("✅ Recent submissions:", 'green');
            foreach ($recentData as $entry) {
                CLI::write("   - {$entry['log_date']}: {$entry['habit_count']} habits (last: {$entry['last_update']})", 'white');
            }
        }
        
        // 4. Possible causes and solutions
        CLI::write("\n4. 🔧 Possible causes and solutions:", 'yellow');
        
        if (empty($dbData)) {
            CLI::write("❌ ISSUE: Data not saving to database", 'red');
            CLI::write("   Possible causes:", 'yellow');
            CLI::write("   - JavaScript fetch() request failing", 'white');
            CLI::write("   - Authentication/session issues", 'white');
            CLI::write("   - Server endpoint not working", 'white');
            CLI::write("   - Network connectivity problems", 'white');
            
            CLI::write("\n   💡 Solutions:", 'green');
            CLI::write("   1. Check browser console for JavaScript errors", 'white');
            CLI::write("   2. Check network tab for failed API requests", 'white');
            CLI::write("   3. Verify user is logged in properly", 'white');
            CLI::write("   4. Test API endpoint manually", 'white');
        } else {
            CLI::write("✅ Data exists in database", 'green');
            CLI::write("   Issue might be with data loading/display logic", 'yellow');
        }
        
        // 5. Debug steps
        CLI::write("\n5. 🧪 Debug steps to try:", 'cyan');
        CLI::write("   1. Open browser developer tools", 'white');
        CLI::write("   2. Go to Console tab", 'white');
        CLI::write("   3. Look for errors when saving/loading data", 'white');
        CLI::write("   4. Go to Network tab", 'white');
        CLI::write("   5. Monitor API requests when clicking save", 'white');
        CLI::write("   6. Check if POST request to /siswa/logs succeeds", 'white');
        CLI::write("   7. Verify GET request to /siswa/summary returns data", 'white');
        
        CLI::write("\n🔍 Diagnosis complete!", 'green');
    }
}
