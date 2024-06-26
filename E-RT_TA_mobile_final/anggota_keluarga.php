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
$queryGetUser = "SELECT nama, no_rumah FROM user WHERE user_id = '$user_id'";
$resultGetUser = mysqli_query($con, $queryGetUser);

if ($resultGetUser) {
    $userData = mysqli_fetch_assoc($resultGetUser);
    $nama = $userData['nama'];
    $no_rumah = $userData['no_rumah'];
} else {
    echo "Error: " . mysqli_error($con);
}



// Jika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $nama = $_POST['nama'];
    $no_rumah = $_POST['no_rumah'];
	$no_nik = $_POST['no_nik'];
	$nama_anggota_keluarga = $_POST['nama_anggota_keluarga'];
	$status_hubungan = $_POST['status_hubungan'];
	$no_telepon = $_POST['no_telepon'];
    $no_kk = $_POST['no_kk'];
    $pekerjaan = $_POST['pekerjaan'];
    $agama = $_POST['agama'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    

    
        // Simpan data ke database
        $query = "INSERT INTO anggota_keluarga (nama, no_rumah, no_telepon, no_nik, nama_anggota_keluarga, status_hubungan, no_kk, pekerjaan, agama, tempat_lahir, tanggal_lahir, jenis_kelamin) 
        		  VALUES ('$nama',  '$no_rumah', '$no_telepon', '$no_nik', '$nama_anggota_keluarga', '$status_hubungan', '$no_kk', '$pekerjaan', '$agama', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin')";

        $result = mysqli_query($con, $query);

        if ($result) { 
        $successMessage = "Selamat! anda berhasil menambah data anggota keluarga anda";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } 



// Ambil ID warga dari session
$nama = $_SESSION['nama'];

// Ambil data riwayat pembayaran berdasarkan ID warga
$queryRiwayat = "SELECT * FROM anggota_keluarga WHERE nama = '$nama'";
$resultRiwayat = mysqli_query($con, $queryRiwayat);

if (!$resultRiwayat) {
    die("Query error: " . mysqli_error($con));
}

// Jika ada permintaan untuk menghapus anggota keluarga
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    
    // Lakukan penghapusan data di database
    $queryDelete = "DELETE FROM anggota_keluarga WHERE anggota_keluarga_id = ?";
    $stmt = mysqli_prepare($con, $queryDelete);
    mysqli_stmt_bind_param($stmt, "i", $deleteId);
    
    if (mysqli_stmt_execute($stmt)) {
        // Penghapusan berhasil
        $successMessage = "Data anggota berhasil dihapus.";
    } else {
        // Gagal menghapus data
        echo "Error: " . mysqli_error($con);
    }
    
    mysqli_stmt_close($stmt);
}

// Ambil ID warga dari session
$nama = $_SESSION['nama'];

// Ambil data anggota keluarga
$queryRiwayat = "SELECT * FROM anggota_keluarga WHERE nama = '$nama'";
$resultRiwayat = mysqli_query($con, $queryRiwayat);

if (!$resultRiwayat) {
    die("Query error: " . mysqli_error($con));
}


?>

<!DOCTYPE html>
<html lang="en">
<head>

<style>
    .success-message {
        background-color: #4caf50;
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 10px;
    }

    .table-data {
        margin-top: 20px; /* Add some margin for better separation */
    }
    
    .table-data1 {
        margin-top: 80px; /* Add some margin for better separation */
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

    .edit-btn {
        padding: 8px 15px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
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

    .delete-btn {
        padding: 8px 15px;
        background-color: #dc3545;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    /* Gaya untuk menyembunyikan modal secara default */
    #editModal {
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

    /* Gaya untuk konten modal */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 60%; /* Lebar konten modal */
        transform: translateY(10%); /* Vertical centering */
    }

    /* Gaya untuk tombol close modal */
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

    .modal-content button[type="submit"] {
        margin-top: 20px;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        cursor: pointer;
    }

    .modal-content button[type="submit"]:hover {
        background-color: #45a049;
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

        .modal-content {
            width: 90%; /* Adjust modal width for smaller screens */
            transform: translateY(50%); /* Center vertically for smaller screens */
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
					<h3>Data keluarga anda</h3>
						</div>
					<table>
						<thead>
							<tr>
								<th>Status hubungan</th>
								<th>Nama anggota keluarga</th>
								<th>NIK</th>
                                <th>No.telepon</th>
                                <th>No.KK</th>
                                <th>Jenis kelamin</th>
                                <th>Pekerjaan</th>
                                <th>agama</th>
                                <th>Tempat lahir</th>
                                <th>Tanggal lahir</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php
					
                    while ($row = mysqli_fetch_assoc($resultRiwayat)) {
                        echo "<tr>";
                        echo "<td>" . $row['status_hubungan'] . "</td>";
                        echo "<td>" . $row['nama_anggota_keluarga'] . "</td>";
                        echo "<td>" . $row['no_nik'] . "</td>";
                        echo "<td>" . $row['no_telepon'] . "</td>";
                        echo "<td>" . $row['no_kk'] . "</td>";
                        echo "<td>" . $row['jenis_kelamin'] . "</td>";
                        echo "<td>" . $row['pekerjaan'] . "</td>";
                        echo "<td>" . $row['agama'] . "</td>";
                        echo "<td>" . $row['tempat_lahir'] . "</td>";
                        echo "<td>" . $row['tanggal_lahir'] . "</td>";
						echo "<td><button class='edit-btn' data-id='" . $row['anggota_keluarga_id'] . "'>Edit</button><button class='delete-btn' data-id='" . $row['anggota_keluarga_id'] . "'>Hapus</button></td>";
                        echo "</tr>";
                    }
                    ?>
						</tbody>
					</table>
				
			</div>
		</div>

        
		
			<div class="table-data2">
				<div class="order">
					<div class="head">
						<h3>Form tambah data anggota keluarga</h3>
					</div>
		
						<form method="post" enctype="multipart/form-data">
						<label for="nama">Nama kepala keluarga:</label>
						<input type="text" name="nama" placeholder="" value="<?php echo htmlspecialchars($nama); ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
						<label for="rumah">No rumah:</label>
						<input type="text" name="no_rumah" placeholder="" value="<?php echo htmlspecialchars($no_rumah); ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
						<label for="nama_anggota_keluarga">Nama anggota keluarga:</label>
						<input type="text" name="nama_anggota_keluarga" required><br>
                        <label for="jenis_kelamin">Jenis kelamin:</label>
                        <select name="jenis_kelamin" required>
							<option value="lelaki">lelaki</option>
							<option value="perempuan">perempuan</option>
					    </select><br>
                        <label for="no_kk">No.KK:</label>
                        <input type="text" name="no_kk" required><br>
                        <label for="nik">NIK:</label>
						<input type="text" name="no_nik" required><br>
						<label for="telepon">No telepon:</label>
                        <input type="text" name="no_telepon" required><br>
                        <label for="agama">Agama:</label>
                        <select name="agama" required>
                            <option value="tidak ada">tidak ada</option>
							<option value="islam">islam</option>
							<option value="kristen protestan">kristen protestan</option>
                            <option value="katolik">katolik</option>
							<option value="hindu">hindu</option>
                            <option value="buddha">buddha</option>
							<option value="konghucu">konghucu</option>
					    </select><br>
                        <label for="status_hubungan">Status hubungan:</label>
						<input type="text" name="status_hubungan" required><br>
                        <label for="pekerjaan">Pekerjaan:</label>
                        <input type="text" name="pekerjaan" required><br>
                        <label for="tempat_lahir">Tempat lahir:</label>
                        <input type="text" name="tempat_lahir" required><br>
                        <label for="tanggal_lahir">Tanggal lahir:</label>
                        <input type="text" name="tanggal_lahir" required><br>
						<input type="submit" name="submit" value="Tambah data">    
					</form>
				</div>	
			</div>

<!-- Edit Modal Form -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="editForm">
            <input type="hidden" id="editAnggotaKeluargaId" name="anggota_keluarga_id">
            <label for="editStatusHubungan">Status hubungan:</label>
            <input type="text" id="editStatusHubungan" name="status_hubungan"><br>
            <label for="editNamaAnggotaKeluarga">Nama anggota keluarga:</label>
            <input type="text" id="editNamaAnggotaKeluarga" name="nama_anggota_keluarga"><br>
            <label for="editNoNik">NIK:</label>
            <input type="text" id="editNoNik" name="no_nik"><br>
            <label for="editNoTelepon">No.telepon:</label>
            <input type="text" id="editNoTelepon" name="no_telepon"><br>
            <label for="editNoKk">No.KK:</label>
            <input type="text" id="editNoKk" name="no_kk"><br>
            <label for="editJenisKelamin">Jenis kelamin:</label>
            <input type="text" id="editJenisKelamin" name="jenis_kelamin"><br>
            <label for="editPekerjaan">Pekerjaan:</label>
            <input type="text" id="editPekerjaan" name="pekerjaan"><br>
            <label for="editAgama">Agama:</label>
            <input type="text" id="editAgama" name="agama"><br>
            <label for="editTempatLahir">Tempat lahir:</label>
            <input type="text" id="editTempatLahir" name="tempat_lahir"><br>
            <label for="editTanggalLahir">Tanggal lahir:</label>
            <input type="text" id="editTanggalLahir" name="tanggal_lahir"><br>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>


		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<script src="script.js"></script>
	
	 <!-- Script JavaScript untuk menangani penghapusan -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var deleteButtons = document.querySelectorAll(".delete-btn");
            deleteButtons.forEach(function(button) {
                button.addEventListener("click", function() {
                    var anggotaId = this.getAttribute("data-id");
                    var confirmation = confirm("Apakah Anda yakin ingin menghapus data anggota ini?");
                    if (confirmation) {
                        // Kirim permintaan penghapusan ke server
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                // Handle response from server if needed
                                location.reload(); // Reload halaman setelah penghapusan berhasil
                            }
                        };
                        xhr.send("delete_id=" + anggotaId);
                    }
                });
            });
        });
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('editModal');
    var span = document.getElementsByClassName('close')[0];
    var editForm = document.getElementById('editForm');

    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var row = button.closest('tr');
            var data = {
                anggota_keluarga_id: button.getAttribute('data-id'),
                status_hubungan: row.children[0].textContent,
                nama_anggota_keluarga: row.children[1].textContent,
                no_nik: row.children[2].textContent,
                no_telepon: row.children[3].textContent,
                no_kk: row.children[4].textContent,
                jenis_kelamin: row.children[5].textContent,
                pekerjaan: row.children[6].textContent,
                agama: row.children[7].textContent,
                tempat_lahir: row.children[8].textContent,
                tanggal_lahir: row.children[9].textContent
            };
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    var inputId = 'edit' + key.charAt(0).toUpperCase() + key.slice(1).replace(/_([a-z])/g, function (g) { return g[1].toUpperCase(); });
                    var input = document.getElementById(inputId);
                    if (input) {
                        input.value = data[key];
                    } else {
                        console.error('Element with ID ' + inputId + ' not found.');
                    }
                }
            }
            modal.style.display = 'block';
        });
    });

    span.onclick = function () {
        modal.style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    editForm.onsubmit = function (e) {
        e.preventDefault();
        var formData = new FormData(editForm);
        fetch('edit_anggota.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data berhasil diperbarui');
                location.reload();
            } else {
                alert('Gagal memperbarui data');
            }
        })
        .catch(error => console.error('Error:', error));
    }
});


</script>
<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>

</body>
</html>