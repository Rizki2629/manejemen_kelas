<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $restricted = $restricted ?? false; ?>
<div class="space-y-8 bg-gradient-to-b from-[#F8F9FF] to-white min-h-screen -mx-4 px-4 py-4 md:py-8" x-data="adminMonthly()" x-init="init()">
    <style>
    .monthly-table {border-collapse:separate;border-spacing:0;font-size:.65rem;}
    .monthly-table th,.monthly-table td{padding:6px 6px;text-align:center;vertical-align:middle;position:relative;}
    .monthly-table thead tr.top-header th{background:linear-gradient(135deg,#d3f1ff 0%,#b9e7fb 60%,#afe0f6 100%);font-weight:700;color:#0f172a;}
    .monthly-table thead tr.sub-header th{background:#e6f7ff;font-weight:600;color:#334155;}
    .monthly-table tbody td{border-top:1px solid #e2e8f0;border-right:1px solid #eef2f7;}
    .monthly-table tbody tr:last-child td{border-bottom:1px solid #e2e8f0;}
    .monthly-table tbody tr:nth-child(even) td{background:#f8fafc;}
    .monthly-table tbody tr:nth-child(odd) td{background:#ffffff;}
    .monthly-table tbody tr:hover td{background:#f0f7ff;}
    .date-cell{font-weight:600;text-align:left;min-width:140px;font-size:.7rem;}
    .status-bar{position:relative;width:70px;height:24px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;font-weight:600;font-size:.6rem;display:flex;align-items:center;justify-content:center;color:#334155;}
    .status-bar-fill{position:absolute;inset:0;background:linear-gradient(90deg,#34d399,#10b981);width:0%;transition:width .5s ease;opacity:.18;}
    .pray-cell{font-size:.55rem;font-weight:700;cursor:default;}
    .pray-cell.done{color:#059669;background:#ecfdf5;}
    .pray-cell.miss{color:#b91c1c;}
    .worship-others{font-size:.55rem;max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:left;}
    .worship-others.has-data{background:linear-gradient(90deg,#ecfdf5,#d1fae5);color:#065f46;font-weight:500;}
    .simple-habit{font-size:.6rem;font-weight:600;}
    .simple-habit.completed{background:#dcfce7;color:#166534;}
    .simple-habit.not-completed{background:#fef2f2;color:#b91c1c;}
    .simple-habit.no-data{background:#f8fafc;color:#94a3b8;}
    .time-habit{font-size:.55rem;font-weight:600;letter-spacing:.3px;}
    .time-habit.good{background:#dcfce7;color:#166534;}
    .time-habit.bad{background:#fef2f2;color:#b91c1c;}
    .time-habit.no-data{background:#f8fafc;color:#94a3b8;}
    /* Sticky header (dua baris) */
    :root{ --monthly-top-header-h:40px; }
    .monthly-table thead th{position:sticky;z-index:25;}
    .monthly-table thead tr.top-header th{top:0; height:var(--monthly-top-header-h);}    
    /* Sub header ditempatkan tepat di bawah baris pertama tanpa celah */
    .monthly-table thead tr.sub-header th{top:var(--monthly-top-header-h);}    
    /* Pastikan header tanggal (rowspan) di atas layer lain */
    .monthly-table thead tr.top-header th.date-header{z-index:40;}
    /* Tambah bayangan halus saat scroll */
    .monthly-table thead tr.top-header th{box-shadow:0 2px 3px -1px rgba(0,0,0,.08);}    
    </style>
    <div class="text-center">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-[#6C63FF] via-[#8A6CFF] to-[#C77DFF] bg-clip-text text-transparent tracking-tight">Rekapitulasi 7 Kebiasaan Anak Indonesia Hebat</h1>
    <p class="mt-2 text-sm text-[#555]">Pilih kelas, siswa, dan bulan untuk menampilkan data (otomatis tampil tanpa tombol terapkan).</p>
    </div>
    <div class="max-w-6xl mx-auto rounded-2xl bg-white shadow-lg ring-1 ring-[#e3e6ef] p-5 md:p-7">
        <div class="grid lg:grid-cols-5 gap-6">
            <!-- Kelas -->
            <div class="space-y-1">
                <label class="block text-xs font-semibold tracking-wide text-[#6C63FF] uppercase">Kelas</label>
                <div class="relative">
                    <select x-model="filters.kelas" @change="loadStudents()" class="w-full rounded-xl border border-[#ddd] bg-white/80 text-[#212529] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#6C63FF] focus:border-[#6C63FF] transition disabled:opacity-60">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Siswa -->
            <div class="space-y-1 lg:col-span-2">
                <label class="block text-xs font-semibold tracking-wide text-[#6C63FF] uppercase">Siswa</label>
                <div class="relative">
                    <select x-model="filters.student" @change="maybeAutoLoad()" class="w-full rounded-xl border border-[#ddd] bg-white/80 text-[#212529] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#6C63FF] focus:border-[#6C63FF] transition">
                        <option value="">-- Pilih Siswa --</option>
                        <template x-for="s in students" :key="s.id">
                            <option :value="s.id" x-text="s.nama || s.Nama || ('ID:'+s.id)"></option>
                        </template>
                    </select>
                </div>
                <div class="text-[10px] mt-1 text-[#6C757D]" x-show="students.length">Total <span x-text="students.length"></span> siswa</div>
            </div>
            <!-- Bulan -->
            <div class="space-y-1">
                <label class="block text-xs font-semibold tracking-wide text-[#6C63FF] uppercase">Bulan</label>
                <input type="month" x-model="filters.month" @change="maybeAutoLoad()" class="w-full rounded-xl border border-[#ddd] bg-white/80 text-[#212529] text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#6C63FF] focus:border-[#6C63FF] transition" />
            </div>
            <!-- Tombol -->
            <div class="flex flex-col gap-3 justify-end">
                <button @click="exportExcel()" class="w-full px-4 py-2 rounded-xl font-semibold text-white text-sm tracking-wide shadow-md bg-[#00C49A] hover:bg-[#00af88] hover:scale-[1.02] active:scale-95 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00C49A] disabled:opacity-30" :disabled="!filters.student || !hasAnyData">Export Excel</button>
                <button @click="exportPdf()" class="w-full px-4 py-2 rounded-xl font-semibold text-white text-sm tracking-wide shadow-md bg-[#F97316] hover:bg-[#ea6505] hover:scale-[1.02] active:scale-95 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#fb923c] disabled:opacity-30" :disabled="!filters.student || !hasAnyData">Export PDF</button>
            </div>
        </div>
        
    </div>
    <template x-if="loading">
        <div class="rounded-2xl bg-white p-10 text-center text-slate-500 border border-slate-200 animate-pulse">Memuat data...</div>
    </template>
    <template x-if="!loading && filters.student && !hasAnyData">
        <div class="rounded-2xl bg-white p-10 text-center text-slate-500 border border-slate-200">Tidak ada data kebiasaan untuk bulan ini.</div>
    </template>
    <div x-show="!loading && filters.student" x-cloak class="rounded-2xl bg-white p-6 shadow border border-slate-200 overflow-auto">
        <h2 class="text-xl font-semibold text-center mb-6" x-text="title"></h2>
        <div class="overflow-auto max-h-[70vh] border rounded-xl">
            <table class="monthly-table w-full text-[11px] min-w-[1200px]" id="adminMonthlyTable">
                <thead>
                    <tr class="top-header">
                        <th rowspan="2" class="date-header bg-sky-100 sticky left-0 z-20">HARI TANGGAL</th>
                        <th rowspan="2" class="status-header">STATUS</th>
                        <th rowspan="2">BANGUN PAGI</th>
                        <th colspan="6" class="worship-group">BERIBADAH</th>
                        <th rowspan="2">BEROLAHRAGA</th>
                        <th rowspan="2">MAKAN SEHAT</th>
                        <th rowspan="2">GEMAR BELAJAR</th>
                        <th rowspan="2">BERMASYARAKAT</th>
                        <th rowspan="2">TIDUR CEPAT</th>
                    </tr>
                    <tr class="sub-header">
                        <th class="pray-header">S</th>
                        <th class="pray-header">D</th>
                        <th class="pray-header">A</th>
                        <th class="pray-header">M</th>
                        <th class="pray-header">I</th>
                        <th class="lainnya-header">Ibadah Lainnya</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="d in daysInMonth" :key="d">
                        <tr>
                            <td class="date-cell sticky left-0 bg-white" x-text="formatLong(d)"></td>
                            <td class="status-cell">
                                <div class="status-bar" :data-pct="statusPercent(d)">
                                    <div class="status-bar-fill" :style="'width:'+statusPercent(d)+'%'" ></div>
                                    <span class="status-text" x-text="statusText(d)"></span>
                                </div>
                            </td>
                            <td :class="bangunClass(d)" x-html="bangunContent(d)"></td>
                            <td class="pray-cell" :class="prayerClass(d,'subuh')" x-html="prayerMark(d,'subuh')"></td>
                            <td class="pray-cell" :class="prayerClass(d,'dzuhur')" x-html="prayerMark(d,'dzuhur')"></td>
                            <td class="pray-cell" :class="prayerClass(d,'ashar')" x-html="prayerMark(d,'ashar')"></td>
                            <td class="pray-cell" :class="prayerClass(d,'maghrib')" x-html="prayerMark(d,'maghrib')"></td>
                            <td class="pray-cell" :class="prayerClass(d,'isya')" x-html="prayerMark(d,'isya')"></td>
                            <td class="worship-others" :class="worshipOthersClass(d)" x-text="worshipOthers(d)"></td>
                            <td :class="simpleClass(d,3)" x-html="simpleMark(d,3)"></td>
                            <td :class="simpleClass(d,4)" x-html="simpleMark(d,4)"></td>
                            <td :class="simpleClass(d,5)" x-html="simpleMark(d,5)"></td>
                            <td :class="simpleClass(d,6)" x-html="simpleMark(d,6)"></td>
                            <td :class="tidurClass(d)" x-html="tidurContent(d)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function adminMonthly(){
    return {
        filters:{ kelas:'', student:'', month: '<?= date('Y-m') ?>' },
        students:[],
        monthly:{},
        loading:false,
        title:'',
        restricted: <?= $restricted ? 'true':'false' ?>,
    defaultKelas: <?= ($restricted && count($classes)>0)? (int)$classes[0]['id'] : 'null' ?>,
            init(){
                if(this.restricted && this.defaultKelas){
                    // Auto set class and load students immediately
                    this.filters.kelas = String(this.defaultKelas);
                    this.loadStudents();
                }
            },
            async loadStudents(){
                if(!this.filters.kelas){ this.students=[]; this.filters.student=''; return; }
                try {
                    this.loading=true;
                    console.log('Loading students for kelas:', this.filters.kelas);
                    const basePath = '<?= (session()->get('role')==='walikelas')? base_url('habits/monthly/students/') : base_url('admin/habits/monthly/students/') ?>';
                    const r=await fetch(basePath+this.filters.kelas);
                    console.log('Response status:', r.status);
                    if(!r.ok) throw new Error('Gagal memuat siswa');
                    const j=await r.json();
                    console.log('Students response:', j);
                    this.students=j.data||[]; 
                    // Auto-pilih siswa pertama untuk walikelas agar langsung tampil tanpa klik
                    if(this.restricted && this.students.length){
                        this.filters.student = String(this.students[0].id);
                        // Auto apply jika month sudah ada
                        if(this.filters.month){
                            this.apply();
                        }
                    } else {
                        this.filters.student='';
                    }
                    console.log('Students loaded:', this.students.length);
                }catch(e){ 
                    console.error('Error loading students:', e);
                    this.students=[]; 
                }
                finally{ this.loading=false; }
            },
            maybeAutoLoad(){ if(this.filters.student && this.filters.month) this.apply(); },
            async apply(){
                if(!this.filters.student) return; this.loading=true; this.monthly={};
                const qs=new URLSearchParams({month:this.filters.month, student_id:this.filters.student});
                const dataUrl = '<?= (session()->get('role')==='walikelas')? base_url('habits/monthly/data') : base_url('admin/habits/monthly/data') ?>';
                const r=await fetch(dataUrl+'?'+qs.toString());
                const j=await r.json(); this.monthly=j.data||{}; this.loading=false; this.title='Rekap Kebiasaan '+this.formatMonth(this.filters.month);
            },
            exportExcel(){ if(!this.filters.student) return; const qs=new URLSearchParams({month:this.filters.month, student_id:this.filters.student}); const exportUrl='<?= (session()->get('role')==='walikelas')? base_url('habits/monthly/export') : base_url('admin/habits/monthly/export') ?>'; window.location=exportUrl+'?'+qs.toString(); },
            exportPdf(){ if(!this.filters.student) return; const qs=new URLSearchParams({month:this.filters.month, student_id:this.filters.student}); const pdfUrl='<?= (session()->get('role')==='walikelas')? base_url('habits/monthly/export-pdf') : base_url('admin/habits/monthly/export-pdf') ?>'; window.location=pdfUrl+'?'+qs.toString(); },
            get hasAnyData(){ return Object.keys(this.monthly).length>0; },
            get daysInMonth(){ const m=this.filters.month; if(!m) return []; const [y,mo]=m.split('-'); const year=parseInt(y),month=parseInt(mo); const last=new Date(year,month,0).getDate(); const out=[]; for(let d=1; d<=last; d++){ const ds=('0'+d).slice(-2); out.push(`${y}-${mo}-${ds}`);} return out; },
    statusText(date){ const day=this.monthly[date]||{}; let done=0; for(let i=1;i<=7;i++){ const h=day['habit_'+i]; if(h&&h.completed) done++; } return done+'/7'; },
        statusPercent(date){ const day=this.monthly[date]||{}; let done=0; for(let i=1;i<=7;i++){ const h=day['habit_'+i]; if(h&&h.completed) done++; } return Math.round(done/7*100); },
        isFuture(date){ const today=new Date(); const d=new Date(date); d.setHours(0,0,0,0); today.setHours(0,0,0,0); return d>today; },
        isPast(date){ const today=new Date(); const d=new Date(date); d.setHours(0,0,0,0); today.setHours(0,0,0,0); return d<today; },
        redX(){ return `<span class='miss-x' style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:9999px;background:#fecaca;color:#b91c1c;font-size:11px;font-weight:600;">×</span>`; },
        bangunClass(date){ if(this.isFuture(date)) return 'time-habit no-data'; const h=this.monthly[date]?.habit_1; if(!h||!h.time) return 'time-habit no-data'; return h.time<='06:00'?'time-habit good':'time-habit bad'; },
        bangunContent(date){ if(this.isFuture(date)) return ''; const h=this.monthly[date]?.habit_1; if(!h||!h.time) return this.isPast(date)? this.redX():''; return h.time<='06:00'? `<span>${h.time}</span>`:`<span style='color:#dc2626;'>${h.time}</span>`; },
        tidurClass(date){ if(this.isFuture(date)) return 'time-habit no-data'; const h=this.monthly[date]?.habit_7; if(!h||!h.time) return 'time-habit no-data'; return h.time<='21:00'?'time-habit good':'time-habit bad'; },
        tidurContent(date){ if(this.isFuture(date)) return ''; const h=this.monthly[date]?.habit_7; if(!h||!h.time) return this.isPast(date)? this.redX():''; return h.time<='21:00'? `<span>${h.time}</span>`:`<span style='color:#dc2626;'>${h.time}</span>`; },
        prayerMark(date,pr){ if(this.isFuture(date)) return ''; const h=this.monthly[date]?.habit_2; if(!h||!h.notes){ return this.isPast(date)? this.redX():''; } const txt=h.notes.toLowerCase(); if(txt.includes(pr)) return '✓'; return this.isPast(date)? this.redX():''; },
        prayerClass(date,pr){ if(this.isFuture(date)) return 'pray-cell'; const h=this.monthly[date]?.habit_2; const txt=(h?.notes||'').toLowerCase(); if(txt.includes(pr)) return 'pray-cell done'; return 'pray-cell '+(this.isPast(date)?'miss':''); },
        worshipOthers(date){ if(this.isFuture(date)) return ''; const h=this.monthly[date]?.habit_2; if(!h||!h.notes) return ''; let txt=h.notes; ['subuh','dzuhur','ashar','maghrib','isya'].forEach(p=>{ txt=txt.replace(new RegExp(p,'ig'),'');}); txt=txt.replace(/sholat:?/ig,''); txt=txt.split(',').map(s=>s.trim()).filter(Boolean).join(', '); txt=txt.replace(/\s+/g,' ').trim(); return txt; },
        worshipOthersClass(date){ if(this.isFuture(date)) return 'worship-others no-data'; const c=this.worshipOthers(date); return c? 'worship-others has-data':'worship-others no-data'; },
        simpleMark(date,id){ if(this.isFuture(date)) return ''; const h=this.monthly[date]?.['habit_'+id]; if(!h) return this.isPast(date)? this.redX():''; let notes=(h.notes||'').trim(); notes=notes.replace(/\s*,\s*/g,', ').replace(/\s{2,}/g,' '); let dur=''; if(h.duration){ const num=parseFloat(h.duration); if(!isNaN(num)) dur=(Number.isInteger(num)? num: num.toFixed(1))+' menit'; } let combined=''; if(notes&&dur) combined=notes+' ('+dur+')'; else if(notes) combined=notes; else if(dur) combined=dur; if(combined){ if(combined.length>50) combined=combined.substring(0,47)+'…'; return combined; } if(h.completed) return '✓'; return this.isPast(date)? this.redX():'✗'; },
        simpleClass(date,id){ if(this.isFuture(date)) return 'simple-habit no-data'; const h=this.monthly[date]?.['habit_'+id]; if(!h) return 'simple-habit no-data'; return 'simple-habit '+(h.completed?'completed':'not-completed'); },
        formatLong(d){ const dt=new Date(d); const hari=['Minggu','Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu']; const bulan=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; return hari[dt.getDay()]+' '+dt.getDate()+' '+bulan[dt.getMonth()]+' '+dt.getFullYear(); },
        formatMonth(m){ const [y,mo]=m.split('-'); const bulan=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; return bulan[parseInt(mo)-1]+' '+y; }
    }
}
</script>
<?= $this->endSection() ?>
