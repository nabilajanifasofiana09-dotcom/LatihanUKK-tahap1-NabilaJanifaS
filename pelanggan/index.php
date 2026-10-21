<?php
include "../koneksi.php";

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

$query = mysqli_query($koneksi, "
    SELECT * FROM pelanggan
    WHERE nama_pelanggan LIKE '%$cari%'
       OR alamat LIKE '%$cari%'
       OR no_telepon LIKE '%$cari%'
    ORDER BY id_pelanggan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Pelanggan - SembakoKu</title>

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
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        h2 {
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
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

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .search-box {
            background: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 11px 13px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #0d6efd;
            color: white;
            padding: 13px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .aksi {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        @media (max-width: 700px) {

            .navbar {
                padding: 15px 20px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-form {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="navbar">

    <div class="brand">
        🛒 SEMBAKOKU
    </div>

    <a href="../admin/" class="btn btn-secondary">
        Dashboard
    </a>

</div>

<div class="container">

    <div class="header">

        <h2>Data Pelanggan</h2>

        <a href="tambah.php" class="btn btn-primary">
            + Tambah Pelanggan
        </a>

    </div>

    <div class="search-box">

        <form method="GET" class="search-form">

            <input
                type="text"
                name="cari"
                class="search-input"
                placeholder="Cari nama, alamat, atau no. telepon..."
                value="<?= htmlspecialchars($cari); ?>"
            >

            <button
                type="submit"
                class="btn btn-primary"
            >
                🔍 Cari
            </button>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Reset
            </a>

        </form>

    </div>

    <div class="table-card">

        <table>

            <thead>

                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            if (mysqli_num_rows($query) > 0) {

                while ($data = mysqli_fetch_assoc($query)) {

            ?>

                <tr>

                    <td><?= $no++; ?></td>

                    <td>
                        <?= htmlspecialchars($data['nama_pelanggan']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($data['alamat']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($data['no_telepon']); ?>
                    </td>

                    <td>

                        <div class="aksi">

                            <a
                                href="edit.php?id=<?= $data['id_pelanggan']; ?>"
                                class="btn btn-warning"
                            >
                                Edit
                            </a>

                            <a
                                href="hapus.php?id=<?= $data['id_pelanggan']; ?>"
                                class="btn btn-danger"
                                onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                            >
                                Hapus
                            </a>

                        </div>

                    </td>

                </tr>

            <?php

                }

            } else {

            ?>

                <tr>

                    <td colspan="5" class="empty">
                        Data pelanggan tidak ditemukan.
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>