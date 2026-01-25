<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div x-data="mapelPage()" x-init="init()" class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">Manajemen Mata Pelajaran</h1>
    <button @click="openModal()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white font-semibold shadow hover:scale-[1.02] active:scale-95">Tambah Mapel</button>
    </div>
    <div class="bg-white rounded-2xl shadow p-6 border border-slate-200">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left bg-slate-100/70">
                    <th class="p-2 rounded-l-lg">#</th>
                    <th class="p-2">Nama Mapel</th>
                    <th class="p-2">Untuk Kelas</th>
                    <th class="p-2 rounded-r-lg">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($subjects)): ?>
                <tr><td colspan="4" class="p-4 text-center text-slate-500">Belum ada data mapel.</td></tr>
                <?php endif; ?>
                <?php foreach($subjects as $i=>$s): $grades = array_filter(explode(',',$s['grades']??'')); ?>
                <tr class="border-b last:border-none">
                    <td class="p-2 text-slate-500"><?= $i+1 ?></td>
                    <td class="p-2 font-medium"><?= esc($s['name']) ?></td>
                    <td class="p-2">
                        <?php if($grades): ?>
                            <div class="flex flex-wrap gap-1">
                            <?php foreach($grades as $g): ?>
                                <span class="px-2 py-0.5 text-[11px] bg-indigo-100 text-indigo-700 rounded-full font-semibold"><?= esc($g) ?></span>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-400 italic">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-2 text-xs">
                        <div class="flex gap-2">
                            <button @click="startEdit(<?= $s['id'] ?>,'<?= esc($s['name']) ?>','<?= esc($s['grades']) ?>')" class="px-2 py-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 text-[11px] font-semibold">Edit</button>
                            <button @click="confirmDelete(<?= $s['id'] ?>,'<?= esc($s['name']) ?>')" class="px-2 py-1 rounded bg-rose-100 text-rose-700 hover:bg-rose-200 text-[11px] font-semibold">Hapus</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
        <div @click.outside="closeModal()" class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6 relative">
            <button @click="closeModal()" class="absolute top-2 right-2 text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            <h2 class="text-lg font-semibold mb-4" x-text="editId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'"></h2>
            <form @submit.prevent="submit()" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-indigo-600 mb-1">Mata Pelajaran</label>
                    <select x-model="form.name" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih --</option>
                        <?php foreach($master as $m): ?>
                        <option value="<?= esc($m) ?>" :disabled="isSubjectUsed('<?= esc($m) ?>') && form.name!=='<?= esc($m) ?>'"><?= esc($m) ?> <template x-if="isSubjectUsed('<?= esc($m) ?>') && form.name!=='<?= esc($m) ?>'">(sudah ada)</template></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-indigo-600 mb-2">Untuk Kelas</label>
                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <?php for($k=1;$k<=6;$k++): ?>
                        <label class="flex items-center gap-2 p-2 rounded-lg border hover:bg-indigo-50 cursor-pointer">
                            <input type="checkbox" :value="<?= $k ?>" x-model="form.grades" class="rounded text-indigo-600 focus:ring-indigo-500" />
                            <span>Kelas <?= $k ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3 pt-2">
                    <button type="button" @click="closeModal()" class="px-4 py-2 rounded-lg text-slate-500 hover:text-slate-700">Batal</button>
                    <div class="flex gap-3">
                        <button x-show="editId" type="button" @click="resetForm()" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200">Baru</button>
                        <button type="submit" :disabled="loading" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white font-semibold shadow disabled:opacity-40">
                            <span x-show="!loading" x-text="editId? 'Update' : 'Simpan'"></span>
                            <span x-show="loading" class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Proses...</span>
                        </button>
                    </div>
                </div>
                <p x-show="error" x-text="error" class="text-sm text-red-600"></p>
            </form>
        </div>
    </div>
</div>
<script>
function mapelPage(){
    return {
        showModal:false,
        loading:false,
        error:'',
        form:{ name:'', grades:[] },
        editId:null,
        existing:[],
        init(){},
        openModal(){ this.error=''; this.resetForm(); this.fetchExisting(); this.showModal=true; },
        closeModal(){ if(!this.loading) this.showModal=false; },
        resetForm(){ this.editId=null; this.form={ name:'', grades:[] }; },
        isSubjectUsed(name){ return this.existing.some(s=>s.name===name); },
        startEdit(id,name,grades){ this.editId=id; this.form.name=name; this.form.grades = grades? grades.split(',').map(g=>parseInt(g)) : []; this.showModal=true; this.fetchExisting(); },
        async fetchExisting(){ try { const r= await fetch('<?= base_url('admin/mapel/json') ?>'); const j= await r.json(); this.existing=j.data||[]; } catch(e){} },
        confirmDelete(id,name){ if(confirm('Hapus mapel '+name+'?')) this.deleteMapel(id); },
        async deleteMapel(id){
            this.loading=true; this.error='';
            try { const r= await fetch('<?= base_url('admin/mapel/delete') ?>/'+id,{ method:'POST', headers:{'Accept':'application/json'} }); const d= await r.json(); if(!r.ok||d.status!=='success'){ this.error=d.message||'Gagal hapus'; } else { window.location.reload(); } }
            catch(e){ this.error=e.message; }
            finally{ this.loading=false; }
        },
        async submit(){
            if(!this.form.name){ this.error='Pilih nama mata pelajaran.'; return; }
            if(this.form.grades.length===0){ this.error='Pilih minimal satu kelas.'; return; }
            this.loading=true; this.error='';
            try {
                const url = this.editId ? '<?= base_url('admin/mapel/update') ?>/'+this.editId : '<?= base_url('admin/mapel/store') ?>';
                const resp = await fetch(url,{ method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify({ name:this.form.name, grades:this.form.grades }) });
                const data = await resp.json();
                if(!resp.ok || data.status!=='success'){ this.error=data.message||'Gagal menyimpan'; }
                else { window.location.reload(); }
            } catch(e){ this.error=e.message; }
            finally { this.loading=false; }
        }
    }
}
</script>
<?= $this->endSection() ?>
