<?php
include('database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_rt.php");
    exit();
}


					// Query untuk menghitung jumlah pengguna dengan role 'kepala keluarga'
					$queryCountUsers = "SELECT COUNT(*) as totalUsers FROM user WHERE role = 'kepala keluarga'";
					$resultCountUsers = mysqli_query($con, $queryCountUsers);

					if (!$resultCountUsers) {
						echo "Error: " . mysqli_error($con);
					} else {
						$row = mysqli_fetch_assoc($resultCountUsers);
						$totalUsers = $row['totalUsers'];
					}

					//
					$queryCountApproved = "SELECT COUNT(*) as totalApproved FROM pembayaran_iuran WHERE status = 'lunas'";
					$resultCountApproved = mysqli_query($con, $queryCountApproved);

					if (!$resultCountApproved) {
						echo "Error: " . mysqli_error($con);
					} else {
						$row = mysqli_fetch_assoc($resultCountApproved);
						$totalApproved = $row['totalApproved'];
					}

					//
					$queryCountRejected = "SELECT COUNT(*) as totalRejected FROM pembayaran_iuran WHERE status = 'ditolak'";
					$resultCountRejected = mysqli_query($con, $queryCountRejected);

					if (!$resultCountRejected) {
						echo "Error: " . mysqli_error($con);
					} else {
						$row = mysqli_fetch_assoc($resultCountRejected);
						$totalRejected = $row['totalRejected'];
					}

					//
					$queryCountPending = "SELECT COUNT(*) as totalPending FROM pembayaran_iuran WHERE status = 'diproses'";
					$resultCountPending = mysqli_query($con, $queryCountPending);

					if (!$resultCountPending) {
						echo "Error: " . mysqli_error($con);
					} else {
						$row = mysqli_fetch_assoc($resultCountPending);
						$totalPending = $row['totalPending'];
					}

					//
					$queryTotalWajibAmount = "SELECT SUM(bayaran_wajib) as totalWajibAmount FROM pembayaran_iuran WHERE status = 'lunas'";
					$resultTotalWajibAmount = mysqli_query($con, $queryTotalWajibAmount);
					
					if (!$resultTotalWajibAmount) {
						echo "Error: " . mysqli_error($con);
					} else {
						$row = mysqli_fetch_assoc($resultTotalWajibAmount);
						$totalWajibAmount = $row['totalWajibAmount'];

						$wajibAmount = "Rp " . number_format($totalWajibAmount, 3, '.', '.');
					}

					//
					$queryTotalApprovedAmount = "SELECT SUM(bayaran_sukarela) as totalApprovedAmount FROM pembayaran_iuran WHERE status = 'lunas'";
					$resultTotalApprovedAmount = mysqli_query($con, $queryTotalApprovedAmount);
					
					if (!$resultTotalApprovedAmount) {
						echo "Error: " . mysqli_error($con);
					} else {
						$row = mysqli_fetch_assoc($resultTotalApprovedAmount);
						$totalApprovedAmount = $row['totalApprovedAmount'];

						$formattedAmount = "Rp " . number_format($totalApprovedAmount, 3, '.', '.');
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

	<title>E-RT TAMPAK SIRING (BENDAHARA)</title>
</head>
<style>
	.box-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    list-style-type: none;
    padding: 0;
    margin: 0;
	margin-top: 80px;
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
</style>
<body>


	<!--===== HEADER =====-->
    <header class="l-header">
            <nav class="nav bd-grid">
            <img src="gambar/logoviladago.png" alt="" width="100px">
                <div>
                <i class='bx bxs-user'></i>
                    <a href="#" class="nav__logo">Hallo, Ibu Fitriani</a>
                </div>

                <div class="nav__menu" id="nav-menu">
                    <ul class="nav__list">
                        <li class="nav__item"><a href="dashboard_bendahara.php" class="nav__link"><i class='bx bxs-home'></i> Home</a></li>
                        <li class="nav__item"><a href="data_iuran.php" class="nav__link"><i class='bx bx-money'></i> Data iuran</a></li>
                        <li class="nav__item"><a href="keuangan_bendahara.php" class="nav__link"><i class='bx bx-coin-stack'></i> Laporan keuangan</a></li>
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
			

<ul class="box-info">

	<li class="box-item">
        <a href="" class="box-content">
            <i class='bx bxs-group'></i>
			<p>jumlah kepala keluarga</p>
            <h1><?php echo $totalUsers?></h1>
        </a>
    </li>
	<li class="box-item">	
		<a href="data_iuran.php" class="box-content">
			<i class='bx bxs-check-circle'></i>		
			<p>Jumlah iuran yang lunas:</p>
			<h1><?php echo $totalApproved?></h1>
		</a>
	</li>

	<li class="box-item">
		<a href="data_iuran.php" class="box-content">
			<i class='bx bxs-x-circle'></i>
			<p>Jumlah iuran yang ditolak:</p>
			<h3><?php echo $totalRejected?></h3>
		</a>
	</li>

	<li class="box-item">
		<a href="data_iuran.php" class="box-content">
			<i class='bx bxs-minus-circle'></i>
			<p>Jumlah iuran yang perlu dikonfirmasi:</p>
			<h3><?php echo $totalPending?></h3>
		</a>				
	</li>
	<li class="box-item">
		<a href="" class="box-content">
			<i class='bx bx-money'></i>
			<p>Jumlah iuran wajib:</p>
			<h3><?php echo $wajibAmount ?></h3>
		</a>
	</li>
	<li class="box-item">
		<a href="" class="box-content">
			<i class='bx bx-money'></i>
			<p>Jumlah iuran sukarela:</p>
			<h3><?php echo $formattedAmount ?></h3>
		</a>
	</li>
</ul>


			
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>