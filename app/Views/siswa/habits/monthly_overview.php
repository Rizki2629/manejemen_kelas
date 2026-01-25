<!-- Monthly Overview Component -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
  
  <!-- Monthly Calendar Heatmap -->
  <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-900">Kalender Bulanan</h3>
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <span>Agustus 2025</span>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
        </svg>
      </div>
    </div>
    
    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1 mb-4">
      <!-- Day Headers -->
      <?php 
      $dayHeaders = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
      foreach($dayHeaders as $day): ?>
        <div class="text-center text-xs font-medium text-gray-500 py-2"><?= $day ?></div>
      <?php endforeach; ?>
      
      <!-- Calendar Days -->
      <?php for($i = 1; $i <= 31; $i++): 
        $completionRate = rand(0, 100);
        $colorClass = '';
        if($completionRate >= 80) $colorClass = 'bg-green-500';
        elseif($completionRate >= 60) $colorClass = 'bg-green-300';
        elseif($completionRate >= 40) $colorClass = 'bg-yellow-300';
        elseif($completionRate >= 20) $colorClass = 'bg-orange-300';
        else $colorClass = 'bg-gray-200';
      ?>
        <div class="aspect-square <?= $colorClass ?> rounded-lg flex items-center justify-center text-xs font-medium text-gray-700 hover:scale-110 transition-transform cursor-pointer"
             title="<?= $i ?> Agustus - <?= $completionRate ?>% completed">
          <?= $i ?>
        </div>
      <?php endfor; ?>
    </div>
    
    <!-- Legend -->
    <div class="flex items-center justify-between text-xs text-gray-600">
      <span>Rendah</span>
      <div class="flex items-center gap-1">
        <div class="w-3 h-3 bg-gray-200 rounded"></div>
        <div class="w-3 h-3 bg-orange-300 rounded"></div>
        <div class="w-3 h-3 bg-yellow-300 rounded"></div>
        <div class="w-3 h-3 bg-green-300 rounded"></div>
        <div class="w-3 h-3 bg-green-500 rounded"></div>
      </div>
      <span>Tinggi</span>
    </div>
  </div>

  <!-- Habit Completion Stats -->
  <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Statistik Kebiasaan</h3>
    </div>
    
    <!-- Progress Rings -->
    <div class="space-y-6">
      <?php 
      $habits = [
        ['name' => 'Bangun Pagi', 'completion' => 85, 'color' => 'text-orange-500', 'bg' => 'stroke-orange-500'],
        ['name' => 'Beribadah', 'completion' => 92, 'color' => 'text-purple-500', 'bg' => 'stroke-purple-500'],
        ['name' => 'Berolahraga', 'completion' => 76, 'color' => 'text-red-500', 'bg' => 'stroke-red-500'],
        ['name' => 'Makan Sehat', 'completion' => 68, 'color' => 'text-green-500', 'bg' => 'stroke-green-500'],
        ['name' => 'Bermasyarakat', 'completion' => 82, 'color' => 'text-blue-500', 'bg' => 'stroke-blue-500']
      ];
      
      foreach($habits as $habit): 
        $circumference = 2 * 3.14159 * 20; // 2πr where r=20
        $strokeDasharray = ($habit['completion'] / 100) * $circumference;
      ?>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="relative w-12 h-12">
              <svg class="w-12 h-12 transform -rotate-90">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="3" fill="none" class="text-gray-200"></circle>
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="3" fill="none" 
                        class="<?= $habit['bg'] ?>" 
                        style="stroke-dasharray: <?= $strokeDasharray ?> <?= $circumference ?>; stroke-linecap: round;"></circle>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-xs font-bold <?= $habit['color'] ?>"><?= $habit['completion'] ?>%</span>
              </div>
            </div>
            <span class="font-medium text-gray-700"><?= $habit['name'] ?></span>
          </div>
          <div class="text-right">
            <div class="text-sm font-semibold text-gray-900"><?= floor($habit['completion'] * 30 / 100) ?>/30</div>
            <div class="text-xs text-gray-500">hari bulan ini</div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
