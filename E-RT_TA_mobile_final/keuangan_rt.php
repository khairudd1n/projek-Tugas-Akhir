<?php

session_start();

include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login_rt.php");
    exit();
}

// Ambil filter dari form
$status = isset($_GET['status']) ? $_GET['status'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Buat query SQL dengan filter
$sql = "SELECT keuangan_id, tanggal, deskripsi, jenis, jumlah, bukti FROM keuangan WHERE 1=1";

if (!empty($status)) {
    $sql .= " AND jenis = '$status'";
}

if (!empty($bulan)) {
    $sql .= " AND MONTH(tanggal) = '$bulan'";
}

if (!empty($tahun)) {
    $sql .= " AND YEAR(tanggal) = '$tahun'";
}

$result = $con->query($sql);

// Inisialisasi variabel penjumlahan
$totalPemasukan = 0;
$totalPengeluaran = 0;

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="styles.css">

	<title>E-RT TAMPAK SIRING (KETUA RT)</title>
</head>
<body>

<style>
table {
        width: 100%;
        border-collapse: collapse;
    }
    .table-data1 {
            margin-top: 100px; /* Add some margin for better separation */
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
    th, td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    th {
        background-color: lightblue;
    }

    tr:hover {
        background-color: #ddd;
    }

#tambah {
    padding: 8px 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

#tambah:hover {
    background-color: #0056b3;
}
        #popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    z-index: 9999;
    width: 300px; /* Lebar popup */
    max-width: 80%; /* Lebar maksimum */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); /* Efek bayangan */
}

#popup h2 {
    margin-top: 0;
    font-size: 18px;
    color: #333;
}

#popup label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

#popup input[type="date"],
#popup input[type="text"],
#popup select,
#popup input[type="number"] {
    width: calc(100% - 10px); /* Lebar input */
    padding: 5px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

#popup input[type="submit"],
#popup button {
    padding: 8px 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

#popup input[type="submit"]:hover,
#popup button:hover {
    background-color: #0056b3;
}

#popup button {
    background-color: #ccc;
    margin-top: 10px;
}

.pemasukan {
            background-color: #c1e7c1; /* Warna latar belakang hijau untuk pemasukan */
        }
        .pengeluaran {
            background-color: #ffb3b3; /* Warna latar belakang merah untuk pengeluaran */
        }
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
        select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-right: 10px;
        }
        button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        button.refresh {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            background-color: #ff7f0f;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button.refresh:hover {
            background-color: #ff990f;
        }
</style>

	<!--===== HEADER =====-->
    <header class="l-header">
            <nav class="nav bd-grid">
            <img src="gambar/logoviladago.png" alt="" width="100px">
                <div>
                <i class='bx bxs-user'></i>
                    <a href="#" class="nav__logo">Hallo, Bpk Sudarsana</a>
                </div>

                <div class="nav__menu" id="nav-menu">
                    <ul class="nav__list">
                        <li class="nav__item"><a href="dashboard_ketua.php" class="nav__link"><i class='bx bxs-home'></i> Home</a></li>
                        <li class="nav__item"><a href="pengaduan_rt.php" class="nav__link"><i class='bx bx-edit'></i> Pengaduan warga</a></li>
                        <li class="nav__item"><a href="keuangan_rt.php" class="nav__link"><i class='bx bx-coin-stack'></i> Laporan keuangan</a></li>
						<li class="nav__item"><a href="data_warga.php" class="nav__link"><i class='bx bxs-group'></i> Data warga</a></li>
                        <li class="nav__item"><a href="logout_rt.php" class="nav__link">Logout</a></li>
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
					<div class="head">
                    <form action="" method="GET">
                            <label for="status">Jenis:</label>
                            <select name="status" id="status">
                                <option value="">Semua</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                            <label for="bulan">Bulan:</label>
                            <select name="bulan" id="bulan">
                                <option value="">Semua</option>
                                <?php 
                                for($m=1; $m<=12; ++$m){
                                    $month_label = date('F', mktime(0, 0, 0, $m, 1));
                                    echo "<option value='$m'>$month_label</option>";
                                }
                                ?>
                            </select>
                            <label for="tahun">Tahun:</label>
                            <select name="tahun" id="tahun">
                                <option value="">Semua</option>
                                <?php 
                                $year = date('Y');
                                for($y=$year; $y>=2000; $y--){
                                    echo "<option value='$y'>$y</option>";
                                }
                                ?>
                            </select>
                            <button type="submit">
                                <i class='bx bx-filter-alt'></i>    
                                Filter
                            </button>

                            <button type="button" class="refresh" onclick="refreshPage()">
                                <i class='bx bx-sync'></i>
                                Refresh
                            </button>
                        </form>
					</div>

                    

					<table>
                        <thead>
                            <tr>
                                <th>Id keuangan</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Bukti transaksi</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $rowClass = $row["jenis"] == "pemasukan" ? "pemasukan" : "pengeluaran";
                                    echo "<tr class='$rowClass'><td>" . $row["keuangan_id"] . "</td><td>" . $row["tanggal"] . "</td><td>" . $row["deskripsi"] . "</td><td><a href='" . $row["bukti"] . "'>Lihat Bukti</a></td><td>" . $row["jenis"] . "</td><td>" . $row["jumlah"] . "</td></tr>";
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
                            <tr>
                                <td>Total Pemasukan:</td>
                                <td>Rp.<?php echo $totalPemasukan; ?>.000</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td>Total Pengeluaran:</td>
                                <td>Rp.<?php echo $totalPengeluaran; ?>.000</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td>Total Saldo:</td>
                                <td>Rp.<?php echo $totalPemasukan - $totalPengeluaran; ?>.000</td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>


					
				</div>	
			</div>
            
			
			
		</main>
		<!-- MAIN -->
		
	</section>
	<!-- CONTENT -->
	<script>
        function togglePopup() {
            var popup = document.getElementById("popup");
            popup.style.display = (popup.style.display == "block") ? "none" : "block";
        }
        function refreshPage() {
            window.location.href = window.location.pathname;
        }
    </script>

<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>