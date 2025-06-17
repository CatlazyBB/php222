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

// Proses ubah password
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    
    // Validasi input
    if (empty($username) || empty($old_password) || empty($new_password)) {
        $error = "Semua field harus diisi!";
    } else {
        // Cek apakah username ada dan password lama benar
        $check_sql = "SELECT id, password FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows == 0) {
            $error = "Username tidak ditemukan!";
        } else {
            $user = $result->fetch_assoc();
            
            // Verifikasi password lama
            if (password_verify($old_password, $user['password'])) {
                // Hash password baru
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_new_password, $user['id']);
                
                if ($update_stmt->execute()) {
                    $success = "Password berhasil diubah!";
                } else {
                    $error = "Terjadi kesalahan saat mengubah password!";
                }
                $update_stmt->close();
            } else {
                $error = "Password lama tidak benar!";
            }
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
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Ubah Password</title>
</head>

<body>
    <div class="wrapper">
        <form method="POST" action="">
            <h1>Ubah Password</h1>
            
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
                <input type="password" name="old_password" placeholder="Password Lama" required>
                <box-icon name='lock-alt'></box-icon>
            </div>
            
            <div class="input-box">
                <input type="password" name="new_password" placeholder="Password Baru" required>
                <box-icon name='lock'></box-icon>
            </div>

            <button type="submit" class="btn">Ubah Password</button>

            <div class="register-link">
                <p>Gunakan password yang terdiri dari angka dan huruf</p>
                <p><a href="index.php">Silahkan Login</a></p>
            </div>
        </form>
    </div>
</body>

</html>
