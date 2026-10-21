
<?php
include "../koneksi.php";

$pesan_error = "";

if (isset($_POST['tambah_pelanggan'])) {
    $nama = trim($_POST['nama_pelanggan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['no_telepon'] ?? '');

    if ($nama !== '') {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO pelanggan (nama_pelanggan, alamat, no_telepon)
             VALUES (?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $nama, $alamat, $telepon);

            if (mysqli_stmt_execute($stmt)) {
                $id_baru = mysqli_insert_id($koneksi);
                mysqli_stmt_close($stmt);

                header("Location: index.php?pelanggan_baru=" . $id_baru);
                exit;
            }

            $pesan_error = "Gagal menambahkan pelanggan.";
            mysqli_stmt_close($stmt);
        } else {
            $pesan_error = "Gagal menyiapkan data pelanggan.";
        }
    } else {
        $pesan_error = "Nama pelanggan wajib diisi.";
    }
}

$pelanggan = mysqli_query(
    $koneksi,
    "SELECT id_pelanggan, nama_pelanggan, no_telepon
     FROM pelanggan
     ORDER BY nama_pelanggan ASC"
);

$produk = mysqli_query(
    $koneksi,
    "SELECT id_produk, nama_produk, harga, stok
     FROM produk
     WHERE stok > 0
     ORDER BY nama_produk ASC"
);

if (!$pelanggan || !$produk) {
    die("Gagal mengambil data dari database.");
}

$dataPelanggan = [];
while ($p = mysqli_fetch_assoc($pelanggan)) {
    $dataPelanggan[] = [
        'id' => (int)$p['id_pelanggan'],
        'nama' => $p['nama_pelanggan'],
        'telepon' => $p['no_telepon'] ?? ''
    ];
}

$idPelangganBaru = isset($_GET['pelanggan_baru'])
    ? (int)$_GET['pelanggan_baru']
    : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kasir - SembakoKu</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
    color: #333;
}

.navbar {
    background: #f5b43c;
    min-height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 12px 7%;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
}

.brand {
    color: white;
    font-size: 25px;
    font-weight: bold;
}

.btn-beranda {
    background: white;
    color: #333;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 9px;
    font-weight: bold;
}

.container {
    width: 88%;
    max-width: 1200px;
    margin: 38px auto;
}

.title {
    margin-bottom: 25px;
}

.title h1 {
    margin: 0 0 7px;
    font-size: 31px;
}

.title p {
    margin: 0;
    color: #777;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 28px;
    margin-bottom: 22px;
    box-shadow: 0 5px 18px rgba(0,0,0,.07);
}

.card-title {
    font-size: 21px;
    font-weight: bold;
    margin-bottom: 18px;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 11px 17px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary,
.btn-tambah {
    background: #f5b43c;
    color: white;
}

.btn-primary:hover,
.btn-tambah:hover {
    background: #e6a52f;
}

.btn-secondary {
    background: #eee;
    color: #333;
}

.search-box {
    width: 100%;
    height: 46px;
    border: 1px solid #d6d6d6;
    border-radius: 9px;
    padding: 0 14px;
    font-size: 15px;
    outline: none;
}

.search-box:focus {
    border-color: #f5b43c;
    box-shadow: 0 0 0 3px rgba(245,180,60,.15);
}

.customer-search {
    position: relative;
}

.customer-search-info {
    color: #777;
    font-size: 13px;
    margin-top: 8px;
}

.hasil-pelanggan {
    display: none;
    margin-top: 8px;
    border: 1px solid #e3e3e3;
    border-radius: 10px;
    background: white;
    max-height: 260px;
    overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,.08);
}

.item-pelanggan {
    width: 100%;
    display: block;
    text-align: left;
    padding: 13px 15px;
    border: none;
    border-bottom: 1px solid #eee;
    background: white;
    cursor: pointer;
    color: #333;
}

.item-pelanggan:last-child {
    border-bottom: none;
}

.item-pelanggan:hover {
    background: #fff7e7;
}

.nama-hasil {
    font-weight: bold;
    font-size: 15px;
    display: block;
    margin-bottom: 5px;
}

.telepon-hasil {
    color: #777;
    font-size: 13px;
    display: block;
}

.pesan-pencarian {
    padding: 15px;
    text-align: center;
    color: #777;
    font-size: 14px;
}

