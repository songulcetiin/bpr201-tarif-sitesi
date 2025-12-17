<?php 
require 'includes/db.php'; 

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: register.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $user_id = $_SESSION['temp_user_id'];

    // Kodu kontrol et
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND verification_code = ?");
    $stmt->execute([$user_id, $code]);
    $user = $stmt->fetch();

    if ($user) {
        // Doğrulama başarılı: Hesabı onayla
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
        $update->execute([$user_id]);

        // Oturumu gerçek kullanıcıya çevir
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        unset($_SESSION['temp_user_id']); // Geçici ID'yi sil

        echo "<script>alert('Hesabınız doğrulandı! Hoş geldiniz.'); window.location.href='index.php';</script>";
        exit();
    } else {
        $error = "Hatalı kod girdiniz! Lütfen dogrulama_kodlari.txt dosyasını kontrol edin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Doğrulama</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { display:flex; justify-content:center; align-items:center; height:100vh; }
        .auth-box { width:400px; padding:30px; text-align:center; }
    </style>
</head>
<body>
    <div class="card auth-box">
        <h2 style="color:var(--primary);">Hesabı Doğrula</h2>
        <p>Email ve telefonunuza gönderilen (simüle edilen) kodu giriniz.</p>
        <p style="font-size:12px; color:gray;">(İpucu: Proje klasöründeki <b>dogrulama_kodlari.txt</b> dosyasına bak)</p>
        
        <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>

        <form action="" method="POST">
            <div class="form-group">
                <input type="text" name="code" class="form-control" placeholder="6 Haneli Kod" style="text-align:center; font-size:20px; letter-spacing:5px;" maxlength="6" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Doğrula</button>
        </form>
    </div>
</body>
</html>