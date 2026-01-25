<?php $isStudentView = ($role==='siswa' || !empty($asSiswa)); ?>
<?= $this->extend($isStudentView ? 'layouts/siswa_layout' : 'admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-[1600px]">
    <style>
        /* lightweight extras for redesigned student view */
        .card-shadow{box-shadow:0 10px 25px rgba(0,0,0,.07)}
        .gradient-soft{background:linear-gradient(135deg,#eff6ff 0%,#faf5ff 55%,#ecfdf5 100%)}
        .timer-gradient{background:linear-gradient(135deg,#3B82F6 0%,#8B5CF6 100%)}
    </style>
    <div class="grid lg:grid-cols-12 gap-10" x-data="assignmentPlayer()" x-init="init()">
        <div class="lg:col-span-9">
    <?php if(!empty($asSiswa)): ?>
    <div class="mb-4 p-3 rounded-xl bg-blue-50 border border-blue-100 text-[12px] text-blue-700 flex items-center gap-2">
        <i class="fas fa-user-graduate text-xs"></i>
        <span>Mode pratinjau sebagai siswa. <a href="/classroom/assignments/<?= $assignment['id'] ?>" class="underline font-medium">Kembali ke tampilan guru</a></span>
    </div>
    <?php endif; ?>
    <div class="mb-6 flex items-center justify-between">
        <a href="/classroom/assignments" class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-500 font-medium"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide <?= $assignment['visibility']==='published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
            <?= strtoupper($assignment['visibility']) ?>
        </span>
     </div>
    <!-- Redesigned Header Card (student focus) -->
    <div class="bg-white/90 backdrop-blur rounded-2xl border border-gray-100 card-shadow p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 tracking-tight mb-3 flex items-center gap-3">
                    <span><?= esc($assignment['judul']) ?></span>
                    <?php if($role==='siswa' && !empty($attempt) && empty($alreadySubmitted)): ?>
                        <span class="text-[10px] px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-semibold">LANJUTKAN</span>
                    <?php endif; ?>
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
                    <span class="inline-flex items-center gap-1"><i class="fas fa-layer-group"></i> Kelas <?= esc($assignment['kelas']) ?></span>
                    <?php if($assignment['due_at']): ?><span class="inline-flex items-center gap-1 <?= strtotime($assignment['due_at']) < time() ? 'text-red-600 font-semibold' : '' ?>"><i class="fas fa-clock"></i> Deadline: <?= esc($assignment['due_at']) ?></span><?php endif; ?>
                    <span class="inline-flex items-center gap-1 <?= $assignment['allow_late']? 'text-amber-600' : 'text-gray-400' ?>"><i class="fas fa-hourglass-half"></i> Late <?= $assignment['allow_late']? 'Diizinkan' : 'Tidak' ?></span>
                    <?php if(!empty($assignment['work_duration_minutes'])): ?><span class="inline-flex items-center gap-1 text-green-600"><i class="fas fa-stopwatch"></i> Durasi <?= (int)$assignment['work_duration_minutes'] ?> menit</span><?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide <?= $assignment['visibility']==='published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                    <?= strtoupper($assignment['visibility']) ?>
                </span>
                <?php if($role!=='siswa'): ?>
                    <a href="/classroom/assignments/<?= $assignment['id'] ?>/submissions" class="text-[11px] text-purple-600 hover:underline font-medium">Submission &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- /Header Card -->
    <div class="prose max-w-none bg-white/90 backdrop-blur p-6 rounded-2xl border border-gray-100 shadow-sm">
        <?= $assignment['deskripsi_html'] ?>
    </div>
    <?php if(isset($assignment['questions_json']) && $assignment['questions_json']): ?>
        <?php $qs = json_decode($assignment['questions_json'], true) ?: []; ?>
        <?php if($qs): ?>
            <div class="mt-10">
                <?php if($role==='siswa' && !empty($assignment['work_duration_minutes'])): ?>
                <div x-show="started" class="mb-8">
                    <div class="grid md:grid-cols-3 gap-6 w-full items-stretch">
                        <div class="sm:col-span-1 timer-gradient rounded-2xl card-shadow p-6 text-white text-center flex flex-col justify-center">
                            <h2 class="text-base font-semibold mb-2 tracking-wide">Waktu Tersisa</h2>
                            <div class="text-5xl font-extrabold tabular-nums mb-1" :class="remaining <=300 ? 'text-red-200 animate-pulse' : ''" x-text="formattedTime"></div>
                            <p class="text-xs text-white/70">Menit : Detik</p>
                        </div>
                        <div class="md:col-span-2 flex flex-col justify-center" x-show="questionsTotal>0">
                            <div class="bg-white rounded-2xl card-shadow p-6">
                                <div class="flex items-center justify-between mb-3 text-sm">
                                    <span class="font-semibold text-gray-700">Progress Ujian</span>
                                    <span class="text-purple-600 font-bold" x-text="answeredCount + ' dari ' + questionsTotal + ' soal'"></span>
                                </div>
                                <div class="w-full bg-gray-200/70 rounded-full h-3 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-500" :style="'width:'+progressPercent+'%'" ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-sm uppercase tracking-wide text-gray-600">Daftar Soal</h3>
                </div>
                <?php if($role==='siswa'): ?>
        <div x-show="!started" class="p-6 rounded-xl border border-purple-200 bg-purple-50/60 flex flex-col items-center gap-4">
            <?php if(!empty($alreadySubmitted)): ?>
                <p class="text-sm text-purple-700 font-medium flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Kamu telah menyelesaikan tugas ini.</p>
                <button disabled class="inline-flex items-center gap-2 rounded-lg bg-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-600 cursor-not-allowed"><i class="fas fa-lock text-xs"></i> Selesai</button>
            <?php else: ?>
                <p class="text-sm text-purple-700 font-medium">Tekan tombol untuk <?php if(!empty($attempt)) echo 'melanjutkan'; else echo 'memulai'; ?> pengerjaan.</p>
                <button @click.prevent="start()" :disabled="starting" :class="starting ? 'opacity-60 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-2.5 text-sm font-semibold text-white shadow hover:shadow-md"><i class="fas fa-play text-xs"></i> <span x-text="starting ? 'Memulai...' : '<?= !empty($attempt)?'Lanjutkan':'Kerjakan' ?>'"></span></button>
            <?php endif; ?>
        </div>
                <?php endif; ?>
                <form id="quickSubmitForm" class="space-y-6" onsubmit="return false;" x-show="started && !alreadySubmitted" x-cloak>
                <!-- Single Question Container -->
                <template x-for="(q,i) in questionsMeta" :key="q.uid">
                    <div x-show="activeQuestion===i+1" class="bg-white rounded-2xl card-shadow p-6">
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4 flex-wrap">
                                <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold text-[11px]">
                                    Soal <span x-text="(i+1)+' dari '+questionsTotal"></span>
                                </div>
                                <template x-if="q.topic">
                                    <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-semibold text-[11px]" x-text="q.topic"></div>
                                </template>
                                <div class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-semibold text-[11px]" x-text="q.type==='kuis' ? 'PG' : 'Isian'"></div>
                                <template x-if="flaggedSet.has(q.uid)">
                                    <div class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-semibold text-[11px]">Ditandai</div>
                                </template>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold text-gray-800 leading-relaxed" x-html="q.text"></h3>
                        </div>
                        <!-- Question Body -->
                        <div class="space-y-3" x-data>
                            <!-- Multiple Choice -->
                            <template x-if="q.type==='kuis'">
                                <ul class="space-y-3">
                                    <template x-for="opt in q.options" :key="opt.key">
                                        <li>
                                            <label class="flex items-start p-4 bg-gray-50 hover:bg-blue-50 border-2 border-transparent hover:border-blue-200 rounded-xl cursor-pointer transition option-button">
                                                <input class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" type="radio" :name="'q'+q.uid" :value="opt.key">
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm font-medium text-gray-700" x-text="opt.key+'. '+opt.text"></p>
                                                    <template x-if="opt.image"><img :src="'/classroom/assignments/question-image/'+opt.image" class="mt-2 max-h-40 rounded border shadow-sm object-contain zoom-img" /></template>
                                                </div>
                                            </label>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <!-- Essay -->
                            <template x-if="q.type!=='kuis'">
                                <div class="answer-wrapper">
                                    <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-500 mb-1">Jawaban Anda</label>
                                    <textarea :name="'q'+q.uid" rows="5" class="w-full rounded-xl border border-purple-300/60 bg-purple-50/50 px-4 py-3 text-sm leading-relaxed focus:border-purple-500 focus:ring-2 focus:ring-purple-300 outline-none"></textarea>
                                    <div class="mt-1 flex items-center justify-between text-[10px] text-gray-500">
                                        <span>Tulis jawaban singkat & jelas.</span>
                                        <button type="button" class="clear-answer px-2 py-0.5 rounded bg-purple-100 text-purple-600 hover:bg-purple-200 text-[10px] font-medium">Bersihkan</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Navigation Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between" x-show="!submitted">
                    <button type="button" @click="prev()" :disabled="activeQuestion===1" class="disabled:opacity-40 disabled:cursor-not-allowed bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all">
                        ← Soal Sebelumnya
                    </button>
                    <div class="flex gap-3">
                        <button type="button" @click="toggleFlag(); next()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-xl transition-all">
                            📝 Tandai & Lanjut
                        </button>
                        <button type="button" @click="next()" :disabled="activeQuestion===questionsTotal" class="bg-blue-500 hover:bg-blue-600 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-xl transition-all">
                            Soal Selanjutnya →
                        </button>
                    </div>
                </div>

                <!-- Question Navigation Grid -->
                <div class="bg-white rounded-2xl card-shadow p-6 mt-6" x-show="!submitted">
                    <h4 class="text-sm font-bold text-gray-700 mb-4">Navigasi Soal</h4>
                    <div class="grid grid-cols-5 sm:grid-cols-10 gap-3">
                        <template x-for="(q,i) in questionsMeta" :key="q.uid">
                            <button type="button" @click="goTo(i+1)" :class="navButtonClass(i+1,q.uid)" class="w-10 h-10 rounded-lg font-semibold text-sm flex items-center justify-center transition-colors"></button>
                        </template>
                    </div>
                    <div class="flex flex-wrap gap-4 mt-4 text-[11px]">
                        <div class="flex items-center gap-2"><span class="w-4 h-4 bg-green-500 rounded"></span><span class="text-gray-600">Sudah dijawab</span></div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 bg-blue-500 rounded"></span><span class="text-gray-600">Sedang dikerjakan</span></div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 bg-gray-200 rounded border border-gray-300"></span><span class="text-gray-600">Belum dijawab</span></div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 bg-yellow-400 rounded"></span><span class="text-gray-600">Ditandai</span></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center pt-8" x-show="!submitted">
                    <span id="quickSubmitStatus" class="block text-sm text-gray-500 mb-3"></span>
                    <button id="quickSubmitBtn" type="button" @click="finalSubmit()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-xl text-sm sm:text-base transition shadow-md hover:shadow-lg">
                        🎯 Selesai & Kirim Jawaban
                    </button>
                </div>

                <div class="mt-8" x-show="submitted" x-cloak>
                    <div class="p-6 rounded-xl border border-green-200 bg-green-50 flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-700 mb-1">Jawaban terkirim.</p>
                            <p class="text-xs text-green-600">Anda sudah men-submit latihan ini. Waktu dihentikan.</p>
                        </div>
                    </div>
                </div>
                </form>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($attachments)): ?>
    <div class="mt-8">
        <h3 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-600">Lampiran</h3>
        <div class="grid sm:grid-cols-2 gap-3">
            <?php foreach($attachments as $att): ?>
            <a class="group flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-white hover:border-purple-300 hover:bg-purple-50/50 transition" href="/classroom/attachment/<?= $att['id'] ?>/download">
                <div class="w-10 h-10 flex-shrink-0 bg-gradient-to-br from-pink-500 to-purple-500 text-white rounded-lg flex items-center justify-center shadow">
                    <i class="fas fa-file text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate group-hover:text-purple-700"><?= esc($att['original_name']) ?></p>
                </div>
                <i class="fas fa-download text-xs text-purple-400 group-hover:text-purple-600"></i>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
     <?php if (in_array($role,['guru','walikelas','admin'])): ?>
        <div class="mt-10 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100 rounded-2xl p-5">
            <h3 class="font-semibold mb-3 text-sm text-purple-700 uppercase tracking-wide">Manajemen Submission</h3>
            <a href="/classroom/assignments/<?= $assignment['id'] ?>/submissions" class="inline-flex items-center gap-1 text-purple-600 font-medium hover:underline"><i class="fas fa-list text-xs"></i> Lihat Semua Submission</a>
        </div>
    <?php endif; ?>
        <?php if (!empty($attachments)): ?>
    </div>
        <?php endif; ?>
    <div class="lg:col-span-3 space-y-6 self-start lg:sticky lg:top-28" x-data>
            <?php if (in_array($role,['guru','walikelas','admin'])): ?>
            <div class="bg-white/90 backdrop-blur rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-600 flex items-center gap-2"><i class="fas fa-user-check text-purple-500"></i> Siswa Mengumpulkan</h3>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 font-semibold"><?= (int)($assignment['submission_count'] ?? 0) ?></span>
                </div>
                <?php if (empty($submissions)): ?>
                    <p class="text-xs text-gray-400">Belum ada submission.</p>
                <?php else: ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach($submissions as $sub): $nm = trim($sub['student_name'] ?? 'Siswa'); ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide bg-green-100 text-green-700 shadow-sm"><i class="fas fa-user text-[10px] mr-1 opacity-70"></i><?= esc($nm) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="mt-4 text-right">
                    <a href="/classroom/assignments/<?= $assignment['id'] ?>/submissions" class="text-[11px] font-medium text-purple-600 hover:underline">Detail Submission &raquo;</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function assignmentPlayer(){
        return {
                // Always require explicit click for siswa to start/continue (even if attempt exists)
                started: <?= ($role!=='siswa') ? 'true' : 'false' ?>,
                starting: false,
                remaining: <?= ($role==='siswa' && !empty($assignment['work_duration_minutes'])) ? ((int)($attempt['remaining_seconds'] ?? ($assignment['work_duration_minutes']*60))) : 'null' ?>,
                questionsTotal: <?= isset($qs)?count($qs):0 ?>,
                answered: {},
                answeredCount: 0,
                activeQuestion: 1,
                questionsMeta: <?= json_encode(array_map(function($q){return [
                    'uid'=>$q['uid'],
                    'type'=>$q['type'],
                    'topic'=>$q['topic']??null,
                    'text'=>$q['text']??'',
                    'options'=>($q['options']??[])
                ];}, $qs ?? [])) ?>,
                flaggedSet: new Set(),
                timerInt: null,
                submitted: false,
                alreadySubmitted: <?= !empty($alreadySubmitted)?'true':'false' ?>,
                get formattedTime(){ if(this.remaining===null) return ''; const m=Math.floor(this.remaining/60); const s=this.remaining%60; return String(m).padStart(2,'0')+':' + String(s).padStart(2,'0'); },
                get progressPercent(){ if(!this.questionsTotal) return 0; return Math.round((this.answeredCount/this.questionsTotal)*100); },
                init(){
                    if(this.alreadySubmitted){
                        this.submitted=true; this.started=false; return;
                    }
                    // Do not auto-start even if attempt exists; student must click button
                    this.computeAnswered();
                },
        start(){
            if(this.started || this.starting) return;
            this.starting=true;
            fetch('/classroom/assignments/<?= $assignment['id'] ?>/start',{
                method:'POST',
                headers:{'Accept':'application/json'},
                credentials:'same-origin'
            }).then(r=>r.json()).then(j=>{
                if(j.success){
                    this.started=true;
                    if(j.remaining_seconds!==null){ this.remaining=j.remaining_seconds; this.startTimer(); }
                    restoreAnswers(j.answers);
                            this.$nextTick(()=>this.computeAnswered());
                } else {
                    alert(j.error || 'Gagal memulai');
                }
            }).catch(()=>alert('Koneksi gagal')).finally(()=>{ this.starting=false; });
        },
                startTimer(){ if(this.remaining===null) return; this.timerInt = setInterval(()=>{ if(this.remaining>0){ this.remaining--; if(this.remaining%15===0) this.autoSave(); } else { clearInterval(this.timerInt); this.autoSave(true); } },1000); },
                autoSave(expired=false){ if(<?= $role==='siswa'?'true':'false' ?> && !this.submitted){ doSave(expired?0:this.remaining); } },
                finalSubmit(){
                    if(this.submitted || this.alreadySubmitted) return; this.autoSave();
                    const btn = document.getElementById('quickSubmitBtn');
                    const statusEl = document.getElementById('quickSubmitStatus');
                    if(btn){ btn.disabled=true; btn.classList.add('opacity-60'); }
                    statusEl.textContent='Mengirim...';
                    fetch('/classroom/assignments/<?= $assignment['id'] ?>/submit-attempt',{method:'POST',headers:{'Accept':'application/json'},credentials:'same-origin'})
                        .then(r=>r.json()).then(j=>{
                            if(j.success){
                                this.submitted=true; if(this.timerInt) clearInterval(this.timerInt); this.remaining=0; statusEl.textContent='Terkirim';
                            } else { statusEl.textContent=j.error||'Gagal submit'; if(btn){ btn.disabled=false; btn.classList.remove('opacity-60'); } }
                        }).catch(()=>{ statusEl.textContent='Koneksi gagal'; if(btn){ btn.disabled=false; btn.classList.remove('opacity-60'); } });
                },
                saveNow(){ this.autoSave(); },
                computeAnswered(){
                    const form=document.getElementById('quickSubmitForm');
                    if(!form){ this.answeredCount=0; return; }
                    let answered=0; const handled={};
                    form.querySelectorAll('[name^="q"]').forEach(el=>{
                        if(el.type==='radio'){
                            if(handled[el.name]) return; handled[el.name]=true;
                            const grp=[...form.querySelectorAll('input[type=radio][name="'+el.name+'"]')];
                            if(grp.some(r=>r.checked)) answered++;
                        } else if(el.tagName==='TEXTAREA') {
                            if(el.value.trim()!=='') answered++;
                        }
                    });
                    this.answeredCount=answered;
                },
                scanAnswers(){ this.computeAnswered(); },
                scrollToQuestion(i){ },
                goTo(i){ if(i>=1 && i<=this.questionsTotal){ this.activeQuestion=i; } },
                next(){ if(this.activeQuestion < this.questionsTotal){ this.activeQuestion++; } },
                prev(){ if(this.activeQuestion > 1){ this.activeQuestion--; } },
                toggleFlag(){ const q=this.questionsMeta[this.activeQuestion-1]; if(!q) return; if(this.flaggedSet.has(q.uid)) this.flaggedSet.delete(q.uid); else this.flaggedSet.add(q.uid); },
                navButtonClass(i,uid){
                    const form=document.getElementById('quickSubmitForm');
                    let answered=false;
                    if(form){
                        const radios=[...form.querySelectorAll('input[type=radio][name="q'+uid+'"]')];
                        if(radios.length){ answered = radios.some(r=>r.checked); }
                        else { const ta=form.querySelector('textarea[name="q'+uid+'"]'); if(ta && ta.value.trim()!=='') answered=true; }
                    }
                    const isActive=this.activeQuestion===i;
                    const flagged=this.flaggedSet.has(uid);
                    return [
                        'w-10 h-10 rounded-lg font-semibold text-sm flex items-center justify-center',
                        answered? 'bg-green-500 text-white hover:bg-green-600': 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                        isActive? 'ring-2 ring-blue-400 ring-offset-2' : '',
                        !answered && isActive? 'bg-blue-500 text-white hover:bg-blue-600':'' ,
                        flagged? 'relative after:absolute after:-top-1 after:-right-1 after:w-3 after:h-3 after:bg-yellow-400 after:rounded-full after:ring-2 after:ring-white':''
                    ].join(' ');
                }
        }
}
document.addEventListener('DOMContentLoaded',()=>{
  const form = document.getElementById('quickSubmitForm');
  const btn = document.getElementById('quickSubmitBtn');
    if(!form) return;
    const statusEl = document.getElementById('quickSubmitStatus');
    window.restoreAnswers = function(ans){ if(!ans) return; Object.entries(ans).forEach(([k,v])=>{ const els = form.querySelectorAll('[name="'+k+'"]'); els.forEach(el=>{ if(el.type==='radio'){ if(el.value===v) el.checked=true; } else { el.value=v; } }); }); };
    window.doSave = function(rem){ if(!form) return; const answers={}; form.querySelectorAll('[name^="q"]').forEach(el=>{ if(el.type==='radio'){ if(el.checked) answers[el.name]=el.value; } else { const v=el.value.trim(); if(v) answers[el.name]=v; }}); fetch('/classroom/assignments/<?= $assignment['id'] ?>/ajax-save',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},credentials:'same-origin',body:JSON.stringify({answers,remaining_seconds: (typeof rem!=='undefined'?rem:null)})}).then(r=>r.json()).then(j=>{ if(j.success){ statusEl.textContent='Tersimpan otomatis'; setTimeout(()=>statusEl.textContent='',1500);} }); };
    // Real-time autosave (debounced)
    let debounceSave;
    function getAlpineComponent(){
        const container = document.querySelector('[x-data*="assignmentPlayer"]');
        return container?.$data || container.__x?.$data || window._alp;
    }
    function scheduleSave(){ clearTimeout(debounceSave); debounceSave = setTimeout(()=>{ doSave(); },500); }
    // Setup Alpine component reference with multiple fallbacks
    function setupAlpineRef(){
        const container = document.querySelector('[x-data*="assignmentPlayer"]');
        if(container && (container.$data || container.__x)){
            window._alp = container.$data || container.__x.$data;
            if(window._alp && window._alp.scanAnswers) window._alp.scanAnswers();
            return true;
        }
        return false;
    }
    // Try immediate setup, then fallback with timeout
    if(!setupAlpineRef()){
        setTimeout(()=>{ setupAlpineRef(); },500);
    }
    form.addEventListener('change', e=>{ if(e.target.matches('input[type=radio][name^="q"], textarea[name^="q"]')) { scheduleSave(); if(window._alp) window._alp.computeAnswered(); } });
    form.addEventListener('input', e=>{ if(e.target.matches('textarea[name^="q"]')) { scheduleSave(); if(window._alp) window._alp.computeAnswered(); } });
    form.addEventListener('keyup', e=>{ if(e.target.matches('textarea[name^="q"]')) { scheduleSave(); if(window._alp) window._alp.computeAnswered(); } });
    <?php if($role==='siswa' && !empty($attempt)): ?>restoreAnswers(<?= json_encode(json_decode($attempt['answers_json']??'[]',true)) ?>);<?php endif; ?>
  // Clear answer buttons
  form.addEventListener('click', (e)=>{
      if(e.target.classList.contains('clear-answer')){
          const wrap = e.target.closest('.answer-wrapper');
          if(wrap){ const ta = wrap.querySelector('textarea'); if(ta){ ta.value=''; ta.focus(); } }
      }
  });
    // Old inline submit replaced by finalSubmit() in Alpine
});
</script>
<!-- Lightbox overlay -->
<div id="imgLightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
    <div class="relative max-w-full">
         <button id="imgLightboxClose" type="button" class="absolute -top-3 -right-3 bg-white/90 hover:bg-white text-gray-700 rounded-full w-8 h-8 shadow flex items-center justify-center"><span class="text-lg leading-none">&times;</span></button>
         <img id="imgLightboxTarget" src="" alt="preview" class="max-h-[80vh] max-w-[90vw] object-contain rounded shadow-2xl" />
    </div>
</div>
<script>
// Image zoom / lightbox (question + option images)
document.addEventListener('click', e => {
    const img = e.target.closest('.zoom-img');
    const box = document.getElementById('imgLightbox');
    if(img){
        document.getElementById('imgLightboxTarget').src = img.src;
        box.classList.remove('hidden');
        box.classList.add('flex');
    } else if(e.target.id==='imgLightbox' || e.target.id==='imgLightboxClose'){
        box.classList.add('hidden');
        box.classList.remove('flex');
        document.getElementById('imgLightboxTarget').src='';
    }
});
document.addEventListener('keyup', e=>{ if(e.key==='Escape'){ const box=document.getElementById('imgLightbox'); if(!box.classList.contains('hidden')){ box.click(); } } });
</script>
<?= $this->endSection() ?>
