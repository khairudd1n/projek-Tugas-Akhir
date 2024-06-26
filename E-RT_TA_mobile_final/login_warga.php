<?php
session_start();

include('database.php');



// Check if there is a session pop_up_message
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


 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM user WHERE nama='$nama' AND password='$password'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        if ($row['role'] === 'kepala keluarga') {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            header("Location: dashboard_warga.php");
            exit();
        } else {
            echo "Anda tidak memiliki akses. Hanya 'kepala keluarga' yang dapat login.";
        }
    } else {
        echo "Nama atau password salah.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Warga</title>
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
        <h2>Login</h2> 
        <br>
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Login" name="login">

            <p>Belum punya akun? <a href="register_warga.php">Buat akun kepala keluarga</a></p>
        </form>
    </div>
</body>
</html>
