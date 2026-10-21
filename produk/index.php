<?php
include "../koneksi.php";

$query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id_produk DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - SembakoKu</title>

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

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .stok-aman {
            background: #d1e7dd;
            color: #0f5132;
        }

        .stok-menipis {
            background: #fff3cd;
            color: #664d03;
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
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar {
                padding: 15px 20px;
            }

            .container {
                margin-top: 25px;
            }
        }
    </style>
</head>

<body>

<div class="navbar">
    <div class="brand">🛒 SEMBAKOKU</div>

    <a href="../admin/" class="btn btn-secondary">
        Dashboard
    </a>
</div>

<div class="container">

    <div class="header">
        <h2>Data Produk</h2>

        <a href="tambah.php" class="btn btn-primary">
            + Tambah Produk
        </a>
    </div>

    <div class="table-card">

        <table>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status Stok</th>
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
                        <?= htmlspecialchars($data['nama_produk']); ?>
                    </td>

                    <td>
                        Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                    </td>

                    <td>
                        <?= $data['stok']; ?>
                    </td>

                    <td>

                        <?php if ($data['stok'] <= 5) { ?>

                            <span class="badge stok-menipis">
                                Stok Menipis
                            </span>

                        <?php } else { ?>

                            <span class="badge stok-aman">
                                Stok Aman
                            </span>

                        <?php } ?>

                    </td>

                    <td>

                        <div class="aksi">

                            <a
                                href="edit.php?id=<?= $data['id_produk']; ?>"
                                class="btn btn-warning"
                            >
                                Edit
                            </a>

                            <a
                                href="hapus.php?id=<?= $data['id_produk']; ?>"
                                class="btn btn-danger"
                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
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
                    <td colspan="6" class="empty">
                        Belum ada data produk.
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>