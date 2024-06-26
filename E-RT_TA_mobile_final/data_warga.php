<?php
include('database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_rt.php");
    exit();
}

$sql = "SELECT user_id, no_nik, nama, no_rumah, no_telepon, no_kk, pekerjaan, agama, tempat_lahir, tanggal_lahir FROM user WHERE role = 'kepala keluarga'";


// Cek apakah ada pencarian nama warga
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " AND nama LIKE '%$search%'";
}

$result = $con->query($sql);


?>

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

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }
	#content nav form .form-input {
	display: flex;
	align-items: center;
	height: 36px;
}
#content nav form .form-input input {
	flex-grow: 1;
	padding: 0 16px;
	height: 100%;
	border: none;
	background: var(--grey);
	border-radius: 36px 0 0 36px;
	outline: none;
	width: 100%;
	color: var(--dark);
    background-color: lightgrey;
}
#content nav form .form-input button {
	width: 36px;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	background: var(--blue);
	color: var(--light);
	font-size: 18px;
	border: none;
	outline: none;
	border-radius: 0 36px 36px 0;
	cursor: pointer;
    background-color: blueviolet;
}

</style>


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
                <h3>Tabel data warga Tampak Siring</h3>
                <nav>
				<form action="#" method="get">
                <div class="form-input">
                    <input type="search" name="search" placeholder="Search nama...">
                    <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
                </div>
            </form>
                </nav>
                <br>
                <table>
						<thead>
							<tr>
								<th>Id</th>
								<th>Nama kepala keluarga</th>
								<th>NIK</th>
								<th>No.rumah</th>
								<th>No.telepon</th>
								<th>No.KK</th>
								<th>Agama</th>
								<th>Pekerjaan</th>
								<th>Tempat lahir</th>
								<th>Tanggal lahir</th>
								<th>Anggota keluarga</th>
							</tr>
						</thead>
                        <tbody>
						<?php
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>{$row['user_id']}</td>";
                                    echo "<td>{$row['nama']}</td>";
                                    echo "<td>{$row['no_nik']}</td>";
                                    echo "<td>{$row['no_rumah']}</td>";
                                    echo "<td>{$row['no_telepon']}</td>";
									echo "<td>{$row['no_kk']}</td>";
									echo "<td>{$row['agama']}</td>";
									echo "<td>{$row['pekerjaan']}</td>";
									echo "<td>{$row['tempat_lahir']}</td>";
									echo "<td>{$row['tanggal_lahir']}</td>";
									echo "<td><a href='#' onclick=\"showAnggotaWarga('{$row['nama']}')\">Lihat Lebih Detail</a></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                </table>
				
			</div>	
		</div>

			
		</main>
		<!-- MAIN -->
		
	</section>
	<!-- CONTENT -->
	<script>
    function showAnggotaWarga(nama) {
        // Membuka pop-up dengan lebar dan tinggi tertentu
        var popup = window.open("", "_blank", "width=800,height=600");

        // Membuat HTML untuk menampilkan data anggota warga
        var html = "<html><head><title>Data Anggota Keluarga</title>";
        html += "<style>";
        html += "body { font-family: Arial, sans-serif; margin: 20px; }";
        html += "h2 { text-align: center; }";
        html += "table { width: 100%; border-collapse: collapse; margin-top: 20px; }";
        html += "th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }";
        html += "th { background-color: #f2f2f2; }";
        html += "tr:nth-child(even) { background-color: #f9f9f9; }";
        html += "tr:hover { background-color: #f1f1f1; }";
        html += ".center { text-align: center; padding: 10px; }";
        html += "</style></head><body>";
        html += "<h2>Data Anggota Keluarga dari bapak " + nama + "</h2>";
        html += "<table>";
        html += "<tr><th>Nama</th><th>Status Hubungan</th><th>NIK</th><th>No. Rumah</th><th>No. Telepon</th><th>No. KK</th><th>Jenis Kelamin</th><th>Agama</th><th>Pekerjaan</th><th>Tempat Lahir</th><th>Tanggal Lahir</th></tr>";

        // Mengambil data anggota warga dengan AJAX
        $.ajax({
            url: "get_anggota_warga.php",
            type: "GET",
            data: { nama: nama },
            success: function(response) {
                var anggotaWarga = JSON.parse(response);
                if (anggotaWarga.length === 0) {
                    // Jika tidak ada data anggota keluarga
                    html += "<tr><td colspan='11' class='center'>Kepala keluarga belum mengisi data anggota keluarga</td></tr>";
                } else {
                    // Jika ada data anggota keluarga
                    anggotaWarga.forEach(function(anggota) {
                        html += "<tr>";
                        html += "<td>" + anggota.nama_anggota_keluarga + "</td>";
                        html += "<td>" + anggota.status_hubungan + "</td>";
                        html += "<td>" + anggota.no_nik + "</td>";
                        html += "<td>" + anggota.no_rumah + "</td>";
                        html += "<td>" + anggota.no_telepon + "</td>";
                        html += "<td>" + anggota.no_kk + "</td>";
                        html += "<td>" + anggota.jenis_kelamin + "</td>";
                        html += "<td>" + anggota.agama + "</td>";
                        html += "<td>" + anggota.pekerjaan + "</td>";
                        html += "<td>" + anggota.tempat_lahir + "</td>";
                        html += "<td>" + anggota.tanggal_lahir + "</td>";
                        html += "</tr>";
                    });
                }
                html += "</table></body></html>";
                popup.document.write(html);
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    }
</script>

<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>