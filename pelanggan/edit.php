
<?php
session_start();
include "../koneksi.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "<script>
            alert('ID pelanggan tidak valid!');
            window.location='index.php';
          </script>";
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM pelanggan WHERE id_pelanggan = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($query);

mysqli_stmt_close($stmt);

if (!$data) {
    echo "<script>
            alert('Data pelanggan tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit;
}

if (isset($_POST['update'])) {

    $nama = trim($_POST['nama_pelanggan']);
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['no_telepon'] ?? '');

    $stmtUpdate = mysqli_prepare(
        $koneksi,
        "UPDATE pelanggan
         SET nama_pelanggan = ?, alamat = ?, no_telepon = ?
         WHERE id_pelanggan = ?"
    );

    mysqli_stmt_bind_param(
        $stmtUpdate,
        "sssi",
        $nama,
        $alamat,
        $telepon,
        $id
    );

    if (mysqli_stmt_execute($stmtUpdate)) {

        // Catat aktivitas edit pelanggan.
        if (isset($_SESSION['id_user'])) {

            $id_user = (int) $_SESSION['id_user'];
            $aktivitas = "Mengedit data pelanggan: " . $nama;

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

        mysqli_stmt_close($stmtUpdate);

        header("Location: index.php");
        exit;

    } else {
        $pesanError = "Gagal memperbarui data pelanggan.";
        mysqli_stmt_close($stmtUpdate);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan - SembakoKu</title>

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
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            margin-bottom: 18px;
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

        <h2>Edit Pelanggan</h2>

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
                value="<?= htmlspecialchars($data['nama_pelanggan']); ?>"
                required
            >

            <label>Alamat</label>
            <textarea name="alamat"><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>

            <label>No. Telepon</label>
            <input
                type="text"
                name="no_telepon"
                value="<?= htmlspecialchars($data['no_telepon'] ?? ''); ?>"
            >

            <div class="button-area">
                <button type="submit" name="update" class="btn btn-primary">
                    Simpan Perubahan
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