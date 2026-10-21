
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kasir";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi untuk mencatat aktivitas admin.
function catatAktivitas($koneksi, $aktivitas)
{
    if (!isset($_SESSION['id_user'])) {
        return;
    }

    $id_user = (int) $_SESSION['id_user'];

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO log_aktivitas (id_user, aktivitas)
         VALUES (?, ?)"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $id_user,
            $aktivitas
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>