<?php

session_start();

include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login_rt.php");
    exit();
}

// Fungsi untuk menambahkan data ke database
function tambahData($tanggal, $deskripsi, $jenis, $jumlah, $bukti) {
    global $con;
    $sql = "INSERT INTO keuangan (tanggal, deskripsi, jenis, jumlah, bukti) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssds", $tanggal, $deskripsi, $jenis, $jumlah, $bukti);
    
    if ($stmt->execute()) {
        echo "Data berhasil ditambahkan.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fungsi untuk menghapus data dari database
function hapusData($id) {
    global $con;
    $sql = "DELETE FROM keuangan WHERE keuangan_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Data berhasil dihapus.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Ambil data dari form jika ada
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['hapus_id'])) {
        hapusData($_POST['hapus_id']);
    } else {
        $tanggal = $_POST["tanggal"];
        $deskripsi = $_POST["deskripsi"];
        $jenis = $_POST["jenis"];
        $jumlah = $_POST["jumlah"];

        // Proses upload file
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $bukti = "uploads/" . basename($_FILES["bukti"]["name"]);
            move_uploaded_file($_FILES["bukti"]["tmp_name"], $bukti);
        } else {
            $bukti = NULL;
        }

        tambahData($tanggal, $deskripsi, $jenis, $jumlah, $bukti);
    }
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
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <title>E-RT TAMPAK SIRING (BENDAHARA)</title>
    
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
            width: 300px;
            max-width: 80%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
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
        #popup input[type="number"],
        #popup input[type="file"] {
            width: calc(100% - 10px);
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
            background-color: red;
            margin-top: 10px;
        }
        .pemasukan {
            background-color: #c1e7c1;
        }
        .pengeluaran {
            background-color: #ffb3b3;
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
        .hapus {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            background-color: #ff4d4d;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .hapus:hover {
            background-color: #ff1a1a;
        }
        .head {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .head form {
            display: flex;
            align-items: center;
        }
        .head button#tambah {
            margin-left: auto; /* This will push the button to the right */
        }
        button {
            margin-left: 10px; /* Add margin between buttons */
        }
    </style>
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

    <section id="content">
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
                        <button id="tambah" onclick="togglePopup()">
                            <i class='bx bx-plus'></i>
                            Tambah Data
                        </button>
                    </div>

                    <div id="popup">
                        <h2>Input Data Keuangan</h2><br>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <label for="tanggal">Tanggal:</label>
                            <input type="date" name="tanggal" id="tanggal" required><br><br>
                            <label for="deskripsi">Deskripsi:</label>
                            <input type="text" name="deskripsi" id="deskripsi" required><br><br>
                            <label for="jenis">Jenis:</label>
                            <select name="jenis" id="jenis">
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select><br><br>
                            <label for="jumlah">Jumlah:</label>
                            <input type="number" name="jumlah" id="jumlah" required><br><br>
                            <label for="bukti">Bukti Transaksi:</label>
                            <input type="file" name="bukti" id="bukti" required><br><br>
                            <input type="submit" value="Tambahkan Data">
                        </form>
                        <button onclick="togglePopup()">Tutup</button>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $rowClass = $row["jenis"] == "pemasukan" ? "pemasukan" : "pengeluaran";
                                    echo "<tr class='$rowClass'><td>" . $row["keuangan_id"] . "</td><td>" . $row["tanggal"] . "</td><td>" . $row["deskripsi"] . "</td><td><a href='" . $row["bukti"] . "'>Lihat Bukti</a></td><td>" . $row["jenis"] . "</td><td>" . $row["jumlah"] . "</td>";
                                    echo "<td><form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' onsubmit='return confirmDeletion();'><input type='hidden' name='hapus_id' value='" . $row["keuangan_id"] . "'><input class='hapus' type='submit' value='Hapus'></form></td></tr>";
                                    if ($row["jenis"] == "pemasukan") {
                                        $totalPemasukan += (int)$row["jumlah"];
                                    } else {
                                        $totalPengeluaran += (int)$row["jumlah"];
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='7'>0 results</td></tr>";
                            }
                            ?>
                            <tr>
                                <td>Total Pemasukan:</td>
                                <td>Rp.<?php echo $totalPemasukan; ?>.000</td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td>Total Pengeluaran:</td>
                                <td>Rp.<?php echo $totalPengeluaran; ?>.000</td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td>Total Saldo:</td>
                                <td>Rp.<?php echo $totalPemasukan - $totalPengeluaran; ?>.000</td>
                                <td colspan="5"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>    
            </div>
        </main>
    </section>

    <script>
        function togglePopup() {
            var popup = document.getElementById("popup");
            popup.style.display = (popup.style.display == "block") ? "none" : "block";
        }
        
        function refreshPage() {
            window.location.href = window.location.pathname;
        }

        function confirmDeletion() {
            return confirm("Apakah Anda yakin ingin menghapus data ini?");
        }
    </script>

<script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>
    
</body>
</html>
