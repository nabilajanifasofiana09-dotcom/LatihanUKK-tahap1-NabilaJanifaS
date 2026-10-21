<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$query = mysqli_query(
    $koneksi,
    "SELECT log_aktivitas.*, users.nama_user
     FROM log_aktivitas
     LEFT JOIN users ON log_aktivitas.id_user = users.id_user
     ORDER BY log_aktivitas.waktu DESC
     LIMIT 200"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - SembakoKu</title>

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
            background: #198754;
            padding: 18px 4%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .navbar h2 {
            margin: 0;
            color: white;
            font-size: 21px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 16px;
            font-size: 14px;
        }

        .container {
            width: 94%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .header {
            margin-bottom: 22px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 27px;
        }

        .header p {
            color: #777;
            margin: 0;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        th {
            background: #198754;
            color: white;
        }

        .empty {
            text-align: center;
            color: #777;
            padding: 25px;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 16px;
            background: #e9ecef;
            color: #333;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
        }

        @media print {
            .navbar, .back {
                display: none;
            }

            .container {
                width: 100%;
                margin: 0;
            }

            .card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <h2>🛒 SEMBAKOKU</h2>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="header">
        <h1>Log Aktivitas Admin</h1>
        <p>Riwayat aktivitas yang tercatat di sistem SembakoKu.</p>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Admin</th>
                        <th>Aktivitas</th>
                        <th>Waktu</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($data = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?= htmlspecialchars(
                                        $data['nama_user'] ?? 'Admin tidak tersedia'
                                    ); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($data['aktivitas']); ?>
                                </td>
                                <td>
                                    <?= date(
                                        'd-m-Y H:i:s',
                                        strtotime($data['waktu'])
                                    ); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty">
                                Belum ada aktivitas yang tercatat.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="index.php" class="back">← Kembali ke Dashboard</a>
    <button onclick="window.print()" class="back" style="border:none;cursor:pointer">
        Cetak Log
    </button>
</div>

</body>
</html>