<?php
include('database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_rt.php");
    exit();
}
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
		.table-data3 {
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
	/* Styling for approve button */
input[name='diproses'] {
    background-color: #28a745;
    color: #fff;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
}

/* Styling for reject button */
input[name='selesai'] {
    background-color: #dc3545;
    color: #fff;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
}

/* Optional: Hover effects for buttons */
input[name='diproses']:hover,
input[name='selesai']:hover {
    opacity: 0.8;
}
/* Style untuk modal */
.modal {
    display: none; /* Modal tidak terlihat saat halaman dimuat */
        position: fixed; /* Tetap di posisi tetap di layar */
        z-index: 1; /* Atur tumpukan z agar modal muncul di atas konten lain */
        left: 0;
        top: 0;
        width: 100%; /* Lebar modal menutupi seluruh layar */
        height: 100%; /* Tinggi modal menutupi seluruh layar */
        overflow: auto; /* Tambahkan scroll jika konten melebihi layar */
        background-color: rgb(0,0,0); /* Warna latar belakang gelap */
        background-color: rgba(0,0,0,0.4); /* Warna latar belakang gelap dengan transparansi */
  }
/* Style untuk konten modal */
.modal-content {
    background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 60%; /* Lebar konten modal */
        transform: translateY(10%); /* Vertical centering */
  }

  /* Style untuk tombol close */
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
  .highlight {
    background-color: lightblue; /* Anda dapat mengubah warna sesuai keinginan */
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
			
			<!-- Modal -->
			<div id="myModal" class="modal">
			<div class="modal-content">
				<span class="close" style='color: red;'>&times;</span>
				<h2>Detail Pengaduan</h2>
				<table id="detailTable">
				<tr>
					<th>Detail</th>
					<th>Data</th>
				</tr>
				</table>
				<div id="buktiAduanContainer"></div>
				<form id="approvalForm" action="" method="post">
				<input type="hidden" name="pengaduan_id">
				<br>
				<input type="submit" name="diproses" value="diproses" style="background-color: green; color: white;">
				<br><br>
				<input type="submit" name="selesai" value="selesaikan" style="background-color: red; color: white;">
				</form>
			</div>
			</div>

			<!-- list awal -->
			<div class="table-data1">
				<div class="order">
				<div class="head">
					<h3>Proses aduan tahap awal</h3>
				</div>
					<table>
						<thead>
							<tr>
								<th>Id pengaduan</th>
                                <th>Tanggal</th>
								<th>Nama</th>
								<th>Kategori</th>
                                <th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$queryData = "SELECT * FROM  pengaduan WHERE status = 'belum dicek' ORDER BY pengaduan_id ASC";
						$resultPending = mysqli_query($con, $queryData);

						if (!$resultPending) {
							echo "Error: " . mysqli_error($con);
						}

                        while ($row = mysqli_fetch_assoc($resultPending)) {
                            echo "<tr>";
							echo "<td>{$row['pengaduan_id']}</td>";
                            echo "<td>{$row['tanggal']}</td>";
							echo "<td>{$row['nama_pelapor']}</td>";
                            echo "<td>{$row['kategori_masalah']}</td>";
							echo "<td>";
							echo "<button type='button' onclick='showModal(\"{$row['pengaduan_id']}\", \"{$row['tanggal']}\", \"{$row['nama_pelapor']}\", \"{$row['no_rumah']}\", \"{$row['no_telepon']}\", \"{$row['kategori_masalah']}\", \"{$row['deskripsi_masalah']}\", \"{$row['bukti_aduan']}\")' style='background-color: lightseagreen; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; display: flex; align-items: center;'>
							<i class='bx bx-edit' style='font-size: 1.2em; margin-right: 5px;'></i> Detail
							</button>";
							echo "</td>";
                            echo "</tr>";
                        }
                        ?>
						</tbody>
					</table>

<?php
if(isset($_POST['diproses'])){

	$pengaduan_id = $_POST['pengaduan_id'];
	$select = "UPDATE pengaduan SET status = 'diproses' WHERE pengaduan_id = '$pengaduan_id' ";
	$resut = mysqli_query($con,$select);
}


if(isset($_POST['selesai'])){

	$pengaduan_id = $_POST['pengaduan_id'];
	$select = "UPDATE pengaduan SET status = 'selesai' WHERE pengaduan_id = '$pengaduan_id' ";
	$resut = mysqli_query($con,$select);
}

?>
					
</div>	
 </div>
			
			<!-- list final -->
			<div class="table-data2">
				<div class="order">
				<div class="head">
					<h3>Proses aduan tahap final</h3>
				</div>
					<table>
						<thead>
							<tr>
								<th>Id pengaduan</th>
                                <th>Tanggal</th>
								<th>Nama</th>
								<th>No rumah</th>
								<th>Kategori</th>
								<th>Deskripsi</th>
                                <th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$queryData = "SELECT * FROM  pengaduan WHERE status = 'diproses' ORDER BY pengaduan_id ASC";
						$resultPending = mysqli_query($con, $queryData);

						if (!$resultPending) {
							echo "Error: " . mysqli_error($con);
						}

                        while ($row = mysqli_fetch_assoc($resultPending)) {
                            echo "<tr>";
							echo "<td>{$row['pengaduan_id']}</td>";
                            echo "<td>{$row['tanggal']}</td>";
							echo "<td>{$row['nama_pelapor']}</td>";
                            echo "<td>{$row['no_rumah']}</td>";
                            echo "<td>{$row['kategori_masalah']}</td>";
                            echo "<td>{$row['deskripsi_masalah']}</td>";
							echo "<td>";
							echo "<form action='' method='post'>";
							echo "<input type='hidden' name='pengaduan_id' value='{$row['pengaduan_id']}'>";
							echo "<input type='submit' name='selesai' value='selesaikan' style='background-color: green; color: white;'>";
							echo "</form>";
							echo "</td>";
                            echo "</tr>";
                        }
                        ?>
						</tbody>
					</table>

<?php
if(isset($_POST['selesai'])){

	$pengaduan_id = $_POST['pengaduan_id'];
	$select = "UPDATE pengaduan SET status = 'selesai' WHERE pengaduan_id = '$pengaduan_id' ";
	$resut = mysqli_query($con,$select);
}

?>
					
</div>	
 </div>

                        
						</tbody>
					</table>
				</div>	
			</div>

			<!-- riwayat pengaduan yang selesai -->
			<div class="table-data3">
				<div class="order">
				<div class="head">
					<h3>Riwayat pengaduan warga</h3>
				</div>
					<table>
						<thead>
							<tr>
								<th>Id pengaduan</th>
                                <th>Tanggal</th>
								<th>Nama</th>
								<th>No rumah</th>
								<th>Kategori</th>
								<th>Deskripsi</th>
                                <th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$queryData = "SELECT * FROM  pengaduan WHERE status = 'selesai' ORDER BY pengaduan_id ASC";
						$resultPending = mysqli_query($con, $queryData);

						if (!$resultPending) {
							echo "Error: " . mysqli_error($con);
						}

                        while ($row = mysqli_fetch_assoc($resultPending)) {
                            echo "<tr>";
							echo "<td>{$row['pengaduan_id']}</td>";
                            echo "<td>{$row['tanggal']}</td>";
							echo "<td>{$row['nama_pelapor']}</td>";
                            echo "<td>{$row['no_rumah']}</td>";
                            echo "<td>{$row['kategori_masalah']}</td>";
                            echo "<td>{$row['deskripsi_masalah']}</td>";
							echo "<td>{$row['status']}</td>";
                            echo "</tr>";
                        }
                        ?>
						</tbody>
					</table>
					
</div>	
 </div>

                        
						</tbody>
					</table>
				</div>	
			</div>
		</main>
		<!-- MAIN -->
		
	</section>
	<!-- CONTENT -->
	<script>
  // Get the modal
  var modal = document.getElementById("myModal");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks the button, open the modal 
  function showModal(pengaduan_id, tanggal, nama_pelapor, no_rumah, no_telepon, kategori_masalah, deskripsi_masalah, bukti_aduan) {
    modal.style.display = "block";
    document.querySelector("input[name='pengaduan_id']").value = pengaduan_id;

    // Display details in the modal
    var detailTable = document.getElementById("detailTable");
    detailTable.innerHTML = `
	  <tr><td class="highlight">Id pengaduan:</td><td>${pengaduan_id}</td></tr>
      <tr><td class="highlight">Tanggal:</td><td>${tanggal}</td></tr>
      <tr><td class="highlight">Nama pelapor:</td><td>${nama_pelapor}</td></tr>
      <tr><td class="highlight">No rumah:</td><td>${no_rumah}</td></tr>
      <tr><td class="highlight">No telepon:</td><td>${no_telepon}</td></tr>
      <tr><td class="highlight">Kategori masalah:</td><td>${kategori_masalah}</td></tr>
      <tr><td class="highlight">Deskripsi masalah:</td><td>${deskripsi_masalah}</td></tr>
	  <tr><td class="highlight">Bukti aduan:</td><td><a href="${bukti_aduan}" data-lightbox="bukti-aduan"><img src="${bukti_aduan}" alt="Bukti aduan" style="max-width: 200px; max-height: 200px;"></a></td></tr>
    `;

	 
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>

<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>