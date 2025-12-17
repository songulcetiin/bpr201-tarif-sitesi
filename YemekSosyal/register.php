<?php 
require 'includes/db.php'; 

// *** EKLENEN KISIM ***
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']); // Yeni
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Resim Yükleme
    $avatar = "default.png";
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0){
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], "uploads/" . $filename);
        $avatar = $filename;
    }

    $verification_code = rand(100000, 999999);

    try {
        // SQL Güncellendi: username eklendi
        $sql = "INSERT INTO users (username, full_name, email, phone, password, avatar, verification_code, is_verified) VALUES (?,?,?,?,?,?,?,0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $full_name, $email, $phone, $password, $avatar, $verification_code]);

        $_SESSION['temp_user_id'] = $pdo->lastInsertId();

        // TXT Dosyasına Yaz
        $log_mesaji = "Kullanici: $username | Email: $email | KOD: $verification_code \n";
        file_put_contents('dogrulama_kodlari.txt', $log_mesaji, FILE_APPEND);

        header("Location: verify.php");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Bu kullanıcı adı veya e-posta zaten kullanılıyor!";
        } else {
            $message = "Hata: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="css/style.css">
    <style> body { display:flex; justify-content:center; align-items:center; height:100vh; } .auth-box { width:400px; padding:30px; } </style>
</head>
<body>
    <div class="card auth-box">
        <h2 style="text-align:center; color:var(--primary);">Kayıt Ol</h2>
        <?php if($message) echo "<p style='color:red; text-align:center;'>$message</p>"; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Kullanıcı Adı (Türkçe karakter kullanma)</label>
                <input type="text" name="username" class="form-control" placeholder="orn: gurme_ahmet" required>
            </div>
            <div class="form-group">
                <label>Ad Soyad</label>
                <input type="text" name="full_name" class="form-control" placeholder="Ahmet Yılmaz" required>
            </div>
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Profil Fotoğrafı</label>
                <input type="file" name="avatar" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Kayıt Ol</button>
        </form>
        <p style="text-align:center; margin-top:10px;">Zaten üye misin? <a href="login.php">Giriş Yap</a></p>
    </div>
</body>
</html>