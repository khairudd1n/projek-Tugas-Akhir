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


// Mendapatkan data pengguna yang sedang login
$user_id = $_SESSION['user_id'];
$queryGetUser = "SELECT nama, no_rumah, no_telepon FROM user WHERE user_id = '$user_id'";
$resultGetUser = mysqli_query($con, $queryGetUser);

if ($resultGetUser) {
    $userData = mysqli_fetch_assoc($resultGetUser);
    $nama = $userData['nama'];
    $no_rumah = $userData['no_rumah'];
    $no_telepon = $userData['no_telepon'];
} else {
    echo "Error: " . mysqli_error($con);
}

// Ambil ID warga dari session
$user_id = $_SESSION['user_id'];

// Ambil data riwayat pembayaran berdasarkan ID warga
$queryRiwayat = "SELECT * FROM pengaduan WHERE user_id = '$user_id'";
$riwayatAduan = mysqli_query($con, $queryRiwayat);

if (!$riwayatAduan) {
    die("Query error: " . mysqli_error($con));
}

// Jika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $nama_pelapor = $_POST['nama_pelapor'];
    $no_rumah = $_POST['no_rumah'];
	$no_telepon = $_POST['no_telepon'];
	$tanggal = $_POST['tanggal'];
	$kategori_masalah = $_POST['kategori_masalah'];
	$deskripsi_masalah = $_POST['deskripsi_masalah'];

    // Mengelola unggahan file
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["bukti_aduan"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    
        // Simpan data ke database
        $query = "INSERT INTO pengaduan (nama_pelapor, user_id, no_rumah, no_telepon, tanggal, kategori_masalah, deskripsi_masalah, status, bukti_aduan) 
        		  VALUES ('$nama_pelapor', '$user_id', '$no_rumah', '$no_telepon', '$tanggal', '$kategori_masalah', '$deskripsi_masalah', 'belum dicek', '$target_file')";

        $result = mysqli_query($con, $query);

        if ($result) { 
        $successMessage = "Selamat! anda berhasil mengirim aduan, silakan cek status
		aduan anda di atas form pengaduan ini";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } 


$tanggal_sekarang = date("Y-m-d");

// Set default status filter
$status_filter = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'semua';

// Buat kueri berdasarkan status filter
if ($status_filter === 'semua') {
    $queryRiwayat = "SELECT * FROM pengaduan WHERE user_id = '$user_id'";
} else {
    $queryRiwayat = "SELECT * FROM pengaduan WHERE user_id = '$user_id' AND status = '$status_filter'";
}

$riwayatAduan = mysqli_query($con, $queryRiwayat);

// slider/pagination script
// Hitung jumlah total baris data
$totalRows = mysqli_num_rows($riwayatAduan);

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



<!DOCTYPE html>
<html lang="en">
<head>

<style>
    .nav__link {
        font-size: 12px; /* Atur ukuran teks menjadi kecil */
    }
    .nav__logo {
        font-size: 12px; /* Atur ukuran teks menjadi kecil */
        margin-left: 10px;
    }
	table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    th {
        background-color: lightblue;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }
		.success-message {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 10px;
        }

        .table-data1 {
        margin-top: 100px; /* Add some margin for better separation */
    }

    .table-data2 {
        margin-top: 20px; /* Add some margin for better separation */
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

        form {
            margin-top: 15px; /* Add some spacing between the form elements */
        }

        label {
            display: block;
            margin-bottom: 8px; /* Add spacing between labels and input fields */
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
</style>

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
			<!-- Tampilkan pesan keberhasilan jika ada -->
            <?php if (!empty($successMessage)) : ?>
                <div class="success-message"><?php echo $successMessage; ?></div>
            <?php endif; ?>
			<div class="order">
				<div class="head">
					<h3>Riwayat aduan anda</h3>
					</div>
					<form method="get">
						<label for="filter_status">Filter berdasarkan status:</label>
						<select name="filter_status" id="filter_status">
							<option value="semua">Semua</option>
							<option value="belum dicek">Belum Dicek</option>
							<option value="sedang diproses">Sedang Diproses</option>
							<option value="selesai">Selesai</option>
						</select>
						<button type="submit">
						<i class='bx bx-filter-alt'></i>	
						Filter</button>

						<button type="button" class="refresh" onclick="refreshPage()">
						<i class='bx bx-sync'></i>
						Refresh</button>
					</form>
					<table>
						<thead>
							<tr>
							<th>Tanggal</th>
							<th>Kategori Masalah</th>
							<th>Deskripsi Masalah</th>
							<th>Bukti aduan</th>
							<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php
						
						while ($row = mysqli_fetch_assoc($riwayatAduan)) {
								echo "<tr>";
								echo "<td>".$row['tanggal']. "</td>";
								echo "<td>".$row['kategori_masalah']. "</td>";
								echo "<td>".$row['deskripsi_masalah']. "</td>";
								echo "<td><a href='" . $row['bukti_aduan'] . "'>Lihat bukti</a></td>";
								echo "<td>".$row['status']. "</td>";
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

			<div class="table-data2">
				<div class="order">
					<div class="head">
						<h3>Form pengaduan</h3>
					</div>
		
						<form method="post" enctype="multipart/form-data">
						<label for="nama">Nama pelapor:</label>
						<input type="text" name="nama_pelapor" placeholder="Nama" value="<?php echo htmlspecialchars($nama); ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
						<label for="bulan">No rumah:</label>
						<input type="text" name="no_rumah" placeholder="no_rumah" value="<?php echo htmlspecialchars($no_rumah); ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
						<label for="bulan">No telepon:</label>
						<input type="text" name="no_telepon" placeholder="no_telepon" value="<?php echo htmlspecialchars($no_telepon); ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
						<label for="tanggal">Tanggal:</label>
                        <input type="text" id="tanggal" name="tanggal" value="<?php echo $tanggal_sekarang; ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
						<label for="kategori masalah">kategori masalah:</label>
					    <select name="kategori_masalah" required>
							<option value="keamanan">keamanan</option>
							<option value="lingkungan">lingkungan</option>
							<option value="infrastruktur">infrastruktur</option>
							<option value="kematian">kematian</option>
							<option value="lainnya">lainnya</option>
					    </select><br>
                        <label for="deskripsi_masalah">Deskripsi Masalah:</label>
                        <textarea id="deskripsi" name="deskripsi_masalah" rows="4" cols="40" required></textarea><br>
						<label for="bukti_aduan">Bukti Aduan:</label>
    					<input type="file" name="bukti_aduan" accept="image/*" required><br>
						<input type="submit" name="submit" value="Kirim pengaduan">     
					</form>
					
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