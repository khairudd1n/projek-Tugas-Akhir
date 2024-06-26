<?php
include('database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_rt.php");
    exit();
}

// Fungsi untuk menambahkan data ke dalam tabel keuangan
function tambahKeuangan($tanggal, $deskripsi, $jenis, $jumlah, $bukti) {
    global $con;
    $sql = "INSERT INTO keuangan (tanggal, deskripsi, jenis, jumlah, bukti) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssds", $tanggal, $deskripsi, $jenis, $jumlah, $bukti);
    if ($stmt->execute()) {
        echo "Data berhasil ditambahkan ke tabel keuangan.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Function to create a confirmation dialog
function confirmationDialog($action) {
    return "return confirm('Apakah Anda yakin untuk $action?')";
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
	.highlight {
    background-color: lightblue; /* Anda dapat mengubah warna sesuai keinginan */
  }
	/* Styling for approve button */
input[name='approve'] {
    background-color: #28a745;
    color: #fff;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
}

/* Styling for reject button */
input[name='reject'] {
    background-color: #dc3545;
    color: #fff;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
}

/* Styling for comment input */
input[name='komentar'] {
    padding: 5px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 150px; /* Adjust width as needed */
}

/* Optional: Hover effects for buttons */
input[name='approve']:hover,
input[name='reject']:hover {
    opacity: 0.8;
}

/* Optional: Hover effect for comment input */
input[name='komentar']:hover {
    border-color: #888;
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

	<title>E-RT TAMPAK SIRING (BENDAHARA)</title>
</head>
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
			

			<!-- Modal -->
			<div id="myModal" class="modal">
			<div class="modal-content">
				<span class="close" style='color: red;'>&times;</span>
				<h2>Detail iuran warga</h2>
				<table id="detailTable">
				<tr>
					<th>Detail</th>
					<th>Data</th>
				</tr>
				</table>
				<form id="approvalForm" action="" method="post">
				<input type="hidden" name="pembayaran_iuran_id">
				<br>
				<input type="submit" name="approve" value="Approve" style="background-color: green; color: white;">
				<br><br>
				<input type="text" name="komentar" placeholder="alasan ditolak.." style='width: 300px;'>
				<br>
				<input type="submit" name="reject" value="Reject" style="background-color: red; color: white;">
				</form>
			</div>
			</div>

			<!-- pending list -->
			<div class="table-data1">
				<div class="order">
					<div class="head">
						<h3>Daftar iuran yang perlu dikonfirmasi</h3>
					</div>
					<table>
						<thead>
							<tr>
								<th>Id iuran</th>
								<th>Deskripsi</th>
								<th>tanggal bayar</th>
                                <th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$queryData = "SELECT * FROM  pembayaran_iuran WHERE status = 'diproses' ORDER BY pembayaran_iuran_id ASC";
						$resultPending = mysqli_query($con, $queryData);

						if (!$resultPending) {
							echo "Error: " . mysqli_error($con);
						}

                        while ($row = mysqli_fetch_assoc($resultPending)) {
							if ($row['status'] == 'diproses') {
                            echo "<tr>";
							echo "<td>{$row['pembayaran_iuran_id']}</td>";
                            echo "<td>Iuran Bpk {$row['nama_kk']} {$row['bulan']} {$row['tahun']}</td>";
							echo "<td>{$row['tanggal']}</td>";
							echo "<td>";
							echo "<button type='button' onclick='showModal(\"{$row['pembayaran_iuran_id']}\", \"{$row['tanggal']}\", \"{$row['nama_kk']}\", \"{$row['bulan']}\", \"{$row['tahun']}\", \"{$row['bayaran_wajib']}\", \"{$row['bayaran_sukarela']}\", \"{$row['bukti_bayar']}\")' style='background-color: lightseagreen; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; display: flex; align-items: center;'>
							<i class='bx bx-edit' style='font-size: 1.2em; margin-right: 5px;'></i> Detail
							</button>";
							echo "</td>";
                            echo "</tr>";
							}
                        }
                        ?>
						</tbody>
					</table>

<?php
if(isset($_POST['approve'])){
    // Confirmation dialog before approving
    echo "<script>";
    echo "if(" . confirmationDialog("approve") . ") {";
    echo "var form = document.querySelector('form[action]');";
    echo "form.submit();";
    echo "}";
    echo "</script>";

    // Handle approval after confirmation
    $pembayaran_iuran_id = $_POST['pembayaran_iuran_id'];
        $select = "UPDATE pembayaran_iuran SET status = 'lunas' WHERE pembayaran_iuran_id = '$pembayaran_iuran_id' ";
        if (mysqli_query($con, $select)) {
            // Get the approved payment details
            $query = "SELECT * FROM pembayaran_iuran WHERE pembayaran_iuran_id = '$pembayaran_iuran_id'";
            $result = mysqli_query($con, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $tanggal = $row['tanggal'];
                $deskripsi = "Iuran Bpk {$row['nama_kk']} {$row['bulan']} {$row['tahun']}";
                $jenis = "pemasukan";
                $jumlah = $row['bayaran_wajib'] + $row['bayaran_sukarela'];
                $bukti = $row['bukti_bayar'];

                // Call the function to add to keuangan table
                tambahKeuangan($tanggal, $deskripsi, $jenis, $jumlah, $bukti);
            }
        }
}


if(isset($_POST['reject'])){
    // Confirmation dialog before rejecting
    echo "<script>";
    echo "if(" . confirmationDialog("reject") . ") {";
    echo "var form = document.querySelector('form[action]');";
    echo "form.submit();";
    echo "}";
    echo "</script>";

    // Handle rejection after confirmation
    $pembayaran_iuran_id = $_POST['pembayaran_iuran_id'];
    $komentar = $_POST['komentar']; // Retrieve comment from form
    $select = "UPDATE pembayaran_iuran SET status = 'ditolak', komentar = '$komentar' WHERE pembayaran_iuran_id = '$pembayaran_iuran_id' ";
    $result = mysqli_query($con,$select);
}

?>
					
				</div>	
			</div>
			

			
			<!-- approve list -->
			<div class="table-data2">
				<div class="order">
					<div class="head">
						<h3>Daftar iuran yang telah lunas</h3>
					</div>
					<table>
						<thead>
							<tr>
								<th>Id iuran</th>
								<th>Deskripsi</th>
								<th>tanggal bayar</th>
                                <th>Bayaran wajib</th>
                                <th>Bayaran sukarela</th>
                                <th>Bukti bayar</th>
                                <th>Status</th>
							</tr>
						</thead>
						<tbody>
                        <?php
						$queryData = "SELECT * FROM  pembayaran_iuran WHERE status = 'lunas' ORDER BY pembayaran_iuran_id ASC";
						$resultApprove = mysqli_query($con, $queryData);

						if (!$resultApprove) {
							echo "Error: " . mysqli_error($con);
						}

                        while ($row = mysqli_fetch_assoc($resultApprove)) {
                            echo "<tr>";
							echo "<td>{$row['pembayaran_iuran_id']}</td>";
                            echo "<td>Iuran Bpk {$row['nama_kk']} {$row['bulan']} {$row['tahun']}</td>";
							echo "<td>{$row['tanggal']}</td>";
                            echo "<td>{$row['bayaran_wajib']}</td>";
                            echo "<td>{$row['bayaran_sukarela']}</td>";
                            echo "<td><a href='" . $row['bukti_bayar'] . "' download>Unduh</a></td>";
							echo "<td>{$row['status']}</td>";
                            echo "</tr>";
                        }
                        ?>
						</tbody>
					</table>
				</div>	
			</div>

			<!-- reject list -->
			<div class="table-data3">
				<div class="order">
					<div class="head">
						<h3>Daftar iuran yang telah ditolak</h3>
					</div>
					<table>
						<thead>
							<tr>
								<th>Id iuran</th>
								<th>Deskripsi</th>
								<th>tanggal bayar</th>
                                <th>Bayaran wajib</th>
                                <th>Bayaran sukarela</th>
                                <th>Bukti bayar</th>
                                <th>Status</th>
								<th>Komentar</th>
							</tr>
						</thead>
						<tbody>
                        <?php
						$queryData = "SELECT * FROM  pembayaran_iuran WHERE status = 'ditolak' ORDER BY pembayaran_iuran_id ASC";
						$resultReject = mysqli_query($con, $queryData);

						if (!$resultReject) {
							echo "Error: " . mysqli_error($con);
						}

                        while ($row = mysqli_fetch_assoc($resultReject)) {
                            echo "<tr>";
							echo "<td>{$row['pembayaran_iuran_id']}</td>";
                            echo "<td>Iuran Bpk {$row['nama_kk']} {$row['bulan']} {$row['tahun']}</td>";
							echo "<td>{$row['tanggal']}</td>";
                            echo "<td>{$row['bayaran_wajib']}</td>";
                            echo "<td>{$row['bayaran_sukarela']}</td>";
                            echo "<td><a href='" . $row['bukti_bayar'] . "' download>Unduh</a></td>";
							echo "<td>{$row['status']}</td>";
							echo "<td>{$row['komentar']}</td>";
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
    // Fungsi untuk memuat ulang halaman secara otomatis
    function reloadPage() {
        location.reload();
    }
</script>

<!-- JavaScript for modal -->
<script>
  // Get the modal
  var modal = document.getElementById("myModal");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks the button, open the modal 
  function showModal(pembayaran_iuran_id, tanggal, nama_kk, bulan, tahun, bayaran_wajib, bayaran_sukarela, bukti_bayar) {
    modal.style.display = "block";
    document.querySelector("input[name='pembayaran_iuran_id']").value = pembayaran_iuran_id;

    // Display details in the modal
    var detailTable = document.getElementById("detailTable");
    detailTable.innerHTML = `
	  <tr><td class="highlight">Id pembayaran iuran:</td><td>${pembayaran_iuran_id}</td></tr>
      <tr><td class="highlight">Tanggal:</td><td>${tanggal}</td></tr>
      <tr><td class="highlight">Nama:</td><td>${nama_kk}</td></tr>
      <tr><td class="highlight">Iuran Bulan:</td><td>${bulan}</td></tr>
	  <tr><td class="highlight">Iuran Tahun:</td><td>${tahun}</td></tr>
      <tr><td class="highlight">Bayaran wajib:</td><td>${bayaran_wajib}</td></tr>
      <tr><td class="highlight">Bayaran sukarela:</td><td>${bayaran_sukarela}</td></tr>
      <tr><td class="highlight">Bukti Bayar:</td><td><a href="${bukti_bayar}" data-lightbox="bukti-bayar"><img src="${bukti_bayar}" alt="Bukti Bayar" style="max-width: 200px; max-height: 200px;"></a></td></tr>
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