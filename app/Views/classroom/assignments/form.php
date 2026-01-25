<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-3xl">
    <?php $isEdit = !empty($assignment); ?>
    <h2 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 mb-6"><?= $isEdit ? 'Edit Tugas' : 'Buat Tugas' ?></h2>
    <?php if (session('error')): ?><div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><i class="fas fa-circle-exclamation mt-0.5"></i><div class="whitespace-pre-line"><?= session('error') ?></div></div><?php endif; ?>
    <?php if (session('warn')): ?><div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700"><i class="fas fa-triangle-exclamation mt-0.5"></i><div class="whitespace-pre-line"><?= session('warn') ?></div></div><?php endif; ?>
    <?php if (session('info')): ?><div class="mb-6 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700"><i class="fas fa-info-circle mt-0.5"></i><div class="whitespace-pre-line"><?= session('info') ?></div></div><?php endif; ?>
    <form method="post" action="<?= $isEdit ? '/classroom/assignments/'.$assignment['id'].'/update' : '/classroom/assignments/store' ?>" enctype="multipart/form-data" class="space-y-8" id="assignmentForm">
        <?= csrf_field() ?>
    <div class="grid sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Kelas</label>
                <?php
                    $autoKelas = null;
                    try {
                        $role = session('role');
                        if (in_array($role,['walikelas','wali_kelas'])) {
                            $db = \Config\Database::connect();
                            $row = $db->query("SELECT w.kelas FROM users u JOIN walikelas w ON u.walikelas_id = w.id WHERE u.id=?", [session('user_id')])->getRowArray();
                            if ($row) { $autoKelas = $row['kelas']; }
                        }
                    } catch (\Throwable $e) {}
                ?>
                <?php if($autoKelas): ?>
                    <input type="text" value="<?= esc($autoKelas) ?>" class="w-full rounded-lg border border-purple-200 bg-gray-100 px-3 py-2 text-sm" readonly>
                    <input type="hidden" name="kelas" value="<?= esc($autoKelas) ?>">
                <?php else: ?>
                    <input type="text" name="kelas" value="<?= old('kelas', $assignment['kelas'] ?? '') ?>" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Batas Waktu</label>
                <input type="datetime-local" name="due_at" value="<?= old('due_at', isset($assignment['due_at']) && $assignment['due_at'] ? date('Y-m-d\TH:i', strtotime($assignment['due_at'])) : '') ?>" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" />
                <p class="mt-1 text-[10px] text-gray-500">Pilih tanggal & waktu (opsional)</p>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Durasi (menit)</label>
                <input type="number" min="1" name="work_duration_minutes" value="<?= old('work_duration_minutes', $assignment['work_duration_minutes'] ?? '') ?>" placeholder="Contoh: 30" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" />
                <p class="mt-1 text-[10px] text-gray-500">Kosongkan jika tidak ada timer.</p>
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Mata Pelajaran</label>
            <?php
                $mapelList = [];
                $tableCandidates = ['subject','subjects','mapel'];
                try {
                    $db = \Config\Database::connect();
                    foreach($tableCandidates as $tbl){
                        if ($db->tableExists($tbl)) {
                            // 'subject' / 'subjects' use 'name'; 'mapel' may use 'nama'
                            $colName = $tbl === 'mapel' ? 'nama' : 'name';
                            $rows = $db->table($tbl)->select($colName)->orderBy($colName,'ASC')->get()->getResultArray();
                            foreach($rows as $r){ $val = $r[$colName]; if($val && !in_array($val,$mapelList)) $mapelList[] = $val; }
                        }
                    }
                } catch (\Throwable $e) {}
            ?>
            <?php if(!empty($mapelList)): ?>
                <select name="mapel" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required>
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($mapelList as $nama): ?>
                        <option value="<?= esc($nama) ?>" <?= old('mapel', $assignment['mapel'] ?? '')===$nama ? 'selected' : '' ?>><?= esc($nama) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1 text-[10px] text-gray-500">Sumber: tabel <?= esc(implode(', ', $tableCandidates)) ?> (auto-detect).</p>
            <?php else: ?>
                <input type="text" name="mapel" value="<?= old('mapel', $assignment['mapel'] ?? '') ?>" placeholder="Mapel" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required />
                <p class="mt-1 text-[10px] text-gray-500">Belum ada data. Jalankan migrasi & seeder: <code>php spark migrate</code> lalu <code>php spark db:seed SubjectSeeder</code>.</p>
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Judul</label>
            <input type="text" name="judul" value="<?= old('judul', $assignment['judul'] ?? '') ?>" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" required>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Deskripsi</label>
            <textarea name="deskripsi_html" rows="6" class="w-full rounded-lg border border-purple-200 bg-white/80 px-3 py-2 font-mono text-sm focus:border-purple-400 focus:ring-0"><?= old('deskripsi_html', $assignment['deskripsi_html'] ?? '') ?></textarea>
        </div>
        <div class="space-y-4" id="questionTypeSection">
            <div>
                <label class="block text-xs font-semibold mb-2 uppercase tracking-wide text-gray-600">Jenis Soal</label>
                <select id="questionMode" class="rounded-lg border border-purple-200 bg-white/80 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0">
                    <option value="latihan">Latihan (Isian)</option>
                    <option value="kuis">Kuis (Pilihan Ganda)</option>
                    <option value="campuran">Campuran</option>
                </select>
                <p class="mt-1 text-[10px] text-gray-500">Campuran: gabungkan soal Pilihan Ganda dan Isian. Gunakan tombol berbeda di bawah.</p>
            </div>
            <div id="questionAlerts" class="space-y-2"></div>
            <div id="questionsContainer" class="space-y-6"></div>
            <div class="flex flex-wrap gap-3">
                <button type="button" id="addQuestionBtn" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500"><i class="fas fa-plus text-xs"></i> <span>Tambah Soal Pertama</span></button>
                <button type="button" id="addEssayBtn" class="hidden inline-flex items-center gap-2 rounded-lg bg-fuchsia-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-fuchsia-500"><i class="fas fa-plus text-xs"></i> <span>Tambah Soal Uraian</span></button>
            </div>
            <input type="hidden" name="questions_json" id="questionsJson" value="<?= esc($assignment['questions_json'] ?? '') ?>" />
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="allow_late" id="allow_late" <?= old('allow_late', $assignment['allow_late'] ?? 0) ? 'checked' : '' ?> class="h-4 w-4 rounded border-purple-300 text-purple-600 focus:ring-purple-500">
            <label for="allow_late" class="text-sm text-gray-700">Izinkan terlambat</label>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 uppercase tracking-wide text-gray-600">Lampiran</label>
            <div class="mt-1 rounded-xl border border-dashed border-purple-300 bg-gradient-to-r from-purple-50 to-pink-50 p-4">
                <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-purple-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-purple-500" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip" />
                <p class="mt-2 text-xs text-purple-600">Multiple, max 5MB / file. pdf, gambar, dokumen, zip</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button name="action" value="draft" class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white shadow hover:bg-gray-600"><i class="fas fa-save text-xs"></i> <?= $isEdit? 'Simpan' : 'Draft' ?></button>
            <button name="action" value="publish" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 px-5 py-2.5 text-sm font-medium text-white shadow hover:shadow-md"><i class="fas fa-rocket text-xs"></i> Publish</button>
            <a href="/classroom/assignments" class="ml-auto text-sm font-medium text-gray-500 hover:text-gray-700">Kembali</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Dynamic questions builder (with auto re-numbering)
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('questionsContainer');
    const addBtn = document.getElementById('addQuestionBtn');
    const addEssayBtn = document.getElementById('addEssayBtn');
    const modeSelect = document.getElementById('questionMode');
    const alertsBox = document.getElementById('questionAlerts');
    const questionsJsonInput = document.getElementById('questionsJson');
    let mode = 'latihan'; // latihan | kuis | campuran
    let uidCounter = 0; // internal unique id (not user-facing)
    let questions = []; // {uid, number, type, text, image, options:[{key,text,image}], answer}

    // Delegated delete handler (single source of truth) so 'Hapus' works reliably
    container.addEventListener('click', (e) => {
        if(e.target && e.target.classList.contains('remove')){
            const wrapper = e.target.closest('.question-item');
            if(!wrapper) return;
            const uid = wrapper.getAttribute('data-uid');
            const qCurrent = questions.find(q=>q.uid===uid);
            const labelNumber = qCurrent ? `Soal #${qCurrent.number}` : 'Soal ini';
            const proceed = (ok)=>{ if(!ok) return; questions = questions.filter(q=>q.uid!==uid); wrapper.remove(); renumberAll(); showAlert(`${labelNumber} terhapus`, 'success'); updateAddButtons(); };
            if(window.Swal){
                Swal.fire({
                    title: `Hapus ${labelNumber}?`,
                    text: 'Tindakan ini tidak bisa dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(r=>proceed(r.isConfirmed));
            } else {
                proceed(confirm('Hapus soal ini?'));
            }
        }
    });

    function syncJson(){
        questionsJsonInput.value = JSON.stringify(questions);
    }

    function renumberAll(){
        // Rebuild ordering based on current DOM order
        const ordered = [];
        Array.from(container.querySelectorAll('.question-item')).forEach((el,idx) => {
            const uid = el.getAttribute('data-uid');
            const q = questions.find(q => q.uid === uid);
            if(!q) return;
            q.number = idx + 1;
            // Update heading text
            const titleEl = el.querySelector('.q-title');
            if(titleEl){
                titleEl.textContent = `Soal #${q.number} ${q.type === 'kuis' ? '(Kuis)' : '(Isian)'}`;
            }
            ordered.push(q);
        });
        questions = ordered;
        syncJson();
    }

    function showAlert(msg, kind='info'){
        const icon = (kind === 'success') ? 'success' : (kind === 'error' ? 'error' : (kind==='warning'?'warning':'info'));
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon,
            title: msg
        });
    }

    function updateAddButtons(){
        if(mode === 'campuran') {
            addEssayBtn.classList.remove('hidden');
            addBtn.querySelector('span').textContent = questions.length ? 'Tambah Soal PG' : 'Tambah Soal PG Pertama';
        } else if(mode === 'kuis') {
            addEssayBtn.classList.add('hidden');
            addBtn.querySelector('span').textContent = questions.length ? 'Tambah Soal PG Berikutnya' : 'Tambah Soal PG Pertama';
        } else {
            addEssayBtn.classList.add('hidden');
            addBtn.querySelector('span').textContent = questions.length ? 'Tambah Soal Isian Berikutnya' : 'Tambah Soal Isian Pertama';
        }
    }

    function createQuestionCard(requestedType){
        const questionType = requestedType || (mode === 'campuran' ? 'latihan' : mode);
        uidCounter++;
        const uid = 'q'+uidCounter;
        const wrapper = document.createElement('div');
        wrapper.className = 'question-item rounded-xl border border-purple-200 bg-white/60 p-4 shadow-sm relative';
        wrapper.setAttribute('data-uid', uid);
    const qObj = { uid, number: 0, type: questionType, text: '', image: null, options: [], answer: '' };

        if(questionType === 'kuis') {
            wrapper.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="q-title font-semibold text-purple-600">Soal (Kuis)</h4>
                    <button type="button" class="remove text-xs text-red-500 hover:underline">Hapus</button>
                </div>
                <label class="block text-xs font-semibold mb-1 text-gray-600">Pertanyaan</label>
                <textarea class="q-text w-full mb-2 rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" rows="3"></textarea>
                <div class="mt-2 mb-4">
                    <label class="block text-[11px] font-semibold mb-1 text-gray-500">Gambar (opsional)</label>
                    <input type="file" name="question_images[${uid}]" accept="image/*" class="q-image block w-full text-xs text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-purple-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-purple-500" />
                    <div class="q-image-preview mt-2"></div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    ${['A','B','C','D'].map(l => `
                        <div>
                            <label class='block text-[11px] font-semibold mb-1 text-gray-500'>Pilihan ${l}</label>
                            <input data-opt='${l}' type='text' class='q-opt w-full rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0' />
                            <input data-opt='${l}' type='file' name='option_images[${uid}][${l}]' accept='image/*' class='q-opt-image mt-1 block w-full text-[11px] text-gray-600 file:mr-2 file:rounded-md file:border-0 file:bg-purple-500 file:px-2 file:py-1 file:text-[11px] file:font-medium file:text-white hover:file:bg-purple-400' />
                            <div class='q-opt-preview mt-1 min-h-[1rem]'></div>
                            <div class='text-[9px] text-gray-400'>Debug: name="option_images[${uid}][${l}]"</div>
                        </div>`).join('')}
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                    <label class="flex items-center gap-2"><span class="text-xs text-gray-600 font-semibold">Jawaban Benar:</span>
                        <select class="q-answer rounded-md border border-purple-200 px-2 py-1 text-sm focus:border-purple-400 focus:ring-0" required>
                            <option value="">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </label>
                </div>`;
            // Multi-line paste handler: paste lines into option A to auto-fill A-D
            setTimeout(() => {
                const firstOpt = wrapper.querySelector(".q-opt[data-opt='A']");
                if(firstOpt){
                    firstOpt.addEventListener('paste', (e) => {
                        const clip = (e.clipboardData || window.clipboardData).getData('text');
                        if(clip && /\n/.test(clip)) {
                            e.preventDefault();
                            const lines = clip.split(/\r?\n/).map(s=>s.trim()).filter(Boolean);
                            const keys = ['A','B','C','D'];
                            keys.forEach((k,i)=>{
                                if(lines[i]){
                                    const inp = wrapper.querySelector(".q-opt[data-opt='"+k+"']");
                                    if(inp){ inp.value = lines[i]; }
                                }
                            });
                            // trigger input update
                            wrapper.dispatchEvent(new Event('input', {bubbles:true}));
                        }
                    });
                }
            },0);
    } else {
            wrapper.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="q-title font-semibold text-purple-600">Soal (Isian)</h4>
                    <button type="button" class="remove text-xs text-red-500 hover:underline">Hapus</button>
                </div>
                <label class="block text-xs font-semibold mb-1 text-gray-600">Pertanyaan</label>
                <textarea class="q-text w-full mb-2 rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" rows="3"></textarea>
                <div class="mt-2 mb-4">
                    <label class="block text-[11px] font-semibold mb-1 text-gray-500">Gambar (opsional)</label>
                    <input type="file" name="question_images[${uid}]" accept="image/*" class="q-image block w-full text-xs text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-purple-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-purple-500" />
                    <div class="q-image-preview mt-2"></div>
                </div>
                <label class="block text-xs font-semibold mb-1 text-gray-600">Kunci Jawaban (opsional)</label>
                <input type="text" class="q-answer w-full rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" />`;
        }

        // Events
        wrapper.addEventListener('input', () => {
            qObj.text = wrapper.querySelector('.q-text')?.value || '';
            const imgInput = wrapper.querySelector('.q-image');
            if(imgInput && imgInput.files && imgInput.files.length>0){
                qObj.image = '__NEW__'; // marker for new upload
            } else if(typeof qObj.image === 'string' || qObj.image === null){
                // keep existing filename or null
            } else {
                qObj.image = null;
            }
            if(qObj.type === 'kuis') {
                const prevOptionsMap = new Map((qObj.options||[]).map(o=>[o.key,o]));
                qObj.options = Array.from(wrapper.querySelectorAll('.q-opt')).map(inp=>{
                    const key = inp.getAttribute('data-opt');
                    const fileInp = wrapper.querySelector(`.q-opt-image[data-opt="${key}"]`);
                    let imageVal = null;
                    if(fileInp && fileInp.files && fileInp.files.length>0){
                        imageVal='__NEW__';
                    } else if(prevOptionsMap.has(key)) {
                        const prev = prevOptionsMap.get(key);
                        if(typeof prev.image==='string') imageVal=prev.image; // retain existing filename
                    }
                    return {key, text: inp.value, image: imageVal};
                });
                qObj.answer = wrapper.querySelector('.q-answer')?.value || '';
            } else {
                qObj.answer = wrapper.querySelector('.q-answer')?.value || '';
            }
            syncJson();
        });

        // File input change events to trigger JSON sync
        setTimeout(() => {
            wrapper.querySelectorAll('.q-image, .q-opt-image').forEach(inp => {
                inp.addEventListener('change', () => {
                    wrapper.dispatchEvent(new Event('input', {bubbles:true}));
                    // preview logic
                    if(inp.classList.contains('q-image')){
                        const previewBox = wrapper.querySelector('.q-image-preview');
                        if(previewBox){
                            previewBox.innerHTML='';
                            if(inp.files && inp.files[0]){
                                const url = URL.createObjectURL(inp.files[0]);
                                const img = document.createElement('img');
                                img.src = url; img.className='h-20 rounded border shadow-sm';
                                img.onload = () => setTimeout(()=>URL.revokeObjectURL(url), 1500);
                                previewBox.appendChild(img);
                            }
                        }
                    } else if(inp.classList.contains('q-opt-image')) {
                        const key = inp.getAttribute('data-opt');
                        const previewBox = inp.parentElement.querySelector('.q-opt-preview');
                        if(previewBox){
                            previewBox.innerHTML='';
                            if(inp.files && inp.files[0]){
                                const url = URL.createObjectURL(inp.files[0]);
                                const img = document.createElement('img');
                                img.src = url; img.className='h-14 rounded border shadow-sm';
                                img.onload = () => setTimeout(()=>URL.revokeObjectURL(url), 1500);
                                previewBox.appendChild(img);
                            }
                        }
                    }
                });
            });
        },0);
    // Delete handled by delegated listener above

        container.appendChild(wrapper);
        questions.push(qObj);
        renumberAll();
        updateAddButtons();
    }

    addBtn.addEventListener('click', () => {
        if(mode === 'campuran') { // default PG button
            createQuestionCard('kuis');
        } else {
            createQuestionCard(mode);
        }
    });
    addEssayBtn.addEventListener('click', () => createQuestionCard('latihan'));
    modeSelect.addEventListener('change', () => {
        mode = modeSelect.value;
        questions = []; uidCounter = 0; container.innerHTML=''; syncJson();
        showAlert('Mode soal diubah ke: ' + mode.charAt(0).toUpperCase()+mode.slice(1), 'info');
        updateAddButtons();
    });
    updateAddButtons();
    // Force re-sync before submit to capture last chosen files
    const formEl = document.getElementById('assignmentForm');
    formEl.addEventListener('submit', (e) => {
        console.log('Form submission triggered');
        
        // Check if any files are actually selected in the form
        const allFileInputs = formEl.querySelectorAll('input[type="file"]');
        let fileCount = 0;
        allFileInputs.forEach(input => {
            if (input.files && input.files.length > 0) {
                console.log(`File input ${input.name}: ${input.files[0].name} (${input.files[0].size} bytes)`);
                fileCount++;
            }
        });
        console.log(`Total file inputs with files selected: ${fileCount}`);
        
        if (fileCount === 0) {
            console.warn('No files selected in any file inputs');
        }
        
        container.querySelectorAll('.question-item').forEach(wrapper => {
            wrapper.dispatchEvent(new Event('input', {bubbles:true}));
        });
        syncJson();
        
        // Check the final JSON
        const jsonField = document.querySelector('input[name="questions_json"]');
        if (jsonField) {
            console.log('Final questions_json:', jsonField.value);
        }
    });
    // Prefill existing questions in edit mode (preserve original UIDs so image reuse works)
    function createExistingQuestionCard(q){
        const wrapper = document.createElement('div');
        wrapper.className = 'question-item rounded-xl border border-purple-200 bg-white/60 p-4 shadow-sm relative';
        wrapper.setAttribute('data-uid', q.uid);
        const qObj = { uid: q.uid, number: 0, type: q.type, text: q.text||'', image: q.image||null, options: q.options||[], answer: q.answer||'' };
        // Build inner HTML (reuse logic simplified from createQuestionCard)
        if(q.type==='kuis') {
            wrapper.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="q-title font-semibold text-purple-600">Soal (Kuis)</h4>
                    <button type="button" class="remove text-xs text-red-500 hover:underline">Hapus</button>
                </div>
                <label class="block text-xs font-semibold mb-1 text-gray-600">Pertanyaan</label>
                <textarea class="q-text w-full mb-2 rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" rows="3"></textarea>
                <div class="mt-2 mb-4">
                    <label class="block text-[11px] font-semibold mb-1 text-gray-500">Gambar (opsional)</label>
                    <input type="file" name="question_images[${q.uid}]" accept="image/*" class="q-image block w-full text-xs text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-purple-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-purple-500" />
                    <div class="q-image-preview mt-2">${q.image?`<img src="/classroom/assignments/question-image/${q.image}" class="max-h-40 w-auto max-w-full object-contain rounded border shadow-sm" alt="img" />`:''}</div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    ${['A','B','C','D'].map(l => {
                        const opt = (q.options||[]).find(o=>o.key===l) || {text:'',image:null};
                        return `<div>
                            <label class='block text-[11px] font-semibold mb-1 text-gray-500'>Pilihan ${l}</label>
                            <input data-opt='${l}' type='text' class='q-opt w-full rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0' />
                            <input data-opt='${l}' type='file' name='option_images[${q.uid}][${l}]' accept='image/*' class='q-opt-image mt-1 block w-full text-[11px] text-gray-600 file:mr-2 file:rounded-md file:border-0 file:bg-purple-500 file:px-2 file:py-1 file:text-[11px] file:font-medium file:text-white hover:file:bg-purple-400' />
                            <div class='q-opt-preview mt-1 min-h-[1rem]'>${opt.image?`<img src="/classroom/assignments/question-image/${opt.image}" class="max-h-28 w-auto max-w-full object-contain rounded border shadow-sm" alt="opt" />`:''}</div>
                        </div>`;
                    }).join('')}
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                    <label class="flex items-center gap-2"><span class="text-xs text-gray-600 font-semibold">Jawaban Benar:</span>
                        <select class="q-answer rounded-md border border-purple-200 px-2 py-1 text-sm focus:border-purple-400 focus:ring-0" required>
                            <option value="">-</option>
                            ${['A','B','C','D'].map(l=>`<option value='${l}' ${q.answer===l?'selected':''}>${l}</option>`).join('')}
                        </select>
                    </label>
                </div>`;
        } else {
            wrapper.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="q-title font-semibold text-purple-600">Soal (Isian)</h4>
                    <button type="button" class="remove text-xs text-red-500 hover:underline">Hapus</button>
                </div>
                <label class="block text-xs font-semibold mb-1 text-gray-600">Pertanyaan</label>
                <textarea class="q-text w-full mb-2 rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" rows="3"></textarea>
                <div class="mt-2 mb-4">
                    <label class="block text-[11px] font-semibold mb-1 text-gray-500">Gambar (opsional)</label>
                    <input type="file" name="question_images[${q.uid}]" accept="image/*" class="q-image block w-full text-xs text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-purple-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-purple-500" />
                    <div class="q-image-preview mt-2">${q.image?`<img src="/classroom/assignments/question-image/${q.image}" class="max-h-40 w-auto max-w-full object-contain rounded border shadow-sm" alt="img" />`:''}</div>
                </div>
                <label class="block text-xs font-semibold mb-1 text-gray-600">Kunci Jawaban (opsional)</label>
                <input type="text" class="q-answer w-full rounded-lg border border-purple-200 px-3 py-2 text-sm focus:border-purple-400 focus:ring-0" value="${q.answer?String(q.answer).replace(/"/g,'&quot;'):''}" />`;
        }
        // listeners (reuse from createQuestionCard simplified)
        wrapper.addEventListener('input', () => {
            qObj.text = wrapper.querySelector('.q-text')?.value || '';
            const imgInput = wrapper.querySelector('.q-image');
            if(imgInput && imgInput.files && imgInput.files.length>0){
                qObj.image = '__NEW__';
            } else {
                qObj.image = (typeof qObj.image==='string') ? qObj.image : (q.image||null);
            }
            if(qObj.type==='kuis'){
                qObj.options = Array.from(wrapper.querySelectorAll('.q-opt')).map(i=>{
                    const key = i.getAttribute('data-opt');
                    const prev = (q.options||[]).find(o=>o.key===key) || {};
                    const imgInp = wrapper.querySelector(`.q-opt-image[data-opt='${key}']`);
                    if(imgInp && imgInp.files && imgInp.files.length>0){
                        return {key, text:i.value, image:'__NEW__'};
                    }
                    const existing = (qObj.options||[]).find(o=>o.key===key);
                    return {key, text:i.value, image: (existing && typeof existing.image==='string') ? existing.image : (prev.image||null)};
                });
                qObj.answer = wrapper.querySelector('.q-answer')?.value || '';
            } else {
                qObj.answer = wrapper.querySelector('.q-answer')?.value || '';
            }
            syncJson();
        });
        setTimeout(()=>{
            wrapper.querySelectorAll('.q-image, .q-opt-image').forEach(inp=>{
                inp.addEventListener('change',()=> {
                    wrapper.dispatchEvent(new Event('input',{bubbles:true}));
                    // live preview replacement
                    if(inp.classList.contains('q-image')){
                        const box = wrapper.querySelector('.q-image-preview');
                        if(box){
                            box.innerHTML='';
                            if(inp.files && inp.files[0]){
                                const url = URL.createObjectURL(inp.files[0]);
                                const img = document.createElement('img');
                                img.src=url; img.className='h-20 rounded border shadow-sm';
                                img.onload=()=>setTimeout(()=>URL.revokeObjectURL(url),1500);
                                box.appendChild(img);
                            }
                        }
                    } else if(inp.classList.contains('q-opt-image')){
                        const key = inp.getAttribute('data-opt');
                        const box = inp.parentElement.querySelector('.q-opt-preview');
                        if(box){
                            box.innerHTML='';
                            if(inp.files && inp.files[0]){
                                const url = URL.createObjectURL(inp.files[0]);
                                const img = document.createElement('img');
                                img.src=url; img.className='h-14 rounded border shadow-sm';
                                img.onload=()=>setTimeout(()=>URL.revokeObjectURL(url),1500);
                                box.appendChild(img);
                            }
                        }
                    }
                });
            });
        },0);
    // Delete handled by delegated listener above
        container.appendChild(wrapper);
        // Populate field values now that elements are in DOM
        const txt = wrapper.querySelector('.q-text'); if(txt) txt.value = q.text || '';
        if(q.type==='kuis'){
            (q.options||[]).forEach(opt => {
                const inp = wrapper.querySelector(`.q-opt[data-opt='${opt.key}']`);
                if(inp) inp.value = opt.text || '';
            });
            const ansSel = wrapper.querySelector('.q-answer'); if(ansSel && q.answer) ansSel.value = q.answer;
        } else {
            const ansInput = wrapper.querySelector('.q-answer'); if(ansInput && q.answer) ansInput.value = q.answer;
        }
        // trigger sync after setting values
        wrapper.dispatchEvent(new Event('input',{bubbles:true}));
        questions.push(qObj);
        renumberAll();
    }
    <?php if(!empty($assignment['questions_json'])): ?>
    try {
        const existing = <?= json_encode(json_decode($assignment['questions_json'], true)) ?>;
        if(Array.isArray(existing) && existing.length){
            const types = new Set(existing.map(q=>q.type));
            if(types.size>1){ mode='campuran'; modeSelect.value='campuran'; }
            else if(types.has('kuis')){ mode='kuis'; modeSelect.value='kuis'; }
            else { mode='latihan'; modeSelect.value='latihan'; }
            questions=[]; container.innerHTML=''; uidCounter=0;
            existing.forEach(q=>{
                // update uidCounter numeric part so new additions don't collide
                const num = parseInt(String(q.uid).replace(/\D/g,''))||0; if(num>uidCounter) uidCounter=num;
                createExistingQuestionCard(q);
            });
            updateAddButtons();
            syncJson();
        }
    } catch(e){ console.warn('Prefill failed', e); }
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
