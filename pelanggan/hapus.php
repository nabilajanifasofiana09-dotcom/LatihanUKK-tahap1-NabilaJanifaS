
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
        alert('ID pelanggan tidak valid!');
        window.location='index.php';
    </script>";
    exit;
}

// Ambil nama pelanggan sebelum data dihapus.
$stmtNama = mysqli_prepare(
    $koneksi,
    "SELECT nama_pelanggan FROM pelanggan WHERE id_pelanggan = ?"
);

mysqli_stmt_bind_param($stmtNama, "i", $id);
mysqli_stmt_execute($stmtNama);

$resultNama = mysqli_stmt_get_result($stmtNama);
$dataPelanggan = mysqli_fetch_assoc($resultNama);

mysqli_stmt_close($stmtNama);

if (!$dataPelanggan) {
    echo "<script>
        alert('Data pelanggan tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit;
}

$nama = $dataPelanggan['nama_pelanggan'];

// Cek apakah pelanggan memiliki riwayat transaksi.
$stmtCek = mysqli_prepare(
    $koneksi,
    "SELECT id_penjualan
     FROM penjualan
     WHERE id_pelanggan = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmtCek, "i", $id);
mysqli_stmt_execute($stmtCek);

$resultCek = mysqli_stmt_get_result($stmtCek);
$adaTransaksi = mysqli_num_rows($resultCek) > 0;

mysqli_stmt_close($stmtCek);

if ($adaTransaksi) {
    echo "<script>
        alert('Pelanggan tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        window.location='index.php';
    </script>";
    exit;
}

// Hapus pelanggan.
$stmtHapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM pelanggan WHERE id_pelanggan = ?"
);

mysqli_stmt_bind_param($stmtHapus, "i", $id);

if (mysqli_stmt_execute($stmtHapus) &&
    mysqli_stmt_affected_rows($stmtHapus) > 0) {

    mysqli_stmt_close($stmtHapus);

    // Catat aktivitas setelah penghapusan berhasil.
    $id_user = (int) $_SESSION['id_user'];
    $aktivitas = "Menghapus pelanggan: " . $nama;

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

    echo "<script>
        alert('Data pelanggan berhasil dihapus.');
        window.location='index.php';
    </script>";

} else {
    mysqli_stmt_close($stmtHapus);

    echo "<script>
        alert('Data pelanggan gagal dihapus.');
        window.location='index.php';
    </script>";
}

exit;
?>