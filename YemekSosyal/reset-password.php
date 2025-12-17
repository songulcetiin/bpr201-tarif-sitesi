<?php 
require 'includes/db.php'; 

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$message = "";
$email_from_url = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $new_pass = $_POST['new_password'];

    // E-posta ve Kod Eşleşiyor mu?
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if ($user) {
        // Yeni şifreyi hashle
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

        // Şifreyi güncelle ve kodu sil (tek kullanımlık olsun)
        $update = $pdo->prepare("UPDATE users SET password = ?, verification_code = NULL WHERE id = ?");
        $update->execute([$hashed_password, $user['id']]);

        echo "<script>alert('Şifreniz başarıyla değiştirildi! Yeni şifrenizle giriş yapabilirsiniz.'); window.location.href='login.php';</script>";
        exit();
    } else {
        $message = "Hatalı kod veya e-posta adresi!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Şifre Belirle</title>
    <link rel="stylesheet" href="css/style.css">
    <style> body { display:flex; justify-content:center; align-items:center; height:100vh; } .auth-box { width:400px; padding:30px; } </style>
</head>
<body>
    <div class="card auth-box">
        <h2 style="text-align:center; color:var(--primary);">Yeni Şifre</h2>
        
        <?php if($message) echo "<p style='color:red; text-align:center;'>$message</p>"; ?>
        <p style="font-size:12px; color:gray; text-align:center;">(İpucu: <b>sifre_sifirlama.txt</b> dosyasına bak)</p>

        <form action="" method="POST">
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email_from_url; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Doğrulama Kodu</label>
                <input type="text" name="code" class="form-control" placeholder="6 haneli kod" maxlength="6" required>
            </div>

            <div class="form-group">
                <label>Yeni Şifre</label>
                <input type="password" name="new_password" class="form-control" placeholder="Yeni şifrenizi girin" required>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;">Şifreyi Güncelle</button>
        </form>
    </div>
</body>
</html>