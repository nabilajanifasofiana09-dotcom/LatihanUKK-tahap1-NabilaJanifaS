<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['id_user'])) {
    $id_user = (int) $_SESSION['id_user'];

    $stmtLog = mysqli_prepare(
        $koneksi,
        "INSERT INTO log_aktivitas (id_user, aktivitas)
         VALUES (?, ?)"
    );

    if ($stmtLog) {
        $aktivitas = "Logout dari sistem";

        mysqli_stmt_bind_param(
            $stmtLog,
            "is",
            $id_user,
            $aktivitas
        );

        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
}

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>