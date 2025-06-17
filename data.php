<?php
$hostname = "sql102.infinityfree.com";
$username = "if0_39242257";
$password = "pXXQHVGkQsG2k43";
$database = "if0_39242257_loginform"; // ganti XXX dengan nama database yang sesuai
$port = 3306;

$conn = mysqli_connect($hostname, $username, $password, $database, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
