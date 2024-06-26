<?php
session_start();

include('database.php');

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
$queryGetUser = "SELECT nama FROM user WHERE user_id = '$user_id'";
$resultGetUser = mysqli_query($con, $queryGetUser);

if ($resultGetUser) {
    $userData = mysqli_fetch_assoc($resultGetUser);
    $nama = $userData['nama'];
} else {
    echo "Error: " . mysqli_error($con);
}

$queryGetUser = "SELECT * FROM user WHERE user_id = '$user_id'";
$resultGetUser = mysqli_query($con, $queryGetUser);

if ($resultGetUser) {
    $userData = mysqli_fetch_assoc($resultGetUser);
} else {
    echo "Error: " . mysqli_error($con);
}

$successMessage = ''; // Tambahkan variabel untuk pesan keberhasilan

// Mendapatkan data pengguna yang sedang login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $no_nik = $_POST['no_nik'];
    $nama = $_POST['nama'];
    $no_rumah = $_POST['no_rumah'];
    $no_telepon = $_POST['no_telepon'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $no_kk = $_POST['no_kk'];
    $agama = $_POST['agama'];
    $pekerjaan = $_POST['pekerjaan'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $password = $_POST['password'];

    // Query untuk mengupdate data profil pengguna
    $query = "UPDATE user SET no_nik='$no_nik', nama='$nama', no_rumah='$no_rumah', no_telepon='$no_telepon', jenis_kelamin='$jenis_kelamin', no_kk='$no_kk', agama='$agama', pekerjaan='$pekerjaan', tempat_lahir='$tempat_lahir', tanggal_lahir='$tanggal_lahir', password='$password' WHERE user_id='$user_id'";

    // Jalankan query
    if (mysqli_query($con, $query)) {
        // Jika berhasil, alihkan kembali ke halaman profil dengan pesan keberhasilan
        header("Location: kelola_profil.php?status=success");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($con);
    }

    // Tutup koneksi ke database
    mysqli_close($con);
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
            margin-top: 100px; /* Add some margin for better separation */
        }
        
        .order {
            background-color: #f9f9f9; /* Add a light background color to the form */
            padding: 20px;
            border-radius: 10px; /* Add rounded corners for a modern look */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow effect */
            width: 70%; /* Adjust the width as needed */
            margin: 0 auto; /* Center the form on the page */
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
        
        <main>
            

            <div class="table-data">
            <!-- Tampilkan pesan keberhasilan jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success') : ?>
            <div class="success-message">Anda berhasil mengupdate data anda</div>
            <?php endif; ?>
                <div class="order">
                    <div class="head">
                        <h3>Data profil anda</h3>
                    </div>
        
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                        <label for="no_nik">NIK:</label>
                        <input type="text" name="no_nik" placeholder="NIK" value="<?php echo $userData['no_nik']; ?>"><br>

                        <label for="nama">Nama Kepala Keluarga:</label>
                        <input type="text" name="nama" placeholder="Nama Kepala Keluarga" value="<?php echo $userData['nama']; ?>"><br>

                        <label for="no_rumah">No Rumah:</label>
                        <input type="text" name="no_rumah" placeholder="No Rumah" value="<?php echo $userData['no_rumah']; ?>"><br>

                        <label for="no_telepon">No Telepon:</label>
                        <input type="text" name="no_telepon" placeholder="No Telepon" value="<?php echo $userData['no_telepon']; ?>"><br>

                        <label for="jenis_kelamin">Jenis kelamin:</label>
                        <input type="text" name="jenis_kelamin" placeholder="Jenis Kelamin" value="<?php echo $userData['jenis_kelamin']; ?>"><br>

                        <label for="no_kk">No KK:</label>
                        <input type="text" name="no_kk" placeholder="No KK" value="<?php echo $userData['no_kk']; ?>"><br>

                        <label for="agama">Agama:</label>
                        <input type="text" name="agama" placeholder="Agama" value="<?php echo $userData['agama']; ?>"><br>

                        <label for="pekerjaan">Pekerjaan:</label>
                        <input type="text" name="pekerjaan" placeholder="Pekerjaan" value="<?php echo $userData['pekerjaan']; ?>"><br>

                        <label for="tempat_lahir">Tempat lahir:</label>
                        <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" value="<?php echo $userData['tempat_lahir']; ?>"><br>

                        <label for="tanggal_lahir">Tanggal lahir:</label>
                        <input type="text" name="tanggal_lahir" placeholder="Tanggal Lahir" value="<?php echo $userData['tanggal_lahir']; ?>"><br>

                        <label for="password">Password:</label>
                        <input type="text" name="password" placeholder="Password" value="<?php echo $userData['password']; ?>"><br>

                        <input type="submit" name="submit" value="Update Data">     
                    </form>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>
