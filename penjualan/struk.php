<?php

include "../koneksi.php";

$id_transaksi = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id_transaksi <= 0) {
    header("Location: index.php");
    exit;
}

$query = mysqli_query(
    $koneksi,
    "SELECT
        transaksi.*,
        penjualan.id_penjualan,
        penjualan.id_pelanggan,
        penjualan.tanggal_penjualan,
        pelanggan.nama_pelanggan
    FROM transaksi
    INNER JOIN penjualan
        ON transaksi.id_penjualan = penjualan.id_penjualan
    INNER JOIN pelanggan
        ON penjualan.id_pelanggan = pelanggan.id_pelanggan
    WHERE transaksi.id_transaksi = '$id_transaksi'"
);

if (mysqli_num_rows($query) == 0) {
    echo "<script>
        alert('Data transaksi tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit;
}

$transaksi = mysqli_fetch_assoc($query);

$id_penjualan = $transaksi['id_penjualan'];

$query_detail = mysqli_query(
    $koneksi,
    "SELECT
        detail_penjualan.*,
        produk.nama_produk,
        produk.harga
    FROM detail_penjualan
    INNER JOIN produk
        ON detail_penjualan.id_produk = produk.id_produk
    WHERE detail_penjualan.id_penjualan = '$id_penjualan'"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Struk Transaksi - SembakoKu</title>

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

        .container {
            width: 90%;
            max-width: 650px;
            margin: 40px auto;
        }

        .receipt {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header .icon {
            font-size: 45px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 28px;
            color: #198754;
            margin-bottom: 5px;
        }

        .header p {
            color: #777;
            font-size: 14px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
        }

        .info-label {
            color: #777;
        }

        .info-value {
            font-weight: bold;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #198754;
            color: white;
            padding: 11px 8px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 11px 8px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
            border-top: 2px dashed #ccc;
            padding-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
        }

        .total-row.grand-total {
            font-size: 21px;
            font-weight: bold;
            color: #198754;
            border-top: 1px solid #ddd;
            margin-top: 8px;
            padding-top: 12px;
        }

        .payment {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }

        .thank-you {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed #ccc;
        }

        .thank-you h3 {
            color: #198754;
            margin-bottom: 5px;
        }

        .thank-you p {
            color: #777;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 13px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-print {
            background: #198754;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media (max-width: 600px) {

            .container {
                width: 95%;
                margin: 20px auto;
            }

            .receipt {
                padding: 20px 15px;
            }

            .actions {
                flex-direction: column;
            }

            th,
            td {
                font-size: 12px;
            }

        }

        @media print {

            body {
                background: white;
            }

            .container {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .receipt {
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
            }

            .actions {
                display: none;
            }

            @page {
                margin: 10mm;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="receipt">

        <div class="header">

            <div class="icon">
                🛒
            </div>

            <h1>SEMBAKOKU</h1>

            <p>
                Belanja Hemat, Kebutuhan Lengkap.
            </p>

            <p>
                Struk Pembayaran
            </p>

        </div>

        <div class="info">

            <div class="info-row">

                <span class="info-label">
                    No. Transaksi
                </span>

                <span class="info-value">
                    #<?= $transaksi['id_transaksi']; ?>
                </span>

            </div>

            <div class="info-row">

                <span class="info-label">
                    Pelanggan
                </span>

                <span class="info-value">
                    <?= htmlspecialchars(
                        $transaksi['nama_pelanggan']
                    ); ?>
                </span>

            </div>

            <div class="info-row">

                <span class="info-label">
                    Tanggal
                </span>

                <span class="info-value">
                    <?= date(
                        'd-m-Y H:i',
                        strtotime($transaksi['tanggal_transaksi'])
                    ); ?>
                </span>

            </div>

        </div>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            Produk
                        </th>

                        <th class="text-center">
                            Qty
                        </th>

                        <th class="text-right">
                            Harga
                        </th>

                        <th class="text-right">
                            Subtotal
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($detail = mysqli_fetch_assoc($query_detail)) { ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $detail['nama_produk']
                                ); ?>
                            </td>

                            <td class="text-center">
                                <?= $detail['jumlah']; ?>
                            </td>

                            <td class="text-right">
                                Rp <?= number_format(
                                    $detail['harga'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td class="text-right">
                                Rp <?= number_format(
                                    $detail['subtotal'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

        <div class="total-section">

            <div class="total-row grand-total">

                <span>
                    Total
                </span>

                <span>
                    Rp <?= number_format(
                        $transaksi['total_transaksi'],
                        0,
                        ',',
                        '.'
                    ); ?>
                </span>

            </div>

        </div>

        <div class="payment">

            <div class="total-row">

                <span>
                    Uang Bayar
                </span>

                <strong>
                    Rp <?= number_format(
                        $transaksi['uang_bayar'],
                        0,
                        ',',
                        '.'
                    ); ?>
                </strong>

            </div>

            <div class="total-row">

                <span>
                    Kembalian
                </span>

                <strong>
                    Rp <?= number_format(
                        $transaksi['uang_kembali'],
                        0,
                        ',',
                        '.'
                    ); ?>
                </strong>

            </div>

        </div>

        <div class="thank-you">

            <h3>
                Terima Kasih! 💚
            </h3>

            <p>
                Terima kasih telah berbelanja di SembakoKu.
            </p>

        </div>

        <div class="actions">

            <a
                href="index.php"
                class="btn btn-back">

                ← Kembali ke Kasir

            </a>

            <button
                onclick="window.print()"
                class="btn btn-print">

                🖨️ Cetak Struk

            </button>

        </div>

    </div>

</div>

</body>

</html>