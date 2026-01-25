<!-- Weekly Progress Chart Component -->
<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm" x-data="weeklyChart()" x-init="initChart()">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-900">Progress Mingguan</h3>
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <span>7 hari terakhir</span>
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
      </svg>
    </div>
  </div>
  
  <div class="relative h-64">
    <canvas id="weeklyProgressChart" class="w-full h-full"></canvas>
  </div>
  
  <!-- Legend -->
  <div class="flex items-center justify-center gap-6 mt-4 text-sm">
    <div class="flex items-center gap-2">
      <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
      <span class="text-gray-600">Target</span>
    </div>
    <div class="flex items-center gap-2">
      <div class="w-3 h-3 rounded-full bg-green-500"></div>
      <span class="text-gray-600">Tercapai</span>
    </div>
  </div>
</div>

<script>
function weeklyChart() {
  return {
    chart: null,
    
    initChart() {
      const ctx = document.getElementById('weeklyProgressChart');
      if (!ctx) return;
      
      const chartContext = ctx.getContext('2d');
      
      // Sample data - consistent values
      const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
      const targetData = [7, 7, 7, 7, 7, 7, 7]; // Total habits per day
      const completedData = [6, 7, 5, 7, 6, 6, 7]; // Completed habits per day
      
      this.chart = new Chart(chartContext, {
        type: 'bar',
        data: {
          labels: days,
          datasets: [
            {
              label: 'Target',
              data: targetData,
              backgroundColor: 'rgba(99, 102, 241, 0.2)',
              borderColor: 'rgb(99, 102, 241)',
              borderWidth: 2,
              borderRadius: 6,
              borderSkipped: false,
            },
            {
              label: 'Tercapai',
              data: completedData,
              backgroundColor: 'rgba(34, 197, 94, 0.8)',
              borderColor: 'rgb(34, 197, 94)',
              borderWidth: 2,
              borderRadius: 6,
              borderSkipped: false,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: 'white',
              bodyColor: 'white',
              borderColor: 'rgba(255, 255, 255, 0.1)',
              borderWidth: 1,
              cornerRadius: 8,
              callbacks: {
                title: function(context) {
                  return 'Hari ' + context[0].label;
                },
                label: function(context) {
                  if (context.datasetIndex === 0) {
                    return 'Target: ' + context.parsed.y + ' kebiasaan';
                  } else {
                    return 'Tercapai: ' + context.parsed.y + ' kebiasaan';
                  }
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#6B7280',
                font: {
                  size: 12,
                  weight: '500'
                }
              }
            },
            y: {
              beginAtZero: true,
              max: 8,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                color: '#6B7280',
                font: {
                  size: 11
                },
                stepSize: 1
              }
            }
          },
          interaction: {
            intersect: false,
            mode: 'index'
          },
          animation: {
            duration: 1000,
            easing: 'easeOutQuart'
          }
        }
      });
    },
    
    updateChart(newData) {
      if (this.chart) {
        this.chart.data.datasets[1].data = newData;
        this.chart.update('active');
      }
    }
  };
}
</script>
