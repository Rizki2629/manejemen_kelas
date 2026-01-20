<?php

if (! function_exists('format_tanggal_lokal')) {
    function format_tanggal_lokal(string $date): string
    {
        $bulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $ts = strtotime($date);
        if (!$ts) return $date;
        $d = (int)date('j', $ts);
        $m = (int)date('n', $ts);
        $y = date('Y', $ts);
        return $d.' '.$bulan[$m].' '.$y;
    }
}
