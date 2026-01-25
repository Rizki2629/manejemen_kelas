<?php /** @var array $pagination */ ?>
<?php if (($pagination['pages'] ?? 1) > 1): ?>
<nav class="mt-8 flex items-center justify-between text-sm" aria-label="Pagination">
    <?php $page = $pagination['page']; $pages = $pagination['pages']; ?>
    <div>
        <p class="text-xs text-gray-500">Halaman <span class="font-semibold"><?= $page ?></span> dari <span class="font-semibold"><?= $pages ?></span> (<?= $pagination['total'] ?> materi)</p>
    </div>
    <ul class="inline-flex items-center gap-1">
        <?php $baseParams = $_GET; unset($baseParams['page']); ?>
        <?php $build = function($p,$label,$active=false,$disabled=false) use ($baseParams){
            $params = $baseParams; $params['page']=$p; $query=http_build_query($params);
            if($disabled){
                return '<span class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300">'.$label.'</span>';
            }
            $cls = 'px-3 py-1.5 rounded-lg border text-xs font-medium transition '; 
            if($active){
                $cls.='border-purple-600 bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow';
            } else {
                $cls.='border-gray-200 bg-white text-gray-600 hover:border-purple-400 hover:text-purple-600';
            }
            return '<a class="'.$cls.'" href="?'.$query.'">'.$label.'</a>';
        }; ?>
        <?= $build(max(1,$page-1),'&laquo;',false,$page===1) ?>
        <?php
        // window logic
        $window = 3; $start = max(1,$page-$window); $end = min($pages,$page+$window);
        if ($start>1) echo $build(1,'1',$page===1);
        if ($start>2) echo '<span class="px-2 text-gray-400">…</span>';
        for($i=$start;$i<=$end;$i++){
            if($i===1 || $i===$pages) continue; // already handled edges
            echo $build($i,(string)$i,$i===$page);
        }
        if ($end < $pages-1) echo '<span class="px-2 text-gray-400">…</span>';
        if ($pages>1) echo $build($pages,(string)$pages,$page===$pages);
        ?>
        <?= $build(min($pages,$page+1),'&raquo;',false,$page===$pages) ?>
    </ul>
</nav>
<?php endif; ?>