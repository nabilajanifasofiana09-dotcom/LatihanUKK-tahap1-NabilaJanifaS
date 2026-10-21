<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM pelanggan"
);
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$total_pelanggan = $data_pelanggan['total'];

$query_produk = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM produk"
);
$data_produk = mysqli_fetch_assoc($query_produk);
$total_produk = $data_produk['total'];

$query_transaksi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM transaksi
     WHERE status_transaksi = 'selesai'"
);
$data_transaksi = mysqli_fetch_assoc($query_transaksi);
$total_transaksi = $data_transaksi['total'];

$query_penjualan = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(total_transaksi), 0) AS total
     FROM transaksi
     WHERE status_transaksi = 'selesai'"
);
$data_penjualan = mysqli_fetch_assoc($query_penjualan);
$total_penjualan = $data_penjualan['total'];

$query_terbaru = mysqli_query(
    $koneksi,
    "SELECT
        transaksi.id_transaksi,
        transaksi.tanggal_transaksi,
        transaksi.total_transaksi,
        pelanggan.nama_pelanggan
     FROM transaksi
     INNER JOIN penjualan
        ON transaksi.id_penjualan = penjualan.id_penjualan
     INNER JOIN pelanggan
        ON penjualan.id_pelanggan = pelanggan.id_pelanggan
     WHERE transaksi.status_transaksi = 'selesai'
     ORDER BY transaksi.id_transaksi DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SembakoKu</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .navbar {
            background: #198754;
            padding: 18px 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }

        .navbar-container {
            width: 92%;
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            color: white;
            text-decoration: none;
            font-size: 21px;
            font-weight: bold;
        }

        .admin-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-name {
            color: white;
            font-size: 14px;
        }

        .logout {
            background: white;
            color: #333;
            text-decoration: none;
            padding: 9px 15px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
        }

        .container {
            width: 92%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .welcome {
            margin-bottom: 30px;
        }

        .welcome h1 {
            font-size: 30px;
            margin-bottom: 7px;
        }

        .welcome p {
            color: #777;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 23px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .stat-title {
            color: #777;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .stat-number {
            font-size: 26px;
            font-weight: bold;
            color: #198754;
            overflow-wrap: anywhere;
        }

        .menu-title {
            font-size: 21px;
            margin-bottom: 15px;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .menu-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            transition: 0.2s;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .menu-icon {
            font-size: 35px;
            margin-bottom: 13px;
        }

        .menu-card h3 {
            margin-bottom: 7px;
        }

        .menu-card p {
            color: #777;
            font-size: 13px;
            line-height: 1.5;
        }

        .recent-card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            overflow-x: auto;
        }

        .recent-card h2 {
            margin-bottom: 20px;
            font-size: 21px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th {
            background: #198754;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .text-right {
            text-align: right;
        }

        .empty {
            text-align: center;
            color: #777;
            padding: 25px;
        }

        .info {
            background: #e8f5e9;
            border-left: 5px solid #198754;
            padding: 18px;
            border-radius: 8px;
            margin-top: 30px;
            line-height: 1.7;
        }

        .info strong {
            color: #198754;
        }

        @media (max-width: 900px) {
            .stats,
            .menu {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }

            .admin-area {
                flex-direction: column;
                gap: 8px;
            }

            .stats,
            .menu {
                grid-template-columns: 1fr;
            }

            .container {
                width: 94%;
            }

            .welcome h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="../" class="brand">🛒 SEMBAKOKU</a>

        <div class="admin-area">
            <span class="admin-name">
                👤 <?= htmlspecialchars($_SESSION['nama_user']); ?>
            </span>

            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </div>
</nav>

<div class="container">

    <div class="welcome">
        <h1>Dashboard Admin 👋</h1>
        <p>Selamat datang di halaman pengelolaan SembakoKu.</p>
    </div>

    <div class="stats">

        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-title">Total Pelanggan</div>
            <div class="stat-number">
                <?= $total_pelanggan; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-title">Total Produk</div>
            <div class="stat-number">
                <?= $total_produk; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🧾</div>
            <div class="stat-title">Transaksi Selesai</div>
            <div class="stat-number">
                <?= $total_transaksi; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-title">Total Penjualan</div>
            <div class="stat-number">
                Rp <?= number_format($total_penjualan, 0, ',', '.'); ?>
            </div>
        </div>

    </div>

    <h2 class="menu-title">Menu Pengelolaan</h2>

    <div class="menu">

        <a href="../pelanggan/" class="menu-card">
            <div class="menu-icon">👥</div>
            <h3>Data Pelanggan</h3>
            <p>Kelola data pelanggan.</p>
        </a>

        <a href="../produk/" class="menu-card">
            <div class="menu-icon">📦</div>
            <h3>Data Produk</h3>
            <p>Kelola produk dan stok.</p>
        </a>

        <a href="../penjualan/" class="menu-card">
            <div class="menu-icon">🛒</div>
            <h3>Kasir</h3>
            <p>Buat transaksi penjualan baru.</p>
        </a>

        <a href="../transaksi/" class="menu-card">
            <div class="menu-icon">🧾</div>
            <h3>Riwayat Transaksi</h3>
            <p>Lihat transaksi yang telah selesai.</p>
        </a>

        <a href="report.php" class="menu-card">
            <div class="menu-icon">📊</div>
            <h3>Report Bulanan</h3>
            <p>Lihat ringkasan dan rincian penjualan berdasarkan bulan dan tahun serta cetak laporan.</p>
        </a>

        <a href="log_aktivitas.php" class="menu-card">
            <div class="menu-icon">📋</div>
            <h3>Log Aktivitas</h3>
            <p>Lihat riwayat aktivitas admin yang tercatat di sistem.</p>
        </a>

    </div>

    <div class="recent-card">
        <h2>Transaksi Terbaru</h2>

        <?php if (mysqli_num_rows($query_terbaru) > 0) { ?>

            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($data = mysqli_fetch_assoc($query_terbaru)) { ?>
                        <tr>
                            <td>#<?= $data['id_transaksi']; ?></td>

                            <td>
                                <?= date(
                                    'd-m-Y H:i',
                                    strtotime($data['tanggal_transaksi'])
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($data['nama_pelanggan']); ?>
                            </td>

                            <td class="text-right">
                                Rp <?= number_format(
                                    $data['total_transaksi'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        <?php } else { ?>

            <div class="empty">
                Belum ada transaksi selesai.
            </div>

        <?php } ?>
    </div>

    <div class="info">
        <strong>SembakoKu</strong>
        <br>
        Belanja Hemat, Kebutuhan Lengkap.
        <br>
        <span>
            Sistem pengelolaan data pelanggan, produk, stok,
            transaksi penjualan, laporan bulanan, dan log aktivitas.
        </span>
    </div>

</div>

</body>
</html>