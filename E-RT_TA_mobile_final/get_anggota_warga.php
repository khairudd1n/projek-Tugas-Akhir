<?php
include('database.php');

if(isset($_GET['nama'])) {
    $nama = $_GET['nama'];

    // Ambil data anggota keluarga berdasarkan nama kepala keluarga
    $sql = "SELECT * FROM anggota_keluarga WHERE nama='$nama'";
    $result = $con->query($sql);

    $anggotaWarga = array();

    // Memeriksa apakah ada anggota keluarga dengan nama kepala keluarga yang diberikan
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $anggotaWarga[] = $row;
        }
    }

    // Mengembalikan data anggota keluarga dalam format JSON
    echo json_encode($anggotaWarga);
} else {
    echo "Nama kepala keluarga tidak ditemukan.";
}
?>
