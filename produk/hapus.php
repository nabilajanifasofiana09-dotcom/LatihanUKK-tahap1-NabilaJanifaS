<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "<script>
        alert('ID produk tidak valid!');
        window.location='index.php';
    </script>";
    exit;
}

/* Ambil data produk */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama_produk FROM produk WHERE id_produk = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    echo "<script>
        alert('Produk tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit;
}

$nama = $data['nama_produk'];

/* Cek apakah produk sudah digunakan dalam transaksi */
$stmtCek = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM detail_penjualan
     WHERE id_produk = ?"
);

mysqli_stmt_bind_param($stmtCek, "i", $id);
mysqli_stmt_execute($stmtCek);

$resultCek = mysqli_stmt_get_result($stmtCek);
$dataCek = mysqli_fetch_assoc($resultCek);

mysqli_stmt_close($stmtCek);

if ($dataCek['jumlah'] > 0) {
    echo "<script>
        alert('Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        window.location='index.php';
    </script>";
    exit;
}

/* Hapus produk */
$stmtDelete = mysqli_prepare(
    $koneksi,
    "DELETE FROM produk WHERE id_produk = ?"
);

mysqli_stmt_bind_param($stmtDelete, "i", $id);

if (mysqli_stmt_execute($stmtDelete)) {

    mysqli_stmt_close($stmtDelete);

    /* Catat aktivitas */
    $id_user = (int) $_SESSION['id_user'];
    $aktivitas = "Menghapus produk: " . $nama;

    $stmtLog = mysqli_prepare(
        $koneksi,
        "INSERT INTO log_aktivitas (id_user, aktivitas)
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

    header("Location: index.php");
    exit;

} else {

    mysqli_stmt_close($stmtDelete);

    echo "<script>
        alert('Produk gagal dihapus.');
        window.location='index.php';
    </script>";
    exit;
}
?>