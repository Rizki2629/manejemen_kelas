<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<!-- Ensure Alpine is available for x-data/x-init bindings on this page only -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<div x-data="guruDash()" x-init="init()" class="space-y-6">
  <div class="rounded-2xl p-4 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 shadow-lg" data-aos="fade-up" data-aos-duration="600">
    <div class="grid md:grid-cols-4 gap-3 items-end">
      <div>
        <label class="block text-sm mb-1 text-indigo-700 font-medium">Kelas</label>
        <select x-model="filters.classId" class="w-full px-3 py-2 rounded-lg bg-white border border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <option value="">Semua</option>
          <?php foreach($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= esc($c['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm mb-1 text-fuchsia-700 font-medium">Dari</label>
        <input type="date" x-model="filters.from" value="<?= esc($weekAgo) ?>" class="w-full px-3 py-2 rounded-lg bg-white border border-fuchsia-200 focus:outline-none focus:ring-2 focus:ring-fuchsia-500" />
      </div>
      <div>
        <label class="block text-sm mb-1 text-rose-700 font-medium">Sampai</label>
        <input type="date" x-model="filters.to" value="<?= esc($today) ?>" class="w-full px-3 py-2 rounded-lg bg-white border border-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500" />
      </div>
      <div>
        <button @click="refresh()" class="w-full px-4 py-2 rounded-xl text-white hover:shadow-xl transition-all bg-gradient-to-r from-indigo-600 via-fuchsia-500 to-rose-500">Terapkan</button>
      </div>
    </div>
  </div>

  <!-- Metric cards placeholder -->
  <div class="grid md:grid-cols-3 gap-4" data-aos="fade-up" data-aos-duration="600">
    <template x-for="(m, i) in metrics" :key="m.title">
      <div class="rounded-2xl p-4 border shadow-lg hover:shadow-xl transition-all"
           :class="[
              i===0 ? 'bg-gradient-to-br from-emerald-50 to-white border-emerald-200' : '',
              i===1 ? 'bg-gradient-to-br from-indigo-50 to-white border-indigo-200'  : '',
              i===2 ? 'bg-gradient-to-br from-amber-50 to-white border-amber-200'    : ''
           ]">
        <div class="flex items-center gap-2 text-sm text-slate-600">
          <span class="inline-flex w-7 h-7 items-center justify-center rounded-lg bg-white/70 ring-1 ring-black/5">
            <i class="fa-solid" :class="{
              'fa-face-smile text-emerald-600': i===0,
              'fa-trophy text-indigo-600': i===1,
              'fa-triangle-exclamation text-amber-600': i===2
            }"></i>
          </span>
          <span x-text="m.title"></span>
        </div>
        <div class="text-2xl font-semibold mt-1 bg-gradient-to-r from-indigo-600 via-fuchsia-600 to-rose-600 bg-clip-text text-transparent" x-text="m.value + '%'">
        </div>
      </div>
    </template>
  </div>

  <div class="rounded-2xl p-4 bg-gradient-to-br from-fuchsia-50 to-white border border-fuchsia-200 shadow-lg" data-aos="fade-up" data-aos-duration="600">
    <div class="flex items-center justify-between">
      <div class="font-medium text-fuchsia-700">Tren 7 Hari</div>
    </div>
    <div x-ref="chartWrap" class="mt-3">
      <canvas id="lineChart" height="100"></canvas>
    </div>
  </div>
</div>

<script>
function guruDash(){
  return {
    filters: { classId: '', from: '<?= esc($weekAgo) ?>', to: '<?= esc($today) ?>' },
    metrics: [
      { title: 'Rata-rata Kepatuhan', value: 0 },
      { title: 'Kepatuhan Terbaik', value: 0 },
      { title: 'Kepatuhan Terendah', value: 0 },
    ],
    chart: null,
    async init(){
      await this.refresh();
      // Lazy load Chart.js
      if (!window.Chart) {
        await new Promise((resolve)=>{
          const s=document.createElement('script'); s.src='https://cdn.jsdelivr.net/npm/chart.js'; s.onload=resolve; document.body.appendChild(s);
        });
      }
      this.renderChart();
    },
    async refresh(){
      const qs = new URLSearchParams(this.filters).toString();
      const res = await fetch(`/guru/stats.json?${qs}`);
      const data = await res.json();
      this._data = data.data || [];
      this.updateMetrics();
      if (this.chart) this.renderChart();
    },
    updateMetrics(){
      // simple: overall yes/total
      let yes=0, total=0;
      this._data.forEach(r=>{ yes += Number(r.yes_count||0); total += Number(r.total||0); });
      const pct = total? Math.round((yes/total)*100):0;
      this.metrics[0].value = pct;
      this.metrics[1].value = Math.min(100, pct+10);
      this.metrics[2].value = Math.max(0, pct-10);
    },
    renderChart(){
      const byDate = {};
      this._data.forEach(r=>{
        const d = r.log_date; const p = r.total? (r.yes_count/r.total)*100:0;
        byDate[d] = (byDate[d]||[]); byDate[d].push(p);
      });
      const labels = Object.keys(byDate).sort();
      const values = labels.map(d=>{
        const arr = byDate[d]; return Math.round(arr.reduce((a,b)=>a+b,0)/arr.length);
      });
      const ctx = document.getElementById('lineChart').getContext('2d');
      if(this.chart) this.chart.destroy();
      this.chart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label:'% Kepatuhan', data: values, borderColor:'#ec4899', backgroundColor:'rgba(236,72,153,0.2)', tension: 0.35, fill: true }] },
        options: { responsive:true, scales: { y: { beginAtZero:true, max:100 } } }
      });
    }
  }
}
</script>
<?= $this->endSection() ?>
