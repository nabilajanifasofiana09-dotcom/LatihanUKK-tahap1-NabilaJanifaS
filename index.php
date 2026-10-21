<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>SembakoKu - Beranda</title>

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

        /* NAVBAR */

        .navbar {
            background: #0d6efd;
            color: white;
            padding: 16px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .login-btn {
            background: white;
            color: #0d6efd;
        }

        .kasir-btn {
            background: #ffc107;
            color: #212529;
        }

        /* HERO */

        .hero {
            min-height: 470px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            background: white;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero-icon {
            font-size: 70px;
            margin-bottom: 10px;
        }

        .hero h1 {
            font-size: 48px;
            margin: 5px 0 10px;
            color: #0d6efd;
        }

        .hero h2 {
            font-size: 23px;
            margin: 0 0 18px;
            color: #444;
        }

        .hero p {
            font-size: 17px;
            line-height: 1.7;
            color: #666;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .main-btn {
            display: inline-block;
            padding: 13px 25px;
            border-radius: 9px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
        }

        .main-login {
            background: #0d6efd;
            color: white;
        }

        .main-kasir {
            background: #ffc107;
            color: #212529;
        }

        /* FITUR */

        .section {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-title h2 {
            margin-bottom: 8px;
        }

        .section-title p {
            color: #777;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 30px 25px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        .card-icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .card h3 {
            margin-bottom: 10px;
        }

        .card p {
            color: #777;
            line-height: 1.6;
            font-size: 14px;
        }

        /* FOOTER */

        footer {
            background: #0d6efd;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 60px;
        }

        footer p {
            margin: 5px;
        }

        /* RESPONSIVE */

        @media (max-width: 750px) {

            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .hero h1 {
                font-size: 38px;
            }

            .hero h2 {
                font-size: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

    <div class="brand">
        🛒 SEMBAKOKU
    </div>

</div>
<!-- HERO -->


<section class="hero">

    <div class="hero-content">

        <div class="hero-icon">
            🛒
        </div>

        <h1>
            SEMBAKOKU
        </h1>

        <h2>
            Belanja Hemat, Kebutuhan Lengkap.
        </h2>

        <p>
            Selamat datang di Sistem Informasi Penjualan
            SembakoKu. Kelola data produk, pelanggan,
            dan transaksi dengan mudah dan terorganisir.
        </p>

        <div class="hero-buttons">

            <a
                href="penjualan/"
                class="main-btn main-kasir"
            >
                🧾 Buka Kasir
            </a>

            <a
                href="login.php"
                class="main-btn main-login"
            >
                🔐 Login Admin
            </a>

        </div>

    </div>

</section>


<!-- FITUR -->

<section class="section">

    <div class="section-title">

        <h2>
            Fitur SembakoKu
        </h2>

        <p>
            Sistem pengelolaan penjualan toko sembako
        </p>

    </div>


    <div class="cards">

        <div class="card">

            <div class="card-icon">
                👥
            </div>

            <h3>
                Data Pelanggan
            </h3>

            <p>
                Mengelola data pelanggan seperti nama,
                alamat, dan nomor telepon.
            </p>

        </div>


        <div class="card">

            <div class="card-icon">
                📦
            </div>

            <h3>
                Data Produk
            </h3>

            <p>
                Mengelola nama produk, harga,
                dan stok barang.
            </p>

        </div>


        <div class="card">

            <div class="card-icon">
                🧾
            </div>

            <h3>
                Transaksi Penjualan
            </h3>

            <p>
                Melakukan transaksi, pembayaran,
                dan mencetak struk penjualan.
            </p>

        </div>

    </div>

</section>


<!-- FOOTER -->

<footer>

    <p>
        <strong>🛒 SEMBAKOKU</strong>
    </p>

    <p>
        Belanja Hemat, Kebutuhan Lengkap.
    </p>

    <p>
        © 2026 SembakoKu
    </p>

</footer>

</body>

</html>