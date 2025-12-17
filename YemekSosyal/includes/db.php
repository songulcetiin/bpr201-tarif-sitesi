<?php
// Sayfa yönlendirme hatalarını önlemek için çıktı tamponlamayı başlat
ob_start();

$host = 'localhost';
$dbname = 'yemeksosyal'; // Veritabanı adının bu olduğundan emin ol
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Oturumu güvenli bir şekilde başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// DİKKAT: Eski kodda burada olan "Test kullanıcısı simülasyonu" kodlarını SİLDİK.
// Artık sistem otomatik giriş yapmayacak, login.php üzerinden giriş beklenecek.
?>