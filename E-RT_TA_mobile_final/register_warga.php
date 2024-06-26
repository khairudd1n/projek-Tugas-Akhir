<?php
// Check if there is a session pop_up_message
session_start();
if (isset($_SESSION['pop_up_message'])) {
    echo '<script>
            window.onload = function() {
                var popUp = document.createElement("div");
                popUp.setAttribute("class", "success-pop-up");
                popUp.innerHTML = "' . $_SESSION['pop_up_message'] . '";
                document.body.appendChild(popUp);

                setTimeout(function() {
                    popUp.style.display = "none";
                }, 6000); // Adjust the time the pop-up stays visible (in milliseconds)
            }
          </script>';
    // Remove the session pop_up_message so it won't appear again after a page refresh
    unset($_SESSION['pop_up_message']);
}

// Database connection
$con = new mysqli("localhost", "root", "", "e-rt_ta");

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if form is submitted
if(isset($_POST['register'])){
    // Collect form data
    $nama = $_POST['nama'];
    $no_nik = $_POST['no_nik'];
    $no_rumah = $_POST['no_rumah'];
    $no_telepon = $_POST['no_telepon'];
    $password = $_POST['password'];
    $role = "kepala keluarga"; // Set default role to "kepala keluarga"

    // Insert user data into database
    $sql = "INSERT INTO user (nama, no_nik, no_rumah, no_telepon, password, role) VALUES ('$nama', '$no_nik', '$no_rumah', '$no_telepon', '$password', '$role')";

    if ($con->query($sql) === TRUE) {
        $_SESSION['pop_up_message'] = "Akun berhasil dibuat. Silakan login.";
        header("Location: login_warga.php"); // Redirect to login page after successful registration
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Warga</title>
    <link rel="shortcut icon" type="x-icon" href="gambar/logoviladago.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="login_reg style.css">
    <style>
        body {
            background-image: url('/gambar/bg.jpg'); /* Specify the path to your background image */
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: antiquewhite;
        }
        
        .success-pop-up {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #4CAF50; /* Green background color */
        color: white;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        display: block;
        z-index: 999;
    }
    form {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Shadow effect */
            width: 300px; /* Adjust width as needed */
        }

        form h2 {
            text-align: center;
            color: #333; /* Dark gray text color */
        }

        form label {
            font-weight: bold;
            color: #555; /* Medium gray text color */
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc; /* Light gray border */
            border-radius: 5px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth transition for background color */
        }

        form input[type="submit"]:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        form p {
            text-align: center;
            margin-top: 15px;
        }

        form p a {
            color: #007bff; /* Blue link color */
            text-decoration: none;
        }

        form p a:hover {
            text-decoration: underline; /* Underline on hover */
        }

    </style>
</head>
<body>
    <form action="" method="post">
        <img src="gambar/logoviladago.png" alt="Logo" width="100">
        <h2>Register</h2>
        <br>
        <label for="nama">Nama Kepala Keluarga:</label>
        <input type="text" id="nama" name="nama" required><br>

        <label for="no_nik">NIK:</label>
        <input type="text" id="no_nik" name="no_nik" required><br>

        <label for="no_rumah">No rumah:</label>
        <input type="text" id="no_rumah" name="no_rumah" required><br>

        <label for="no_telepon">No telepon:</label>
        <input type="text" id="no_telepon" name="no_telepon" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Register" name="register">

        <p>Sudah punya akun? <a href="login_warga.php">Login disini</a></p>
    </form>
</body>
</html>