.pelanggan-terpilih {
    display: none;
    margin-top: 14px;
    padding: 15px;
    border: 1px solid #cce7d5;
    background: #f1faf4;
    border-radius: 10px;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.pelanggan-terpilih strong {
    display: block;
    margin-bottom: 5px;
}

.pelanggan-terpilih small {
    color: #666;
}

.btn-ganti {
    border: none;
    border-radius: 7px;
    padding: 9px 12px;
    background: #e4eee7;
    cursor: pointer;
    font-weight: bold;
    color: #285c3a;
}

.search-wrapper {
    margin-bottom: 17px;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #f5f6f8;
    padding: 14px 12px;
    text-align: left;
    font-size: 14px;
    white-space: nowrap;
}

td {
    padding: 14px 12px;
    border-bottom: 1px solid #eee;
}

.harga {
    font-weight: bold;
    white-space: nowrap;
}

.stok {
    color: #218c54;
    font-weight: bold;
}

.btn-tambah {
    border: none;
    padding: 8px 15px;
    border-radius: 7px;
    cursor: pointer;
    font-weight: bold;
    white-space: nowrap;
}

.jumlah-produk,
.cart-table input {
    width: 65px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 7px;
}

.empty-cart {
    text-align: center;
    color: #888;
    padding: 25px;
    background: #fafafa;
    border-radius: 9px;
}

.btn-hapus {
    border: none;
    background: #e74c3c;
    color: white;
    padding: 7px 10px;
    border-radius: 6px;
    cursor: pointer;
}

.total-box {
    margin-top: 20px;
    padding-top: 18px;
    border-top: 2px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-label {
    font-size: 20px;
    font-weight: bold;
}

.total-harga {
    font-size: 25px;
    font-weight: bold;
    color: #e49e18;
}

.aksi {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    inset: 0;
    background: rgba(0,0,0,.45);
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.modal-box {
    background: white;
    width: 100%;
    max-width: 500px;
    border-radius: 14px;
    padding: 27px;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
}

.modal-title {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-group input {
    width: 100%;
    height: 42px;
    border: 1px solid #ccc;
    border-radius: 7px;
    padding: 0 12px;
    font-size: 14px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
}

.alert-error {
    padding: 12px 15px;
    margin-bottom: 18px;
    color: #a52834;
    background: #f8d7da;
    border-radius: 8px;
}

.pesan-kosong {
    display: none;
    padding: 15px;
    text-align: center;
    color: #888;
    background: #fafafa;
    border-radius: 8px;
}

@media (max-width: 700px) {
    .navbar {
        padding: 12px 4%;
    }

    .brand {
        font-size: 19px;
    }

    .btn-beranda {
        padding: 9px 12px;
    }

    .container {
        width: 94%;
        margin: 24px auto;
    }

    .card {
        padding: 18px;
    }

    .pelanggan-terpilih {
        align-items: flex-start;
        flex-direction: column;
    }

    .total-box {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .title h1 {
        font-size: 27px;
    }
}
</style>
</head>

<body>

<div class="navbar">
    <div class="brand">🛒 SEMBAKOKU - KASIR</div>
    <a href="../index.php" class="btn-beranda">← Beranda</a>
</div>

<div class="container">

    <div class="title">
        <h1>Kasir</h1>
        <p>Kelola pelanggan, pilih produk, dan buat transaksi dengan mudah.</p>
    </div>

    <?php if ($pesan_error !== '') : ?>
        <div class="alert-error">
            <?= htmlspecialchars($pesan_error); ?>
        </div>
    <?php endif; ?>

    <!-- PELANGGAN -->
    <div class="card">
        <div class="card-title">👤 Pelanggan</div>

        <div class="customer-search">
            <input
                type="text"
                id="searchPelanggan"
                class="search-box"
                placeholder="🔍 Ketik nama atau nomor telepon pelanggan..."
                autocomplete="off"
            >

            <div class="customer-search-info">
                Ketik beberapa huruf atau angka, lalu klik pelanggan yang muncul.
            </div>

            <div id="hasilPelanggan" class="hasil-pelanggan"></div>
        </div>

        <div id="pelangganTerpilih" class="pelanggan-terpilih">
            <div>
                <strong id="namaPelangganTerpilih"></strong>
                <small id="teleponPelangganTerpilih"></small>
            </div>
            <button type="button" class="btn-ganti" onclick="gantiPelanggan()">
                Ganti
            </button>
        </div>

        <input type="hidden" id="id_pelanggan" value="">
        <div id="pelangganKosong" class="pesan-kosong">
            Pelanggan tidak ditemukan.
        </div>

        <div style="margin-top:15px;">
            <button type="button" class="btn btn-primary" onclick="bukaModal()">
                + Pelanggan Baru
            </button>
        </div>
    </div>

    <!-- PRODUK -->
    <div class="card">
        <div class="card-title">🛍️ Pilih Produk</div>

        <div class="search-wrapper">
            <input
                type="text"
                id="searchProduk"
                class="search-box"
                placeholder="🔍 Cari nama produk..."
            >
        </div>

        <div class="table-wrapper">
            <table id="produkTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                while ($p = mysqli_fetch_assoc($produk)) :
                    $id = (int)$p['id_produk'];
                    $nama = $p['nama_produk'];
                    $harga = (float)$p['harga'];
                    $stok = (int)$p['stok'];
                ?>
                    <tr class="produk-row">
                        <td><?= $no++; ?></td>
                        <td class="nama-produk"><?= htmlspecialchars($nama); ?></td>
                        <td class="harga">Rp <?= number_format($harga, 0, ',', '.'); ?></td>
                        <td class="stok"><?= $stok; ?></td>
                        <td>
                            <input
                                type="number"
                                class="jumlah-produk"
                                id="jumlah_<?= $id; ?>"
                                value="1"
                                min="1"
                                max="<?= $stok; ?>"
                            >
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn-tambah"
                                onclick="tambahKeranjang(
                                    <?= $id; ?>,
                                    <?= htmlspecialchars(
                                        json_encode(
                                            $nama,
                                            JSON_HEX_TAG | JSON_HEX_APOS |
                                            JSON_HEX_QUOT | JSON_HEX_AMP
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>,
                                    <?= $harga; ?>,
                                    <?= $stok; ?>
                                )"
                            >+ Tambah</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div id="produkKosong" class="pesan-kosong">
            Produk tidak ditemukan.
        </div>
    </div>

    <!-- KERANJANG DAN TRANSAKSI -->
    <form method="POST" action="proses.php" id="formTransaksi">
        <input type="hidden" name="id_pelanggan" id="idPelangganHidden">
        <div id="hiddenProduk"></div>

        <div class="card">
            <div class="card-title">🛒 Keranjang Belanja</div>

            <div id="keranjang">
                <div class="empty-cart">Belum ada produk yang dipilih.</div>
            </div>

            <div class="total-box">
                <div class="total-label">Total Belanja</div>
                <div class="total-harga" id="totalHarga">Rp 0</div>
            </div>

            <div class="aksi">
                <button type="button" class="btn btn-secondary" onclick="resetKeranjang()">
                    Reset
                </button>
                <button type="submit" class="btn btn-primary" onclick="return validasiTransaksi()">
                    Lanjut ke Pembayaran →
                </button>
            </div>
        </div>
    </form>
</div>

<!-- MODAL TAMBAH PELANGGAN -->
<div class="modal" id="modalPelanggan">
    <div class="modal-box">
        <div class="modal-title">Tambah Pelanggan Baru</div>

        <form method="POST">
            <div class="form-group">
                <label for="namaPelanggan">Nama Pelanggan</label>
                <input
                    type="text"
                    id="namaPelanggan"
                    name="nama_pelanggan"
                    required
                    placeholder="Masukkan nama pelanggan"
                >
            </div>

            <div class="form-group">
                <label for="teleponPelanggan">No. Telepon</label>
                <input
                    type="text"
                    id="teleponPelanggan"
                    name="no_telepon"
                    placeholder="Masukkan nomor telepon"
                >
            </div>

            <div class="form-group">
                <label for="alamatPelanggan">Alamat</label>
                <input
                    type="text"
                    id="alamatPelanggan"
                    name="alamat"
                    placeholder="Masukkan alamat"
                >
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="tutupModal()">
                    Batal
                </button>
                <button type="submit" name="tambah_pelanggan" class="btn btn-primary">
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const daftarPelanggan = <?= json_encode(
    $dataPelanggan,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
); ?>;

const pelangganBaru = <?= $idPelangganBaru; ?>;

let keranjang = {};

const inputCariPelanggan = document.getElementById("searchPelanggan");
const hasilPelanggan = document.getElementById("hasilPelanggan");
const idPelanggan = document.getElementById("id_pelanggan");
const kartuPelanggan = document.getElementById("pelangganTerpilih");
const pesanPelangganKosong = document.getElementById("pelangganKosong");

/* PENCARIAN PELANGGAN LANGSUNG */
function cariPelanggan() {
    const keyword = inputCariPelanggan.value.trim().toLowerCase();

    hasilPelanggan.innerHTML = "";
    pesanPelangganKosong.style.display = "none";

    if (keyword === "") {
        hasilPelanggan.style.display = "none";
        return;
    }

    const hasil = daftarPelanggan.filter(function(p) {
        const nama = String(p.nama || "").toLowerCase();
        const telepon = String(p.telepon || "").toLowerCase();

        return nama.includes(keyword) || telepon.includes(keyword);
    });

    if (hasil.length === 0) {
        hasilPelanggan.style.display = "none";
        pesanPelangganKosong.style.display = "block";
        return;
    }

    hasil.forEach(function(p) {
        const tombol = document.createElement("button");
        tombol.type = "button";
        tombol.className = "item-pelanggan";

        const nama = document.createElement("span");
        nama.className = "nama-hasil";
        nama.textContent = p.nama;

        const telepon = document.createElement("span");
        telepon.className = "telepon-hasil";
        telepon.textContent = p.telepon || "Nomor telepon belum diisi";

        tombol.appendChild(nama);
        tombol.appendChild(telepon);

        tombol.addEventListener("click", function() {
            pilihPelanggan(p);
        });

        hasilPelanggan.appendChild(tombol);
    });

    hasilPelanggan.style.display = "block";
}

inputCariPelanggan.addEventListener("input", cariPelanggan);

/* PILIH PELANGGAN DARI HASIL PENCARIAN */
function pilihPelanggan(p) {
    idPelanggan.value = p.id;

    document.getElementById("namaPelangganTerpilih").textContent = p.nama;
    document.getElementById("teleponPelangganTerpilih").textContent =
        p.telepon || "Nomor telepon belum diisi";

    kartuPelanggan.style.display = "flex";
    inputCariPelanggan.value = "";
    hasilPelanggan.innerHTML = "";
    hasilPelanggan.style.display = "none";
    pesanPelangganKosong.style.display = "none";
}

/* GANTI PELANGGAN */
function gantiPelanggan() {
    idPelanggan.value = "";
    kartuPelanggan.style.display = "none";
    inputCariPelanggan.value = "";
    inputCariPelanggan.focus();
    hasilPelanggan.innerHTML = "";
    hasilPelanggan.style.display = "none";
}

/* TUTUP HASIL SAAT KLIK DI LUAR PENCARIAN */
document.addEventListener("click", function(event) {
    const area = document.querySelector(".customer-search");

    if (!area.contains(event.target)) {
        hasilPelanggan.style.display = "none";
    }
});

/* CARI PRODUK */
function cariProduk() {
    const keyword = document.getElementById("searchProduk").value.trim().toLowerCase();
    const rows = document.querySelectorAll(".produk-row");
    const pesan = document.getElementById("produkKosong");
    let jumlahCocok = 0;

    rows.forEach(function(row) {
        const nama = row.querySelector(".nama-produk").innerText.toLowerCase();
        const cocok = nama.includes(keyword);

        row.style.display = cocok ? "" : "none";

        if (cocok) jumlahCocok++;
    });

    pesan.style.display =
        rows.length > 0 && jumlahCocok === 0 ? "block" : "none";
}

document.getElementById("searchProduk").addEventListener("input", cariProduk);

/* FORMAT RUPIAH */
function rupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(angka);
}

function escapeHTML(teks) {
    return String(teks).replace(/[&<>"']/g, function(karakter) {
        return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[karakter];
    });
}

/* TAMBAH KE KERANJANG */
function tambahKeranjang(id, nama, harga, stok) {
    const input = document.getElementById("jumlah_" + id);
    let jumlah = parseInt(input.value, 10);

    if (isNaN(jumlah) || jumlah < 1) {
        alert("Jumlah produk minimal 1.");
        input.value = 1;
        return;
    }

    if (jumlah > stok) {
        alert("Jumlah melebihi stok yang tersedia.");
        return;
    }

    if (keranjang[id]) {
        const jumlahBaru = keranjang[id].jumlah + jumlah;

        if (jumlahBaru > stok) {
            alert("Jumlah produk melebihi stok yang tersedia.");
            return;
        }

        keranjang[id].jumlah = jumlahBaru;
    } else {
        keranjang[id] = {
            id: id,
            nama: nama,
            harga: harga,
            stok: stok,
            jumlah: jumlah
        };
    }

    tampilkanKeranjang();
}

/* TAMPILKAN KERANJANG */
function tampilkanKeranjang() {
    const container = document.getElementById("keranjang");
    const ids = Object.keys(keranjang);

    if (ids.length === 0) {
        container.innerHTML =
            '<div class="empty-cart">Belum ada produk yang dipilih.</div>';

        document.getElementById("totalHarga").innerText = "Rp 0";
        updateHiddenProduk();
        return;
    }

    let html = `
        <div class="table-wrapper">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>`;

    let total = 0;

    ids.forEach(function(id, index) {
        const item = keranjang[id];
        const subtotal = item.harga * item.jumlah;
        total += subtotal;

        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHTML(item.nama)}</td>
                <td>${rupiah(item.harga)}</td>
                <td>
                    <input
                        type="number"
                        min="1"
                        max="${item.stok}"
                        value="${item.jumlah}"
                        onchange="ubahJumlah(${item.id}, this.value)"
                    >
                </td>
                <td><strong>${rupiah(subtotal)}</strong></td>
                <td>
                    <button type="button" class="btn-hapus"
                        onclick="hapusKeranjang(${item.id})">
                        Hapus
                    </button>
                </td>
            </tr>`;
    });

    html += "</tbody></table></div>";

    container.innerHTML = html;
    document.getElementById("totalHarga").innerText = rupiah(total);

    updateHiddenProduk();
}

function ubahJumlah(id, jumlah) {
    jumlah = parseInt(jumlah, 10);

    if (isNaN(jumlah) || jumlah < 1) {
        jumlah = 1;
    }

    if (jumlah > keranjang[id].stok) {
        alert("Jumlah melebihi stok yang tersedia.");
        jumlah = keranjang[id].stok;
    }

    keranjang[id].jumlah = jumlah;
    tampilkanKeranjang();
}

function hapusKeranjang(id) {
    delete keranjang[id];
    tampilkanKeranjang();
}

function updateHiddenProduk() {
    const container = document.getElementById("hiddenProduk");
    container.innerHTML = "";

    Object.keys(keranjang).forEach(function(id) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "jumlah[" + id + "]";
        input.value = keranjang[id].jumlah;
        container.appendChild(input);
    });
}

/* VALIDASI SEBELUM PEMBAYARAN */
function validasiTransaksi() {
    if (idPelanggan.value === "") {
        alert("Silakan cari dan pilih pelanggan terlebih dahulu.");
        inputCariPelanggan.focus();
        return false;
    }

    if (Object.keys(keranjang).length === 0) {
        alert("Silakan pilih minimal satu produk.");
        return false;
    }

    document.getElementById("idPelangganHidden").value = idPelanggan.value;
    return true;
}

/* MODAL TAMBAH PELANGGAN */
function bukaModal() {
    document.getElementById("modalPelanggan").style.display = "flex";
    document.getElementById("namaPelanggan").focus();
}

function tutupModal() {
    document.getElementById("modalPelanggan").style.display = "none";
}

window.addEventListener("click", function(event) {
    const modal = document.getElementById("modalPelanggan");

    if (event.target === modal) {
        tutupModal();
    }
});

window.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        tutupModal();
        hasilPelanggan.style.display = "none";
    }
});

/* RESET KERANJANG */
function resetKeranjang() {
    if (Object.keys(keranjang).length === 0) return;

    if (confirm("Yakin ingin mengosongkan keranjang?")) {
        keranjang = {};
        tampilkanKeranjang();
    }
}

/* OTOMATIS PILIH PELANGGAN YANG BARU DITAMBAHKAN */
if (pelangganBaru > 0) {
    const pelangganDitemukan = daftarPelanggan.find(function(p) {
        return p.id === pelangganBaru;
    });

    if (pelangganDitemukan) {
        pilihPelanggan(pelangganDitemukan);
    }
}
</script>

</body>
</html>
