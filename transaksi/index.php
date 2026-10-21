<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$query = mysqli_query(
    $koneksi,
    "SELECT
        transaksi.id_transaksi,
        transaksi.id_penjualan,
        transaksi.tanggal_transaksi,
        transaksi.total_transaksi,
        transaksi.uang_bayar,
        transaksi.uang_kembali,
        transaksi.status_transaksi,
        pelanggan.nama_pelanggan
    FROM transaksi
    INNER JOIN penjualan
        ON transaksi.id_penjualan = penjualan.id_penjualan
    INNER JOIN pelanggan
        ON penjualan.id_pelanggan = pelanggan.id_pelanggan
    ORDER BY transaksi.id_transaksi DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Riwayat Transaksi - SembakoKu</title>

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

        .nav-right {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .page-header p {
            color: #777;
        }

        .btn-new {
            background: #198754;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            background: #198754;
            color: white;
            padding: 13px 10px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 13px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover td {
            background: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .selesai {
            background: #d1e7dd;
            color: #0f5132;
        }

        .pending {
            background: #fff3cd;
            color: #664d03;
        }

        .batal {
            background: #f8d7da;
            color: #842029;
        }

        .btn-struk {
            display: inline-block;
            background: #0d6efd;
            color: white;
            text-decoration: none;
            padding: 7px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 40px 20px;
            color: #777;
        }

        @media (max-width: 700px) {

            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card {
                padding: 15px;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="navbar-container">

        <a href="../admin/" class="brand">
            🛒 SEMBAKOKU
        </a>

        <div class="nav-right">

            <a href="../admin/" class="nav-btn">
                Dashboard
            </a>

            <a href="../logout.php" class="nav-btn">
                Logout
            </a>

        </div>

    </div>

</nav>

<div class="container">

    <div class="page-header">

        <div>

            <h1>
                Riwayat Transaksi
            </h1>

            <p>
                Daftar transaksi penjualan SembakoKu.
            </p>

        </div>

        <a
            href="../penjualan/"
            class="btn-new">

            + Transaksi Baru

        </a>

    </div>

    <div class="card">

        <?php if (mysqli_num_rows($query) > 0) { ?>

            <table>

                <thead>

                    <tr>

                        <th class="text-center">
                            No
                        </th>

                        <th>
                            ID Transaksi
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th class="text-right">
                            Total
                        </th>

                        <th class="text-right">
                            Uang Bayar
                        </th>

                        <th class="text-right">
                            Kembalian
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                        <th class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    $no = 1;

                    while ($data = mysqli_fetch_assoc($query)) {

                    ?>

                        <tr>

                            <td class="text-center">
                                <?= $no++; ?>
                            </td>

                            <td>
                                #<?= $data['id_transaksi']; ?>
                            </td>

                            <td>
                                <?= date(
                                    'd-m-Y H:i',
                                    strtotime($data['tanggal_transaksi'])
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $data['nama_pelanggan']
                                ); ?>
                            </td>

                            <td class="text-right">
                                Rp <?= number_format(
                                    $data['total_transaksi'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td class="text-right">
                                Rp <?= number_format(
                                    $data['uang_bayar'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td class="text-right">
                                Rp <?= number_format(
                                    $data['uang_kembali'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td class="text-center">

                                <?php if ($data['status_transaksi'] == 'selesai') { ?>

                                    <span class="status selesai">
                                        Selesai
                                    </span>

                                <?php } elseif ($data['status_transaksi'] == 'pending') { ?>

                                    <span class="status pending">
                                        Pending
                                    </span>

                                <?php } else { ?>

                                    <span class="status batal">
                                        Batal
                                    </span>

                                <?php } ?>

                            </td>

                            <td class="text-center">

                                <?php if ($data['status_transaksi'] == 'selesai') { ?>

                                    <a
                                        href="../penjualan/struk.php?id=<?= $data['id_transaksi']; ?>"
                                        class="btn-struk">

                                        🧾 Lihat Struk

                                    </a>

                                <?php } else { ?>

                                    <span style="color:#999;">
                                        -
                                    </span>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="empty">

                <h3>
                    Belum Ada Transaksi
                </h3>

                <p>
                    Belum ada transaksi penjualan yang tersimpan.
                </p>

            </div>

        <?php } ?>

    </div>

</div>

</body>

</html>