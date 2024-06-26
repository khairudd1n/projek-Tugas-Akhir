<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anggota_keluarga_id = $_POST['anggota_keluarga_id'];
    $status_hubungan = $_POST['status_hubungan'];
    $nama_anggota_keluarga = $_POST['nama_anggota_keluarga'];
    $no_nik = $_POST['no_nik'];
    $no_telepon = $_POST['no_telepon'];
    $no_kk = $_POST['no_kk'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $pekerjaan = $_POST['pekerjaan'];
    $agama = $_POST['agama'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];

    $sql = "UPDATE anggota_keluarga SET 
            status_hubungan = '$status_hubungan',
            nama_anggota_keluarga = '$nama_anggota_keluarga',
            no_nik = '$no_nik',
            no_telepon = '$no_telepon',
            no_kk = '$no_kk',
            jenis_kelamin = '$jenis_kelamin',
            pekerjaan = '$pekerjaan',
            agama = '$agama',
            tempat_lahir = '$tempat_lahir',
            tanggal_lahir = '$tanggal_lahir'
            WHERE anggota_keluarga_id = $anggota_keluarga_id";

    if (mysqli_query($con, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
    }
}
?>
