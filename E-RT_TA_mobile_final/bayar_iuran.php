<?php
include('database.php');
session_start();

// Misalkan nama pengguna disimpan dalam session dengan kunci 'username'
if (isset($_SESSION['nama'])) {
    $nama = $_SESSION['nama'];
} else {
    $nama = 'nama'; // Nilai default jika pengguna tidak teridentifikasi
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = 'user_id'; // Nilai default jika pengguna tidak teridentifikasi
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login_warga.php");
    exit();
}

// Jika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $bayaran_wajib = $_POST['bayaran_wajib'];
    $bayaran_sukarela = $_POST['bayaran_sukarela'];

    // Cek apakah pembayaran sudah ada untuk bulan dan tahun yang dipilih dengan status 'lunas' atau 'diproses'
    $checkQuery = "SELECT * FROM pembayaran_iuran WHERE user_id = '$user_id' AND bulan = '$bulan' AND tahun = '$tahun' AND (status = 'lunas' OR status = 'diproses')";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $errorMessage = "Anda sudah membayar iuran bulan $bulan tahun $tahun.";
    } else {
        // Proses upload bukti bayar
        $uploadDir = 'uploads/';  // Lokasi folder untuk menyimpan bukti bayar
        $uploadFile = $uploadDir . basename($_FILES['bukti_bayar']['name']);

        if (move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $uploadFile)) {
            // Simpan data ke database
            $query = "INSERT INTO pembayaran_iuran (nama_kk, user_id, tanggal, bulan, tahun, bayaran_wajib, bayaran_sukarela, bukti_bayar, status) 
                      VALUES ('$nama', '$user_id', '$tanggal', '$bulan', '$tahun', '$bayaran_wajib', '$bayaran_sukarela', '$uploadFile', 'diproses')";

            $result = mysqli_query($con, $query);

            if ($result) {
                $successMessage = "Selamat! Anda berhasil melakukan pembayaran iuran, silakan cek status pembayaran iuran Anda di menu riwayat pembayaran iuran.";
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "Gagal mengunggah bukti bayar.";
        }
    }
}

$tanggal_sekarang = date("Y-m-d");
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

        .error-message {
            background-color: #f44336;
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
    <script>
        // script copas no.va
        function copyText() {
            // Get the text to copy
            var copyText = document.getElementById("copyText");

            // Create a range and select the text
            var range = document.createRange();
            range.selectNode(copyText);

            // Select the text
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);

            // Execute the copy command
            document.execCommand("copy");

            // Deselect the text
            window.getSelection().removeAllRanges();

            // Provide some feedback to the user
            alert("Text copied to clipboard: " + copyText.innerText);
        }
    </script>

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

            <!-- Tampilkan pesan kesalahan jika ada -->
            <?php if (!empty($errorMessage)) : ?>
                <div class="error-message"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            <div class="order">
                <ul class="box-info">
                <li>
                    <span class="text">
                        <h3>Prosedur Pembayaran Iuran</h3>
                        <br>
                        <p>1. lakukan pembayaran iuran melalui Virtual Account dibawah menggunakan m-banking anda masing-masing.</p>
                        <br>
                        <p>Bank:  Mandiri</p>
                        <p>Nama:  Fitriani Andriani (bendahara)</p>
                        <p>No.Virtual Account:
                            <div id="copyText" contenteditable="true">
                                <p>1360032739523</p>
                            </div>
                        </p>
                        <button id="copyButton" onclick="copyText()">Copy No Virtual Account</button><br>
                        <br>
                        <p>(JANGAN LUPA SCREENSHOT BUKTI BAYAR/TRANSFER)</p>
                        <br>
                        <p>2. kemudian isi form di bawah ini sesuai dengan jumlah yang sudah anda bayar/transfer sebelumnya serta masukkan juga bukti bayar/transfer.</p>
                    </span>
                </li>
            </ul>
            </div>
        </div>

            <div class="table-data2">
                <div class="order">
                    <div class="head">
                        <h3>Form Pembayaran Iuran</h3>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <label for="nama">Nama Kepala Keluarga:</label>
                        <input type="text" name="nama" placeholder="Nama" value="<?php echo htmlspecialchars($nama); ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
                        <label for="tanggal">Tanggal:</label>
                        <input type="text" id="tanggal" name="tanggal" value="<?php echo $tanggal_sekarang; ?>" readonly style="background-color: #888888; color: #ffffff;"><br>
                        <label for="bulan">Iuran Bulan:</label>
                        <select name="bulan" required>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select><br>
                        <label for="tahun">Iuran Tahun/Periode:</label>
                        <select name="tahun" required>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                        </select><br>
                        <label for="bayaran_wajib">Bayaran wajib:</label>
                        <input type="number" name="bayaran_wajib" placeholder="contoh: 200.000" required><br>
                        <label for="bayaran_sukarela">Bayaran sukarela:</label>
                        <input type="number" name="bayaran_sukarela" placeholder="jika tidak bayar, tulis 0"><br>
                        <label for="bukti_bayar">Bukti Bayar/Transfer (Gambar):</label>
                        <input type="file" name="bukti_bayar" required accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"><br>
                        <input type="submit" name="submit" value="Bayar iuran">
                    </form>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
<!--===== SCROLL REVEAL =====-->
<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
</body>
</html>
