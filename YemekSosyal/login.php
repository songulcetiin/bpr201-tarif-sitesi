<?php 
require 'includes/db.php'; 

// Eğer zaten giriş yapmışsa ana sayfaya gönder
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen veriyi alıyoruz (Adı 'login_input' yaptık)
    $login_input = trim($_POST['login_input']);
    $password = $_POST['password'];

    // SQL GÜNCELLEMESİ: Hem email'e hem username'e bakıyoruz
    // "Email'i bu olan VEYA (OR) kullanıcı adı bu olan kişiyi getir"
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    
    // Soru işaretlerinin yerine değişkeni iki kere gönderiyoruz
    $stmt->execute([$login_input, $login_input]);
    
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Hesap doğrulanmış mı kontrolü
        if ($user['is_verified'] == 1) {
            // Oturum bilgilerini doldur
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role']; // Rolü de eklemeyi unutmuyoruz
            
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['temp_user_id'] = $user['id'];
            $error = "Hesabınız henüz doğrulanmamış. <a href='verify.php'>Doğrula</a>";
        }
    } else {
        $error = "Kullanıcı adı/E-posta veya şifre hatalı.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="css/style.css">
    <style> body { display:flex; justify-content:center; align-items:center; height:100vh; } .auth-box { width:400px; padding:30px; } </style>
</head>
<body>
    <div class="card auth-box">
        <h2 style="text-align:center; color:var(--primary);">Giriş Yap</h2>
        
        <?php if($error) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>Kullanıcı Adı veya E-posta</label>
                <input type="text" name="login_input" class="form-control" placeholder="Örn: gurme_ayse veya ayse@mail.com" required>
            </div>
            
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%;">Giriş Yap</button>
            <div style="text-align:right; margin-bottom:15px;">
    <a href="forgot-password.php" style="color:gray; font-size:13px;">Şifremi Unuttum?</a>
</div>
        </form>
        
        <p style="text-align:center; margin-top:10px;">Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
    </div>
</body>
</html>