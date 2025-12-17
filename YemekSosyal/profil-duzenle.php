<?php 
require 'includes/header.php'; 

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$messageType = ""; // success veya error

// Mevcut bilgileri çek
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Form Gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = htmlspecialchars($_POST['full_name']);
    $username = htmlspecialchars($_POST['username']);
    $bio = htmlspecialchars($_POST['bio']);
    
    // Resim Yükleme İşlemi
    $new_avatar = $user['avatar']; // Varsayılan olarak eskisi kalsın
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], "uploads/" . $filename)) {
                $new_avatar = $filename;
                // İsteğe bağlı: Eski resmi silebilirsin (unlink)
            }
        } else {
            $message = "Sadece JPG, PNG ve GIF yükleyebilirsiniz.";
            $messageType = "error";
        }
    }

    if (empty($message)) {
        try {
            $sql = "UPDATE users SET full_name=?, username=?, bio=?, avatar=? WHERE id=?";
            $update = $pdo->prepare($sql);
            $update->execute([$full_name, $username, $bio, $new_avatar, $user_id]);

            // Session bilgilerini güncelle (isim değişmiş olabilir)
            $_SESSION['full_name'] = $full_name;
            $_SESSION['username'] = $username;

            // Bilgileri ekrana yansıtmak için user dizisini de güncelle
            $user['full_name'] = $full_name;
            $user['username'] = $username;
            $user['bio'] = $bio;
            $user['avatar'] = $new_avatar;

            $message = "Profiliniz başarıyla güncellendi!";
            $messageType = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Bu kullanıcı adı başkası tarafından kullanılıyor.";
            } else {
                $message = "Hata oluştu: " . $e->getMessage();
            }
            $messageType = "error";
        }
    }
}
?>

<div style="max-width: 600px; margin: 20px auto;">
    <div class="card">
        <h2 style="color:var(--primary); margin-bottom:20px; text-align:center;">Profili Düzenle</h2>

        <?php if($message): ?>
            <div style="padding:10px; margin-bottom:15px; border-radius:5px; text-align:center; 
                background-color: <?php echo $messageType == 'success' ? '#d4edda' : '#f8d7da'; ?>;
                color: <?php echo $messageType == 'success' ? '#155724' : '#721c24'; ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div style="text-align:center; margin-bottom:20px;">
                <?php 
                    $imgUrl = 'uploads/' . (!empty($user['avatar']) ? $user['avatar'] : 'default.png');
                    if (!file_exists('uploads/' . $user['avatar'])) { $imgUrl = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; }
                ?>
                <img src="<?php echo $imgUrl; ?>" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid #ddd;">
                <br>
                <label for="file-upload" style="cursor:pointer; color:var(--primary); font-size:14px; font-weight:bold;">
                    <i class="fas fa-camera"></i> Fotoğrafı Değiştir
                </label>
                <input id="file-upload" type="file" name="avatar" style="display:none;">
            </div>

            <div class="form-group">
                <label>Ad Soyad</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label>Hakkımda (Bio)</label>
                <textarea name="bio" class="form-control" maxlength="250" placeholder="Kendinden kısaca bahset..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                <small style="color:#999;">Maksimum 250 karakter.</small>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn-primary" style="flex:1;">Kaydet</button>
                <a href="profil.php" class="btn-primary" style="background:#ccc; color:#333; text-align:center; padding:10px 20px;">İptal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>