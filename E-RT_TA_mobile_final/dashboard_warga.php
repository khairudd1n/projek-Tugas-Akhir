<?php
session_start();
include('database.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: login_warga.php");
    exit();
}

// Misalkan nama pengguna disimpan dalam session dengan kunci 'username'
if (isset($_SESSION['nama'])) {
    $nama = $_SESSION['nama'];
} else {
    $nama = 'tamu'; // Nilai default jika pengguna tidak teridentifikasi
}

// Ambil data keuangan dari database
$sql = "SELECT keuangan_id, jenis, jumlah, tanggal, deskripsi FROM keuangan";
$result = $con->query($sql);

$totalPemasukan = 0;
$totalPengeluaran = 0;

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		
		if ($row["jenis"] == "pemasukan") {
			$totalPemasukan += (int)$row["jumlah"];
		} else {
			$totalPengeluaran += (int)$row["jumlah"];
		}
	}
} else {
	echo "0 results";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="styles.css">

	<title>E-RT TAMPAK SIRING</title>
</head>
<style>
	.box-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    list-style-type: none;
    padding: 0;
    margin: 0;
	margin-top: 20px;
}

.box-item {
    flex: 1 1 30%;
    max-width: 35%;
    box-sizing: border-box;
    background-color: white;
    border: 1px solid #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin: 10px;
    padding: 20px; /* Added padding to ensure the icon fits within the box */
}

.box-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 100%;
    height: 100%;
    padding: 20px;
    background-color: white;
    border: 1px solid #ccc;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.3s ease;
}

.box-content:hover {
    background-color: #f0f0f0;
}

.box-content i {
    font-size: 48px; /* Adjust size as needed */
    margin-bottom: 10px;
}

.box-content h1 {
    font-size: 16px; /* Adjust size as needed */
    color: #333;
    text-align: center;
}

@media (max-width: 768px) {
    .box-item {
        flex: 1 1 45%;
        max-width: 35%;
    }

    .box-content i {
        font-size: 36px; /* Adjust size as needed */
    }

    .box-content h1 {
        font-size: 14px; /* Adjust size as needed */
    }
}

@media (max-width: 480px) {
    .box-item {
        flex: 1 1 100%;
        max-width: 100%;
    }

    .box-content i {
        font-size: 32px; /* Adjust size as needed */
    }

    .box-content h1 {
        font-size: 12px; /* Adjust size as needed */
    }
}
.judul {
	margin-top: 5px;
	text-align: center;
}
.gambar {
	display: flex;
    justify-content: center; /* Horizontally center content */
    align-items: center; /* Vertically center content */
	margin-top: 85px;
}
.nav__link {
        font-size: 12px; /* Atur ukuran teks menjadi kecil */
    }
    .nav__logo {
        font-size: 12px; /* Atur ukuran teks menjadi kecil */
        margin-left: 10px;
    }
</style>
<body>


	<!--===== HEADER =====-->
    <header class="l-header">
            <nav class="nav bd-grid">
            <img src="gambar/logoviladago.png" alt="" width="100px">
                <div>
                <i class='bx bxs-user'></i>
                    <a href="kelola_profil.php" class="nav__logo">Hallo, Bpk/Ibu <?php echo htmlspecialchars($nama); ?></a>
                </div>

                <div class="nav__menu" id="nav-menu">
                    <ul class="nav__list">
                        <li class="nav__item"><a href="dashboard_warga.php" class="nav__link"><i class='bx bxs-home'></i> Home</a></li>
                        <li class="nav__item"><a href="bayar_iuran.php" class="nav__link"><i class='bx bx-money'></i> Bayar iuran</a></li>
                        <li class="nav__item"><a href="riwayat_pembayaran.php" class="nav__link"><i class='bx bxs-book-content'></i> Riwayat iuran</a></li>
                        <li class="nav__item"><a href="pengaduan_warga.php" class="nav__link"><i class='bx bx-edit-alt'></i> Pengaduan</a></li>
                        <li class="nav__item"><a href="keuangan_warga.php" class="nav__link"><i class='bx bx-coin-stack'></i> Laporan keuangan</a></li>
                        <li class="nav__item"><a href="anggota_keluarga.php" class="nav__link"><i class='bx bxs-group'></i> Anggota keluarga</a></li>
                        <li class="nav__item"><a href="logout_warga.php" class="nav__link">Logout</a></li>
                    </ul>
                </div>

                <div class="nav__toggle" id="nav-toggle">
                    <i class='bx bx-menu'></i>
                </div>
            </nav>
        </header>



	<!-- CONTENT -->
	<section id="content">
		<!-- MAIN -->
		<main>
		<div class="gambar">
		<img src="gambar/logoviladago.png" alt="" width="100px">
		</div>
<h1 class="judul">SISTEM PELAYANAN WARGA - CLUSTER TAMPAK SIRING</h1>
<ul class="box-info">
    <li class="box-item">
        <a href="bayar_iuran.php" class="box-content">
            <i class='bx bx-money'></i>
            <h1>BAYAR IURAN</h1>
        </a>
    </li>
    <li class="box-item">
        <a href="pengaduan_warga.php" class="box-content">
            <i class='bx bx-edit-alt'></i>
            <h1>BUAT PENGADUAN</h1>
        </a>
    </li>
    <li class="box-item">
        <a href="keuangan_warga.php" class="box-content">
		<i class='bx bx-coin-stack'></i>
            <h1>LAPORAN KEUANGAN</h1>
        </a>
    </li>
    <li class="box-item">
        <a href="anggota_keluarga.php" class="box-content">
            <i class='bx bxs-group'></i>
            <h1>ANGGOTA KELUARGA</h1>
        </a>
    </li>
    <li class="box-item">
        <a href="kelola_profil.php" class="box-content">
            <i class='bx bxs-user-circle'></i>
            <h1>PROFIL ANDA</h1>
        </a>
    </li>
    <li class="box-item">
        <a href="riwayat_pembayaran.php" class="box-content">
            <i class='bx bxs-book-content'></i>
            <h1>RIWAYAT IURAN</h1>
        </a>
    </li>
</ul>



			
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>