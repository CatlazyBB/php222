<?php
// Konfigurasi database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "user";

// Membuat koneksi
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses pendaftaran
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Cek apakah username sudah ada
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lay/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Daftar</title>
</head>

<body>
    <div class="wrapper">
        <form method="POST" action="">
            <h1>Daftar</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message" style="color: red; margin-bottom: 15px; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success-message" style="color: green; margin-bottom: 15px; text-align: center;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <box-icon name='user'></box-icon>
            </div>
            
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <box-icon name='lock-alt'></box-icon>
            </div>

            <button type="submit" class="btn">Daftar</button>

            <div class="register-link">
                <p>Sudah punya akun? <a href="index.php">Login</a></p>
            </div>
        </form>
    </div>
</body>

</html>