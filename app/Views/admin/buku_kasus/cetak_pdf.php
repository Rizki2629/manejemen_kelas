<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kasus Siswa - <?= esc($kasus['nama_siswa']) ?></title>
    <style>
        @page {
            margin: 2cm;
            size: 215.9mm 330mm;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        
        .content {
            margin: 15px 0;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 3px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 30%;
            font-weight: bold;
        }
        
        .info-table .separator {
            width: 5%;
            text-align: center;
        }
        
        .info-table .value {
            width: 65%;
        }
        
        .description-box {
            border: 1px solid #333;
            padding: 10px;
            margin: 8px 0;
            min-height: 40px;
            background-color: #f9f9f9;
        }
        
        .signature-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        .signature-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .signature-left {
            float: left;
            width: 45%;
            text-align: center;
        }
        
        .signature-right {
            float: right;
            width: 45%;
            text-align: center;
        }
        
        .signature-center {
            clear: both;
            width: 50%;
            margin: 0 auto;
            text-align: center;
        }
        
        .signature-box {
            height: 40px;
            margin: 3px 0;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            width: 200px;
            margin: 8px auto;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 2px;
        }
        
        .signature-nip {
            font-size: 10pt;
            margin-top: 2px;
        }
        
        .signature-title {
            font-style: italic;
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .clearfix {
            clear: both;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Kasus Siswa</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Data Siswa -->
        <div class="section">
            <div class="section-title">Data Siswa</div>
            <table class="info-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="separator">:</td>
                    <td class="value"><?= esc($kasus['nama_siswa']) ?></td>
                </tr>
                <tr>
                    <td class="label">NIS</td>
                    <td class="separator">:</td>
                    <td class="value"><?= esc($kasus['nis']) ?></td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td class="separator">:</td>
                    <td class="value"><?= esc($kasus['kelas']) ?></td>
                </tr>
                <?php if (!empty($kasus['jenis_kelamin'])): ?>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="separator">:</td>
                    <td class="value"><?= $kasus['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($kasus['tempat_lahir']) && !empty($kasus['tanggal_lahir'])): ?>
                <tr>
                    <td class="label">Tempat, Tanggal Lahir</td>
                    <td class="separator">:</td>
                    <td class="value"><?php 
                        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        echo esc($kasus['tempat_lahir']) . ', ' . 
                        date('d ', strtotime($kasus['tanggal_lahir'])) . 
                        $bulanIndo[date('n', strtotime($kasus['tanggal_lahir']))] . 
                        date(' Y', strtotime($kasus['tanggal_lahir'])); 
                    ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Data Kasus -->
        <div class="section">
            <div class="section-title">Data Kasus</div>
            <table class="info-table">
                <tr>
                    <td class="label">Tanggal Kejadian</td>
                    <td class="separator">:</td>
                    <td class="value"><?= 
                        date('d ', strtotime($kasus['tanggal_kejadian'])) . 
                        ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n', strtotime($kasus['tanggal_kejadian']))] . 
                        date(' Y', strtotime($kasus['tanggal_kejadian'])) 
                    ?></td>
                </tr>
                <tr>
                    <td class="label">Pelapor</td>
                    <td class="separator">:</td>
                    <td class="value"><?= esc($kasus['nama_guru']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Deskripsi Kasus -->
        <div class="section">
            <div class="section-title">Deskripsi Kasus</div>
            <div class="description-box">
                <?= nl2br(esc($kasus['deskripsi_kasus'])) ?>
            </div>
        </div>

        <!-- Tindakan yang Diambil -->
        <?php if (!empty($kasus['tindakan_yang_diambil'])): ?>
        <div class="section">
            <div class="section-title">Tindakan yang Diambil</div>
            <div class="description-box">
                <?= nl2br(esc($kasus['tindakan_yang_diambil'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <!-- Baris Pertama: Orang Tua (Kiri) dan Walikelas (Kanan) -->
            <div class="signature-row">
                <div class="signature-left">
                    <div class="signature-title">Orang Tua/Wali</div>
                    <div class="signature-box"></div>
                    <div class="signature-name">(_________________)</div>
                    <div class="signature-line"></div>
                </div>
                <div class="signature-right">
                    <div class="signature-title">Walikelas</div>
                    <div class="signature-box"></div>
                    <div class="signature-name"><?= esc($kasus['nama_guru']) ?></div>
                    <div class="signature-line"></div>
                    <div class="signature-nip">NIP. <?= esc($kasus['nip_guru'] ?? '________________') ?></div>
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <!-- Baris Kedua: Kepala Sekolah (Tengah) -->
            <div class="signature-center">
                <div>Mengetahui,</div>
                <div class="signature-title">Kepala Sekolah</div>
                <div class="signature-box"></div>
                <div class="signature-name">(_________________)</div>
                <div class="signature-line"></div>
                <div class="signature-nip">NIP. ________________</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada <?= 
            date('d ', strtotime('now')) . 
            ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n', strtotime('now'))] . 
            date(' Y H:i:s', strtotime('now')) 
        ?></p>
    </div>
</body>
</html>
