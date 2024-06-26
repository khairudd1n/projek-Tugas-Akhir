<?php
include('database.php');
session_start();

// Misalkan nama pengguna disimpan dalam session dengan kunci 'username'
if (isset($_SESSION['nama'])) {
    $nama = $_SESSION['nama'];
} else {
    $nama = 'nama'; // Nilai default jika pengguna tidak teridentifikasi
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login_warga.php");
    exit();
}


// Ambil ID warga dari session
$user_id = $_SESSION['user_id'];

// Ambil data riwayat pembayaran berdasarkan ID warga with filter for lunas status
$queryRiwayat = "SELECT * FROM pembayaran_iuran WHERE user_id = '$user_id' AND status = 'lunas'";
$resultRiwayat = mysqli_query($con, $queryRiwayat);

if (!$resultRiwayat) {
  die("Query error: " . mysqli_error($con));
}

// Simpan data pembayaran dalam array
$payments = [];
while ($row = mysqli_fetch_assoc($resultRiwayat)) {
    $payments[$row['bulan']] = $row;
}

// Ambil tahun yang dipilih dari form
$selectedYear = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); // Default to current year if not set

// Query untuk mengambil data pembayaran_iuran berdasarkan tahun yang dipilih
$queryPayments = "SELECT bulan, tahun, bayaran_wajib, bayaran_sukarela, status FROM pembayaran_iuran WHERE tahun = '$selectedYear' AND status = 'lunas' AND user_id = '$user_id'";
$resultPayments = mysqli_query($con, $queryPayments);

$payments = [];
while ($rowPayment = mysqli_fetch_assoc($resultPayments)) {
    $month = $rowPayment['bulan'];
    $payments[$month] = $rowPayment;
}


// Daftar bulan dalam bahasa Indonesia
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Filter data for "Kartu Iuran Anda" table based on status "lunas"
$filteredPayments = [];
foreach ($payments as $month => $payment) {
  if ($payment['status'] === 'lunas') {
    $filteredPayments[$month] = $payment;
  }
}

// Ambil data riwayat pembayaran berdasarkan ID warga dengan memperhitungkan filter
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterTahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$queryRiwayat = "SELECT * FROM pembayaran_iuran WHERE user_id = '$user_id'";
if ($filterStatus !== '') {
    $queryRiwayat .= " AND status = '$filterStatus'";
}
if ($filterTahun !== '') {
    $queryRiwayat .= " AND tahun = '$filterTahun'";
}
$resultRiwayat = mysqli_query($con, $queryRiwayat);

// Get the selected year from the form
$selectedTahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Filter the query based on the selected year
if ($selectedTahun !== '') {
  $queryRiwayat .= " AND tahun = '$selectedTahun'";
}

// slider/pagination script
// Hitung jumlah total baris data
$totalRows = mysqli_num_rows($resultRiwayat);

// Tentukan jumlah baris data per halaman
$rowsPerPage = 5;

// Hitung jumlah total halaman
$totalPages = ceil($totalRows / $rowsPerPage);

// Tentukan halaman saat ini
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Hitung offset (posisi awal data pada halaman saat ini)
$offset = ($currentPage - 1) * $rowsPerPage;

