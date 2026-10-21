<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

$id_pelanggan = isset($_POST['id_pelanggan'])
    ? (int) $_POST['id_pelanggan']
    : 0;

$jumlah_produk = isset($_POST['jumlah'])
    ? $_POST['jumlah']
    : [];


// Validasi pelanggan
if ($id_pelanggan <= 0) {
    echo "<script>
        alert('Silakan pilih pelanggan terlebih dahulu!');
        window.location='index.php';
    </script>";
    exit;
}


// Cek pelanggan
$stmtPelanggan = mysqli_prepare(
    $koneksi,
    "SELECT nama_pelanggan
     FROM pelanggan
     WHERE id_pelanggan = ?"
);

mysqli_stmt_bind_param(
    $stmtPelanggan,
    "i",
    $id_pelanggan
);

mysqli_stmt_execute($stmtPelanggan);

$resultPelanggan = mysqli_stmt_get_result($stmtPelanggan);
$pelanggan = mysqli_fetch_assoc($resultPelanggan);

mysqli_stmt_close($stmtPelanggan);

if (!$pelanggan) {
    echo "<script>
        alert('Data pelanggan tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit;
}

$nama_pelanggan = $pelanggan['nama_pelanggan'];


// Siapkan data produk yang dibeli
$detail = [];
$total = 0;

foreach ($jumlah_produk as $id_produk => $jumlah) {

    $id_produk = (int) $id_produk;
    $jumlah = (int) $jumlah;

    if ($jumlah <= 0) {
        continue;
    }


    // Ambil data produk
    $stmtProduk = mysqli_prepare(
        $koneksi,
        "SELECT id_produk, nama_produk, harga, stok
         FROM produk
         WHERE id_produk = ?"
    );

    mysqli_stmt_bind_param(
        $stmtProduk,
        "i",
        $id_produk
    );

    mysqli_stmt_execute($stmtProduk);

    $resultProduk = mysqli_stmt_get_result($stmtProduk);
    $produk = mysqli_fetch_assoc($resultProduk);

    mysqli_stmt_close($stmtProduk);


    if (!$produk) {
        echo "<script>
            alert('Produk tidak ditemukan!');
            window.location='index.php';
        </script>";
        exit;
    }


    // Cek stok
    if ($jumlah > $produk['stok']) {

        $nama_produk = addslashes($produk['nama_produk']);

        echo "<script>
            alert('Stok produk $nama_produk tidak mencukupi!');
            window.location='index.php';
        </script>";

        exit;
    }


    // Hitung subtotal
    $subtotal = $produk['harga'] * $jumlah;

    $total += $subtotal;


    $detail[] = [
        'id_produk' => $id_produk,
        'jumlah' => $jumlah,
        'harga' => $produk['harga'],
        'subtotal' => $subtotal
    ];
}


// Tidak ada produk
if (count($detail) == 0) {

    echo "<script>
        alert('Silakan masukkan minimal satu produk!');
        window.location='index.php';
    </script>";

    exit;
}


// Mulai transaksi database
mysqli_begin_transaction($koneksi);

try {

    /*
     * 1. Simpan ke tabel penjualan
     */
    $tanggal = date('Y-m-d');

    $stmtPenjualan = mysqli_prepare(
        $koneksi,
        "INSERT INTO penjualan
        (id_pelanggan, tanggal_penjualan, total)
        VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmtPenjualan,
        "isi",
        $id_pelanggan,
        $tanggal,
        $total
    );

    if (!mysqli_stmt_execute($stmtPenjualan)) {
        throw new Exception(
            "Gagal menyimpan data penjualan."
        );
    }

    mysqli_stmt_close($stmtPenjualan);


    // Ambil ID penjualan
    $id_penjualan = mysqli_insert_id($koneksi);


    /*
     * 2. Simpan detail penjualan
     */
    foreach ($detail as $item) {

        $id_produk = $item['id_produk'];
        $jumlah = $item['jumlah'];
        $subtotal = $item['subtotal'];


        $stmtDetail = mysqli_prepare(
            $koneksi,
            "INSERT INTO detail_penjualan
            (id_penjualan, id_produk, jumlah, subtotal)
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmtDetail,
            "iiii",
            $id_penjualan,
            $id_produk,
            $jumlah,
            $subtotal
        );


        if (!mysqli_stmt_execute($stmtDetail)) {
            mysqli_stmt_close($stmtDetail);

            throw new Exception(
                "Gagal menyimpan detail penjualan."
            );
        }

        mysqli_stmt_close($stmtDetail);


        /*
         * 3. Kurangi stok
         */
        $stmtStok = mysqli_prepare(
            $koneksi,
            "UPDATE produk
             SET stok = stok - ?
             WHERE id_produk = ?
             AND stok >= ?"
        );

        mysqli_stmt_bind_param(
            $stmtStok,
            "iii",
            $jumlah,
            $id_produk,
            $jumlah
        );


        if (
            !mysqli_stmt_execute($stmtStok) ||
            mysqli_affected_rows($koneksi) == 0
        ) {
            mysqli_stmt_close($stmtStok);

            throw new Exception(
                "Stok produk tidak mencukupi."
            );
        }

        mysqli_stmt_close($stmtStok);
    }


    /*
     * 4. Simpan transaksi pembayaran
     */
    $stmtTransaksi = mysqli_prepare(
        $koneksi,
        "INSERT INTO transaksi
        (
            id_penjualan,
            tanggal_transaksi,
            total_transaksi,
            uang_bayar,
            uang_kembali,
            status_transaksi
        )
        VALUES
        (?, NOW(), ?, 0, 0, 'pending')"
    );

    mysqli_stmt_bind_param(
        $stmtTransaksi,
        "ii",
        $id_penjualan,
        $total
    );


    if (!mysqli_stmt_execute($stmtTransaksi)) {
        mysqli_stmt_close($stmtTransaksi);

        throw new Exception(
            "Gagal membuat data transaksi."
        );
    }

    mysqli_stmt_close($stmtTransaksi);


    // Ambil ID transaksi
    $id_transaksi = mysqli_insert_id($koneksi);


    /*
     * 5. Catat aktivitas
     */
    $aktivitas =
        "Membuat transaksi #" .
        $id_transaksi .
        " - Pelanggan: " .
        $nama_pelanggan;

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


    // Semua berhasil
    mysqli_commit($koneksi);


    // Lanjut ke pembayaran
    header(
        "Location: pembayaran.php?id=" . $id_transaksi
    );

    exit;


} catch (Exception $e) {

    // Batalkan semua perubahan
    mysqli_rollback($koneksi);

    $pesan = addslashes($e->getMessage());

    echo "<script>
        alert('Transaksi gagal: $pesan');
        window.location='index.php';
    </script>";

    exit;
}

?>