<?= $this->extend('layouts/siswa_layout') ?>
<?= $this->section('title') ?>Dashboard Tugas<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
function scoreBadgeClasses($score){
    if($score===null) return '';
    if($score>=85) return 'bg-green-100 text-green-800';
    if($score>=70) return 'bg-orange-100 text-orange-800';
    return 'bg-red-100 text-red-800';
}
function excerpt($html,$len=100){
    $t=trim(strip_tags($html));
    if(mb_strlen($t)<=$len) return $t; return mb_substr($t,0,$len).'…';
}
?>
<style>
    .card-shadow{box-shadow:0 10px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04)}
    .card-hover{transition:.3s ease;}
    .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 40px -5px rgba(0,0,0,.15),0 10px 20px -5px rgba(0,0,0,.1)}
    .pulse{animation:pulse 2s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}
</style>
<div class="font-[Poppins] -mx-2 sm:mx-0">
    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-3"><span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Dashboard Tugas</span></h1>
        <p class="text-gray-600 text-base">Kelola dan pantau semua tugas Anda dengan mudah</p>
    </div>
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-xl card-shadow p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 mb-1 tabular-nums"><?= $stats['total'] ?></div>
            <div class="text-gray-600 text-sm font-medium">Total Tugas</div>
        </div>
        <div class="bg-white rounded-xl card-shadow p-6 text-center">
            <div class="text-3xl font-bold text-yellow-600 mb-1 tabular-nums"><?= $stats['belum'] ?></div>
            <div class="text-gray-600 text-sm font-medium">Belum Dikerjakan</div>
        </div>
        <div class="bg-white rounded-xl card-shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-1 tabular-nums"><?= $stats['dikirim'] ?></div>
            <div class="text-gray-600 text-sm font-medium">Sudah Dikumpul</div>
        </div>
        <div class="bg-white rounded-xl card-shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-1 tabular-nums"><?= $stats['dinilai'] ?></div>
            <div class="text-gray-600 text-sm font-medium">Sudah Dinilai</div>
        </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($assignments as $a):
            $sub = $a['user_submission'] ?? null;
            $status = 'belum';
            if($sub){ $status = $sub['score']!==null ? 'dinilai':'dikirim'; }
            $score = $sub['score'] ?? null;
            $dueAt = $a['due_at'] ? strtotime($a['due_at']) : null;
            $isToday = $dueAt && date('Y-m-d',$dueAt) === date('Y-m-d');
            $overdue = $dueAt && $dueAt < time();
            $urgent = ($status==='belum' && $isToday);
            // Status badge
            $statusLabel = $status==='belum'?'Belum Dikerjakan':($status==='dikirim'?'Sudah Dikumpul':'Sudah Dinilai');
            $statusClasses = [
                'belum'=>'bg-yellow-100 text-yellow-800'.($urgent?' pulse':''),
                'dikirim'=>'bg-blue-100 text-blue-800',
                'dinilai'=>'bg-green-100 text-green-800'] [$status];
            // Deadline badge
            if($status==='belum'){
                $deadlineText = $a['due_at'] ? ($urgent? '⚠️ Deadline: Hari ini!' : ($overdue? '⏰ Terlewat: '.date('d M Y',$dueAt) : '⏰ Deadline: '.date('d M Y',$dueAt))) : 'Tanpa Deadline';
            } elseif($status==='dikirim') {
                $deadlineText = $a['due_at'] ? '✅ Dikumpul: '.date('d M Y', strtotime($sub['submitted_at'] ?? $a['due_at'])) : 'Dikumpul';
            } else { // dinilai
                $deadlineText = $a['due_at'] ? '✅ Selesai: '.date('d M Y', strtotime($sub['submitted_at'] ?? $a['due_at'])) : 'Selesai';
            }
            $deadlineClass = $urgent||($status==='belum' && ($overdue||$urgent)) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
            $mapelBadgeClass = 'bg-blue-100 text-blue-800';
            // Score badge
            $scoreClass = scoreBadgeClasses($score);
            $desc = excerpt($a['deskripsi_html'] ?? '', 120);
        ?>
        <div class="bg-white rounded-xl card-shadow card-hover p-6 relative overflow-hidden <?= $urgent? 'border-2 border-red-200':'' ?>">
            <?php if($urgent): ?><div class="absolute top-0 right-0 bg-red-500 text-white px-3 py-1 text-xs font-bold rounded-bl-lg">URGENT</div><?php endif; ?>
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 <?= $status==='belum'?'bg-purple-100 text-purple-600':($status==='dikirim'?'bg-green-100 text-green-600':'bg-blue-100 text-blue-600') ?> rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg line-clamp-2"><?= esc($a['judul']) ?></h3>
                    </div>
                </div>
                <div class="px-3 py-1 rounded-full text-sm font-semibold <?= $statusClasses ?>"><?= $statusLabel ?></div>
            </div>
            <div class="mb-4 space-y-2">
                <?php if(!empty($a['mapel'])): ?><div class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $mapelBadgeClass ?>">📚 <?= esc($a['mapel']) ?></div><?php endif; ?>
                <div class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?= $deadlineClass ?>"><?= esc($deadlineText) ?></div>
            </div>
            <?php if($desc): ?><p class="text-gray-600 text-sm mb-5 leading-relaxed"><?= esc($desc) ?></p><?php endif; ?>
            <div class="flex justify-between items-center mt-auto gap-3">
                <?php if($status==='belum'): ?>
                    <a href="/classroom/assignments/<?= $a['id'] ?>" class="w-full text-center bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-semibold py-3 rounded-lg transition-all duration-300 <?= $urgent? 'animate-pulse':'' ?>">Mulai Mengerjakan</a>
                <?php elseif($status==='dikirim'): ?>
                    <button disabled class="w-full text-center bg-gray-200 text-gray-600 font-semibold py-3 rounded-lg cursor-not-allowed">Menunggu Penilaian</button>
                <?php else: ?>
                    <a href="/classroom/assignments/<?= $a['id'] ?>" class="w-full text-center bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold py-3 rounded-lg transition-colors">Lihat Detail</a>
                <?php endif; ?>
                <?php if($status==='dinilai'): ?>
                    <div class="px-4 py-2 rounded-xl font-bold text-xl <?= $scoreClass ?> whitespace-nowrap flex items-center gap-1">🎯 <?= (int)$score ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($assignments)): ?>
            <div class="col-span-full bg-white rounded-xl card-shadow p-10 text-center text-sm text-gray-500">Belum ada tugas.</div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
