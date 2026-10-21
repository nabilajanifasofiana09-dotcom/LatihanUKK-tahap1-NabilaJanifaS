<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$bulan = isset($_GET['bulan']) ? (int) $_GET['bulan'] : (int) date('m');
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

if ($bulan < 1 || $bulan > 12) {
    $bulan = (int) date('m');
}

if ($tahun < 2000 || $tahun > 2100) {
    $tahun = (int) date('Y');
}

$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
    4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$awal = sprintf('%04d-%02d-01', $tahun, $bulan);
$akhir = date('Y-m-d', strtotime($awal . ' +1 month'));

$stmt = mysqli_prepare($koneksi, "
    SELECT
        COUNT(*) AS jumlah_transaksi,
        COALESCE(SUM(total_transaksi), 0) AS total_penjualan
    FROM transaksi
    WHERE status_transaksi = 'selesai'
      AND tanggal_transaksi >= ?
      AND tanggal_transaksi < ?
");

mysqli_stmt_bind_param($stmt, "ss", $awal, $akhir);
mysqli_stmt_execute($stmt);
$ringkasan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmtProduk = mysqli_prepare($koneksi, "
    SELECT COALESCE(SUM(dp.jumlah), 0) AS produk_terjual
    FROM transaksi t
    JOIN penjualan p ON t.id_penjualan = p.id_penjualan
    JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
    WHERE t.status_transaksi = 'selesai'
      AND t.tanggal_transaksi >= ?
      AND t.tanggal_transaksi < ?
");

mysqli_stmt_bind_param($stmtProduk, "ss", $awal, $akhir);
mysqli_stmt_execute($stmtProduk);
$dataProduk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtProduk));

$stmtDetail = mysqli_prepare($koneksi, "
    SELECT
        t.id_transaksi,
        t.tanggal_transaksi,
        pl.nama_pelanggan,
        t.total_transaksi,
        t.uang_bayar,
        t.uang_kembali
    FROM transaksi t
    JOIN penjualan p ON t.id_penjualan = p.id_penjualan
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    WHERE t.status_transaksi = 'selesai'
      AND t.tanggal_transaksi >= ?
      AND t.tanggal_transaksi < ?
    ORDER BY t.tanggal_transaksi DESC
");

mysqli_stmt_bind_param($stmtDetail, "ss", $awal, $akhir);
mysqli_stmt_execute($stmtDetail);
$detail = mysqli_stmt_get_result($stmtDetail);

function rupiah($angka) {
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Bulanan - SembakoKu</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            color: #263238;
        }

        .navbar {
            background: #16865c;
            color: white;
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .navbar h2 { margin: 0; font-size: 21px; }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 14px;
        }

        .container {
            max-width: 1150px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        h1 { margin: 0 0 7px; font-size: 26px; }
        .muted { color: #718096; font-size: 14px; }

        .card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #00000008;
            margin-bottom: 22px;
        }

        .filter {
            display: flex;
            align-items: end;
            flex-wrap: wrap;
            gap: 12px;
        }

        .field { display: flex; flex-direction: column; gap: 7px; }

        label { font-size: 13px; font-weight: bold; }

        select, button, .btn {
            padding: 10px 14px;
            border: 1px solid #dce3e8;
            border-radius: 7px;
            font-size: 14px;
        }

        button, .btn {
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .green { background: #16865c; color: white; }
        .gray { background: #e9eef0; color: #263238; }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .stat {
            background: white;
            padding: 22px;
            border-radius: 12px;
            border-left: 4px solid #16865c;
            box-shadow: 0 3px 12px #00000008;
        }

        .stat p { margin: 0 0 12px; color: #718096; font-size: 14px; }
        .stat h2 { margin: 0; font-size: 25px; overflow-wrap: anywhere; }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 13px 12px;
            border-bottom: 1px solid #edf0f2;
            text-align: left;
            font-size: 14px;
        }

        th { background: #f7f9fa; }
        .right { text-align: right; }

        .report-title { display: none; }

        @media (max-width: 700px) {
            .stats { grid-template-columns: 1fr; }
            .stat h2 { font-size: 22px; }
            .container { padding: 0 12px; }
        }

        @media print {
            body { background: white; }
            .navbar, .filter-card, .no-print { display: none !important; }
            .container { max-width: none; margin: 0; padding: 0; }
            .card, .stat { box-shadow: none; border: 1px solid #ddd; }
            .stats { grid-template-columns: repeat(3, 1fr); }
            .report-title { display: block; text-align: center; margin-bottom: 20px; }
            table { min-width: 0; }
            th, td { padding: 8px 5px; font-size: 11px; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🛒 SEMBAKOKU</h2>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="../transaksi/index.php">Riwayat Transaksi</a>
    </div>
</div>

<div class="container">

    <div class="header">
        <div>
            <h1>Report Bulanan</h1>
            <div class="muted">Laporan penjualan SembakoKu berdasarkan bulan dan tahun.</div>
        </div>
        <button class="green no-print" onclick="window.print()">Cetak Laporan</button>
    </div>

    <div class="card filter-card">
        <form method="GET" class="filter">
            <div class="field">
                <label for="bulan">Bulan</label>
                <select name="bulan" id="bulan">
                    <?php foreach ($namaBulan as $nomor => $nama): ?>
                        <option value="<?= $nomor ?>"
                            <?= $bulan === $nomor ? 'selected' : '' ?>>
                            <?= $nama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="tahun">Tahun</label>
                <select name="tahun" id="tahun">
                    <?php for ($y = (int) date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>"
                            <?= $tahun === $y ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="green">Tampilkan</button>
        </form>
    </div>

    <div class="report-title">
        <h2>SEMBAKOKU</h2>
        <h3>Laporan Penjualan Bulan <?= $namaBulan[$bulan] ?> <?= $tahun ?></h3>
    </div>

    <div class="stats">
        <div class="stat">
            <p>Total Penjualan</p>
            <h2><?= rupiah($ringkasan['total_penjualan']) ?></h2>
        </div>

        <div class="stat">
            <p>Transaksi Selesai</p>
            <h2><?= number_format((int) $ringkasan['jumlah_transaksi'], 0, ',', '.') ?></h2>
        </div>

        <div class="stat">
            <p>Jumlah Produk Terjual</p>
            <h2><?= number_format((int) $dataProduk['produk_terjual'], 0, ',', '.') ?> item</h2>
        </div>
    </div>

    <div class="card">
        <h3>Rincian Transaksi</h3>
        <p class="muted">
            Periode <?= $namaBulan[$bulan] ?> <?= $tahun ?>
        </p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th class="right">Total</th>
                        <th class="right">Uang Bayar</th>
                        <th class="right">Kembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($detail) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($detail)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>#<?= (int) $row['id_transaksi'] ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td class="right"><?= rupiah($row['total_transaksi']) ?></td>
                                <td class="right"><?= rupiah($row['uang_bayar']) ?></td>
                                <td class="right"><?= rupiah($row['uang_kembali']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <th colspan="4">TOTAL</th>
                            <th class="right"><?= rupiah($ringkasan['total_penjualan']) ?></th>
                            <th colspan="2"></th>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:25px">
                                Belum ada transaksi selesai pada bulan ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="no-print" style="margin-bottom:30px">
        <a href="index.php" class="btn gray">Kembali ke Dashboard</a>
    </div>

</div>
</body>
</html>