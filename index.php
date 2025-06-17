<?php
session_start();

// Konfigurasi database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "user";

// Membuat koneksi
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Ambil data user dari database
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password menggunakan password_verify()
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="wrapper">
    <form method="POST" action="">
        <h1>Login</h1>
        <div class="input-box">
            <input type="text" name="username" placeholder="Username" required>
            <i class="bx bxs-user"></i>
        </div>
        <div class="input-box">
            <input type="password" name="password" placeholder="Password" required>
            <i class="bx bxs-lock-alt"></i>
        </div>

        <div class="remember-forgot">
            <label><input type="checkbox"> Ingat aku</label>
            <a href="lupa.php">Lupa password?</a>
        </div>

        <button type="submit" name="login" class="btn">Login</button>

        <div class="register-link">
            <p>Belum punya akun? <a href="Daftar.php">Daftar 🥺</a></p>
        </div>
    </form>
</div>

</body>
</html>
