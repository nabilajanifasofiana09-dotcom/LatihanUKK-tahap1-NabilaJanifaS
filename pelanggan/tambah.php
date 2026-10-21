
<?php
session_start();
include "../koneksi.php";

if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama_pelanggan']);
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['no_telepon'] ?? '');

    // Gunakan prepared statement untuk menyimpan pelanggan.
    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO pelanggan (nama_pelanggan, alamat, no_telepon)
         VALUES (?, ?, ?)"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $nama,
            $alamat,
            $telepon
        );

        if (mysqli_stmt_execute($stmt)) {

            // Catat aktivitas jika pelanggan berhasil ditambahkan.
            if (isset($_SESSION['id_user'])) {
                $aktivitas = "Menambahkan pelanggan: " . $nama;
                $id_user = (int) $_SESSION['id_user'];

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
            }

            mysqli_stmt_close($stmt);

            header("Location: index.php");
            exit;

        } else {
            $pesanError = "Gagal menyimpan data pelanggan.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $pesanError = "Terjadi kesalahan pada query database.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan - SembakoKu</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .navbar {
            background: #0d6efd;
            color: white;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 11px 13px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .button-area {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .btn {
            padding: 11px 18px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #0d6efd;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .error {
            padding: 12px;
            margin-bottom: 18px;
            background: #ffebee;
            color: #c62828;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<div class="navbar">
    <div class="brand">🛒 SEMBAKOKU</div>
</div>

<div class="container">
    <div class="card">

        <h2>Tambah Pelanggan</h2>

        <?php if (isset($pesanError)): ?>
            <div class="error">
                <?= htmlspecialchars($pesanError); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Nama Pelanggan</label>
            <input
                type="text"
                name="nama_pelanggan"
                placeholder="Masukkan nama pelanggan"
                required
            >

            <label>Alamat</label>
            <textarea
                name="alamat"
                placeholder="Masukkan alamat pelanggan"
            ></textarea>

            <label>No. Telepon</label>
            <input
                type="text"
                name="no_telepon"
                placeholder="Contoh: 081234567890"
            >

            <div class="button-area">
                <button type="submit" name="simpan" class="btn btn-primary">
                    Simpan
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>