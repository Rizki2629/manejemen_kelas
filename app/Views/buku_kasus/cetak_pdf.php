<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Kasus - <?= $kasus['nama_siswa']; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4338ca;
            padding-bottom: 10px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 18pt;
            color: #4338ca;
            margin: 5px 0;
        }
        h2 {
            font-size: 14pt;
            color: #4338ca;
            margin: 5px 0 20px 0;
        }
        .school-info {
            font-size: 10pt;
            margin-bottom: 5px;
        }
        .container {
            padding: 0 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 30%;
            font-weight: bold;
        }
        .info-value {
            width: 70%;
        }
        .box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 5px;
            min-height: 80px;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
            width: 100%;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 10pt;
            font-weight: bold;
        }
        .badge-blue {
            background-color: #e0e7ff;
            color: #4338ca;
        }
        .badge-yellow {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-red {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .badge-orange {
            background-color: #ffedd5;
            color: #9a3412;
        }
        .badge-gray {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            color: #6b7280;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CATATAN KASUS SISWA</h1>
        <h2>SDN GROGOL UTARA 09</h2>
        <div class="school-info">Jl. Grogol Utara, Kec. Kebayoran Lama, Kota Jakarta Selatan, DKI Jakarta</div>
        <div class="school-info">Telp: (021) XXX-XXXX | Email: sdn.grogolutara09@jakarta.go.id</div>
    </div>

    <div class="container">
        <div class="section">
            <div class="section-title">IDENTITAS SISWA</div>
            <table class="table">
                <tr>
                    <td width="30%"><strong>Nama Lengkap</strong></td>
                    <td width="70%"><?= $kasus['nama_siswa']; ?></td>
                </tr>
                <tr>
                    <td><strong>Kelas</strong></td>
                    <td><?= $kasus['nama_kelas']; ?></td>
                </tr>
                <tr>
                    <td><strong>Tanggal Kejadian</strong></td>
                    <td><?= $tanggal; ?></td>
                </tr>
                <tr>
                    <td><strong>Jenis Masalah</strong></td>
                    <td>
                        <?php if ($kasus['jenis_masalah'] === 'lainnya') : ?>
                            <span class="badge badge-gray"><?= $kasus['jenis_masalah_lainnya']; ?></span>
                        <?php else : ?>
                            <?php
                            $badgeClass = 'badge-blue';
                            if ($kasus['jenis_masalah'] === 'tidak mengerjakan pr') {
                                $badgeClass = 'badge-yellow';
                            } else if ($kasus['jenis_masalah'] === 'mengerjakan PR di sekolah') {
                                $badgeClass = 'badge-orange';
                            } else if ($kasus['jenis_masalah'] === 'berkelahi di kelas') {
                                $badgeClass = 'badge-red';
                            }
                            ?>
                            <span class="badge <?= $badgeClass; ?>"><?= ucfirst($kasus['jenis_masalah']); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">CATATAN MASALAH</div>
            <div class="box">
                <?= nl2br(esc($kasus['catatan_masalah'])); ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">PENYELESAIAN</div>
            <div class="box">
                <?= nl2br(esc($kasus['penyelesaian'])); ?>
            </div>
        </div>

        <div class="signature">
            <p>Jakarta, <?= $tanggal; ?></p>
            <p>Walikelas <?= $kasus['nama_kelas']; ?></p>
            <br><br><br>
            <div class="signature-line"></div>
            <p><?= $kasus['nama_guru']; ?></p>
            <p>
                <?php if (!empty($kasus['nip_guru'])) : ?>
                    NIP. <?= $kasus['nip_guru']; ?>
                <?php else : ?>
                    NIP. -
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini merupakan catatan resmi dan disimpan sebagai arsip sekolah</p>
        <p>Dicetak pada: <?= date('d F Y, H:i'); ?> WIB</p>
    </div>
</body>
</html>
