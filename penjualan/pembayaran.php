<?php

session_start();

include "../koneksi.php";

/* =========================
   CEK LOGIN
========================= */

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

$id_transaksi = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id_transaksi <= 0) {
    header("Location: index.php");
    exit;
}

/* =========================
   AMBIL DATA TRANSAKSI
========================= */

$stmt = mysqli_prepare(
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
    WHERE transaksi.id_transaksi = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_transaksi
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    echo "<script>
        alert('Data transaksi tidak ditemukan!');
        window.location='index.php';
    </script>";

    exit;
}

$transaksi = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* =========================
   CEK STATUS TRANSAKSI
========================= */

if ($transaksi['status_transaksi'] != 'pending') {

    header(
        "Location: struk.php?id=" . $id_transaksi
    );

    exit;
}


$error = "";


/* =========================
   PROSES PEMBAYARAN
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uang_bayar = isset($_POST['uang_bayar'])
        ? (float) $_POST['uang_bayar']
        : 0;

    $total = (float) $transaksi['total_transaksi'];


    /* =========================
       VALIDASI PEMBAYARAN
    ========================= */

    if ($uang_bayar <= 0) {

        $error = "Masukkan jumlah uang pembayaran.";

    } elseif ($uang_bayar < $total) {

        $error = "Uang pembayaran masih kurang.";

    } else {

        $uang_kembali = $uang_bayar - $total;


        /* =========================
           UPDATE TRANSAKSI
        ========================= */

        $stmtUpdate = mysqli_prepare(
            $koneksi,
            "UPDATE transaksi
             SET
                uang_bayar = ?,
                uang_kembali = ?,
                status_transaksi = 'selesai'
             WHERE id_transaksi = ?"
        );

        mysqli_stmt_bind_param(
            $stmtUpdate,
            "ddi",
            $uang_bayar,
            $uang_kembali,
            $id_transaksi
        );

        $update = mysqli_stmt_execute($stmtUpdate);

        mysqli_stmt_close($stmtUpdate);


        /* =========================
           SIMPAN LOG AKTIVITAS
        ========================= */

        if ($update) {

            $aktivitas =
                "Menyelesaikan transaksi #" .
                $id_transaksi .
                " - Pelanggan: " .
                $transaksi['nama_pelanggan'];

            $stmtLog = mysqli_prepare(
                $koneksi,
                "INSERT INTO log_aktivitas
                (id_user, aktivitas)
                VALUES (?, ?)"
            );

            if ($stmtLog) {

                mysqli_stmt_bind_param(
                    $stmtLog,
                    "is",
                    $id_user,
                    $aktivitas
                );

                mysqli_stmt_execute($stmtLog);

                mysqli_stmt_close($stmtLog);
            }


            /* =========================
               KE STRUK
            ========================= */

            header(
                "Location: struk.php?id=" . $id_transaksi
            );

            exit;

        } else {

            $error = "Pembayaran gagal disimpan.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pembayaran - SembakoKu</title>

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
            width: 90%;
            max-width: 1100px;
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

        .back {
            background: white;
            color: #333;
            text-decoration: none;
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 50px auto;
        }

        .payment-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title .icon {
            font-size: 55px;
            margin-bottom: 10px;
        }

        .title h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .title p {
            color: #777;
        }

        .info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #777;
        }

        .info-value {
            font-weight: bold;
        }

        .total {
            background: #e8f5e9;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
        }

        .total-label {
            color: #555;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .total-price {
            color: #198754;
            font-size: 32px;
            font-weight: bold;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 17px;
            outline: none;
        }

        input:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25,135,84,0.1);
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            border: none;
            padding: 14px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            background: #198754;
            color: white;
        }

        .btn:hover {
            background: #157347;
        }

        @media (max-width: 600px) {

            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }

            .payment-card {
                padding: 25px 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 4px;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="navbar-container">

        <a href="index.php" class="brand">
            🛒 SEMBAKOKU
        </a>

        <a href="index.php" class="back">
            ← Kembali ke Kasir
        </a>

    </div>

</nav>


<div class="container">

    <div class="payment-card">

        <div class="title">

            <div class="icon">
                💳
            </div>

            <h1>Pembayaran</h1>

            <p>
                Silakan masukkan uang pembayaran pelanggan.
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
                        strtotime(
                            $transaksi['tanggal_transaksi']
                        )
                    ); ?>

                </span>

            </div>

        </div>


        <div class="total">

            <div class="total-label">
                Total yang harus dibayar
            </div>

            <div class="total-price">

                Rp <?= number_format(
                    $transaksi['total_transaksi'],
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

        </div>


        <?php if ($error != "") { ?>

            <div class="error">
                ⚠️ <?= htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <label for="uang_bayar">
                Uang Bayar
            </label>

            <input
                type="number"
                name="uang_bayar"
                id="uang_bayar"
                min="<?= $transaksi['total_transaksi']; ?>"
                step="1"
                placeholder="Contoh: 200000"
                required
                autofocus
            >


            <button
                type="submit"
                class="btn">

                💰 Proses Pembayaran

            </button>

        </form>

    </div>

</div>

</body>

</html>