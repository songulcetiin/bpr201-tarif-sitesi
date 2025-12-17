<?php 
require 'includes/db.php'; 

// Giriş yapmışsa buraya girmesin
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Bu mailde biri var mı?
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 6 haneli kod üret
        $code = rand(100000, 999999);

        // Kodu veritabanına kaydet (Daha önceki verification_code sütununu kullanıyoruz)
        $update = $pdo->prepare("UPDATE users SET verification_code = ? WHERE id = ?");
        $update->execute([$code, $user['id']]);

        // *** TXT Dosyasına Yaz (Mail Simülasyonu) ***
        $log = "Tarih: " . date("Y-m-d H:i:s") . " | Email: $email | SIFIRLAMA KODU: $code \n";
        file_put_contents('sifre_sifirlama.txt', $log, FILE_APPEND);

        // Şifre yenileme sayfasına yönlendir (Maili de taşıyoruz)
        header("Location: reset-password.php?email=" . $email);
        exit();
    } else {
        $message = "Bu e-posta adresiyle kayıtlı bir kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifremi Unuttum</title>
    <link rel="stylesheet" href="css/style.css">
    <style> body { display:flex; justify-content:center; align-items:center; height:100vh; } .auth-box { width:400px; padding:30px; } </style>
</head>
<body>
    <div class="card auth-box">
        <h2 style="text-align:center; color:var(--primary);">Şifremi Unuttum</h2>
        <p style="text-align:center; color:#666; font-size:14px;">E-posta adresinizi girin, size bir sıfırlama kodu gönderelim.</p>
        
        <?php if($message) echo "<p style='color:red; text-align:center;'>$message</p>"; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Kod Gönder</button>
        </form>
        <p style="text-align:center; margin-top:15px;"><a href="login.php">Girişe Dön</a></p>
    </div>
</body>
</html>