// Ambil data riwayat pembayaran berdasarkan halaman saat ini
$queryRiwayat .= " LIMIT $offset, $rowsPerPage";
$resultRiwayat = mysqli_query($con, $queryRiwayat);
?>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    .table-data1 {
        margin-top: 100px; /* Add some margin for better separation */
    }

    .table-data2 {
        margin-top: 20px; /* Add some margin for better separation */
    }
    th, td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        text-align: center;
        background-color: lightblue;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }
    /* Style untuk filter */
    .filter-container {
        margin-bottom: 20px;
    }

    .filter-container label {
        margin-right: 10px;
        font-weight: bold;
    }

    .filter-container select {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    /* CSS untuk dropdown */
    select {
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
        margin-right: 10px;
    }

    /* CSS untuk tombol filter */
    button {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    /* CSS untuk hover pada tombol filter */
    button:hover {
        background-color: #45a049;
    }
    /* CSS untuk tombol refresh */
    button.refresh {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        background-color: #ff7f0f; /* Warna oranye */
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    /* CSS untuk hover pada tombol refresh */
    button.refresh:hover {
        background-color: #ff990f; 
    }
    .pagination {
        margin-top: 20px; 
        text-align: right; 
    }

    .pagination a {
        display: inline-block;
        padding: 5px 10px;
        margin: 0 2px;
        border: 1px solid #ccc;
        text-decoration: none;
        color: #333;
    }

    .pagination a.active {
        background-color: lightseagreen;
        color: white;
    }

    .pagination a:hover {
        background-color: #ddd;
    }
    .month-column {
        text-align: center;
        background-color: #87CEEB; /* Light blue color */
    }
    .red-icon {
        color: red;
    }
    .green-icon {
        color: green;
    }
    .order {
        background-color: #f9f9f9; /* Add a light background color to the form */
        padding: 20px;
        border-radius: 10px; /* Add rounded corners for a modern look */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow effect */
        width: 70%; /* Adjust the width as needed */
        margin: 0 auto; /* Center the form on the page */
        overflow-x: auto; /* Enable horizontal scrolling */
    }
    /* Media queries for responsive design */
    @media (max-width: 768px) {
        .order {
            width: 90%; /* Reduce the width for smaller screens */
        }

        th, td {
            padding: 8px; /* Reduce padding for smaller screens */
            font-size: 14px; /* Adjust font size for smaller screens */
        }

        button {
            padding: 6px 12px; /* Adjust padding for buttons */
            font-size: 14px; /* Adjust font size for buttons */
        }
    }
    .nav__link {
        font-size: 12px; /* Atur ukuran teks menjadi kecil */
    }
    .nav__logo {
        font-size: 12px; /* Atur ukuran teks menjadi kecil */
        margin-left: 10px;
    }
</style>



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
			

			


<div class="table-data1">
    <div class="order">
        <h2 style="text-align: center;">Kartu Iuran Anda</h2>
        <div class="head">
            <form action="" method="GET">
                <label for="tahun">Tahun/Periode:</label>
                <select name="tahun" id="tahun">
                    <?php
                    // Query untuk mengambil tahun distinct dari tabel pembayaran_iuran
                    $queryTahun = "SELECT DISTINCT tahun FROM pembayaran_iuran ORDER BY tahun DESC";
                    $resultTahun = mysqli_query($con, $queryTahun);

                    // Tambahkan opsi tahun ke dropdown
                    while ($rowTahun = mysqli_fetch_assoc($resultTahun)) {
                        $tahun = $rowTahun['tahun'];
                        $selected = $tahun == $selectedYear ? 'selected' : '';
                        echo "<option value='$tahun' $selected>$tahun</option>";
                    }
                    ?>
                </select>
                <button type="submit">
                    <i class='bx bx-filter-alt'></i>
                    Filter
                </button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Wajib (Rp)</th>
                    <th>Sukarela (Rp)</th>
                    <th>Total (Rp)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($months as $month) {
                    echo "<tr>";
                    echo "<td class='month-column'>$month</td>";

                    if (isset($payments[$month])) {
                        $bayaranWajib = $payments[$month]['bayaran_wajib'];
                        $bayaranSukarela = $payments[$month]['bayaran_sukarela'];
                        $total = $bayaranWajib + $bayaranSukarela;

                        echo "<td>$bayaranWajib</td>";
                        echo "<td>$bayaranSukarela</td>";
                        echo "<td>$total</td>";
                        echo "<td><i class='bx bx-check green-icon'>Sudah bayar</i></td>";
                    } else {
                        // Tampilkan data kosong untuk bulan yang belum lunas
                        echo "<td>-</td>";
                        echo "<td>-</td>";
                        echo "<td>-</td>";
                        echo "<td><i class='bx bx-x red-icon'>Belum bayar</i></td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>


		<div class="table-data2">
				<div class="order">
				<h2 style="text-align: center;">Detail Bayaran Iuran Anda</h2><br>
					<div class="head">
					<form action="" method="GET">
						<label for="status">Status:</label>
						<select name="status" id="status">
							<option value="">Semua</option>
							<option value="lunas">Lunas</option>
							<option value="ditolak">Ditolak</option>
							<option value="diproses">Diproses</option>
						</select>
						<label for="tahun">Tahun:</label>
						<select name="tahun" id="tahun">
						<option value="">Semua</option>
						<?php
						// Query untuk mengambil tahun-tahun yang ada dari tabel pembayaran_iuran
						$queryTahun = "SELECT DISTINCT tahun FROM pembayaran_iuran ORDER BY tahun DESC";
						$resultTahun = mysqli_query($con, $queryTahun);
						
						// Tambahkan pilihan tahun ke dropdown
						while ($rowTahun = mysqli_fetch_assoc($resultTahun)) {
							$tahun = $rowTahun['tahun'];
							echo "<option value='$tahun'>$tahun</option>";
						}
						?>
						</select>
						<button type="submit">
						<i class='bx bx-filter-alt'></i>	
						Filter</button>

						<button type="button" class="refresh" onclick="refreshPage()">
						<i class='bx bx-sync'></i>
						Refresh</button>
					</form>

					</div>
					<table>
						<thead>
							<tr>
								<th>Id iuran</th>
								<th>Tanggal bayar</th>
								<th>Iuran bulan</th>
								<th>Iuran tahun/periode</th>
                                <th>Bayaran wajib</th>
                                <th>Bayaran sukarela</th>
                                <th>Bukti bayar</th>
                                <th>Status</th>
								<th>Komentar</th>
							</tr>
						</thead>
						<tbody>
						<?php
                    while ($row = mysqli_fetch_assoc($resultRiwayat)) {
                        echo "<tr>";
						echo "<td>" . $row['pembayaran_iuran_id'] . "</td>";
                        echo "<td>" . $row['tanggal'] . "</td>";
                        echo "<td>" . $row['bulan'] . "</td>";
                        echo "<td>" . $row['tahun'] . "</td>";
                        echo "<td>" . $row['bayaran_wajib'] . "</td>";
                        echo "<td>" . $row['bayaran_sukarela'] . "</td>";
                        echo "<td><a href='" . $row['bukti_bayar'] . "'>Lihat</a></td>";
						echo "<td>" . $row['status'] . "</td>";
						echo "<td>" . $row['komentar'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
						</tbody>
						
					</table>
					<!-- Tampilkan link navigasi halaman -->
				<div class="pagination">
					<?php if ($currentPage > 1) : ?>
						<a href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
					<?php endif; ?>

					<?php for ($i = 1; $i <= $totalPages; $i++) : ?>
						<a href="?page=<?php echo $i; ?>" <?php if ($currentPage == $i) echo 'class="active"'; ?>><?php echo $i; ?></a>
					<?php endfor; ?>

					<?php if ($currentPage < $totalPages) : ?>
						<a href="?page=<?php echo $currentPage + 1; ?>">Next</a>
					<?php endif; ?>
				</div>	
				</div>

			</div>


		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	
	<script>
    function refreshPage() {
        window.location.href = window.location.pathname;
    }
</script>

<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>