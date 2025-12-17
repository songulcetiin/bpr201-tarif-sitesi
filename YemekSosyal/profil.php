<?php 
include 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$my_id = $_SESSION['user_id']; // Oturum açan kişi

// Hangi profili görüntülüyoruz?
if (isset($_GET['kullanici'])) {
    $username = $_GET['kullanici'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
} else {
    // URL'de isim yoksa kendi profilim
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$my_id]);
    $user = $stmt->fetch();
}

if (!$user) { echo "<div class='container'>Kullanıcı bulunamadı.</div>"; exit(); }

$target_id = $user['id']; // Görüntülenen profilin ID'si

// İstatistikler
$tarif_sayisi = $pdo->query("SELECT COUNT(*) FROM recipes WHERE user_id = $target_id")->fetchColumn();
$takipci_sayisi = $pdo->query("SELECT COUNT(*) FROM follows WHERE following_id = $target_id")->fetchColumn();
$takip_edilen = $pdo->query("SELECT COUNT(*) FROM follows WHERE follower_id = $target_id")->fetchColumn();

// Ben bu kişiyi takip ediyor muyum?
$takip_kontrol = $pdo->prepare("SELECT * FROM follows WHERE follower_id=? AND following_id=?");
$takip_kontrol->execute([$my_id, $target_id]);
$is_following = $takip_kontrol->rowCount() > 0;

// Tarifleri Çek
$r_stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
$r_stmt->execute([$target_id]);
$my_recipes = $r_stmt->fetchAll();

// Profil Resmi Hazırla
$avatarPath = 'uploads/' . $user['avatar'];
if (empty($user['avatar']) || !file_exists($avatarPath)) { 
    $avatarPath = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; 
}
?>

<div style="max-width: 900px; margin: 20px auto;">
    <div class="card profile-header">
        <div style="display:flex; align-items:center; gap:30px; flex-wrap:wrap;">
            <img src="<?php echo $avatarPath; ?>" class="profile-avatar" style="border: 4px solid #fff;">
            
            <div style="text-align:left; flex:1;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h1 style="font-size:28px; margin:0;"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    
                    <?php if ($my_id == $target_id): ?>
    <a href="profil-duzenle.php" class="btn-primary" style="background:#eee; color:#333; padding:5px 15px; font-size:14px;">Profili Düzenle</a>
    
    <a href="kaydedilenler.php" class="btn-primary" style="background: #fff0f0; color: var(--primary); padding:5px 15px; font-size:14px; margin-left:10px;">
        <i class="fas fa-bookmark"></i> Kaydedilenler
    </a>
    <?php else: ?>
                        <a href="islem.php?takip=<?php echo $target_id; ?>" class="btn-primary" 
                           style="background: <?php echo $is_following ? '#ddd' : 'var(--primary)'; ?>; 
                                  color: <?php echo $is_following ? '#333' : '#fff'; ?>;">
                           <?php echo $is_following ? 'Takipten Çık' : 'Takip Et'; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <p style="color:#777;">@<?php echo htmlspecialchars($user['username']); ?></p>
                <?php if(!empty($user['bio'])): ?>
                    <p style="margin-top:10px; font-style:italic; color:#555;">"<?php echo htmlspecialchars($user['bio']); ?>"</p>
                <?php endif; ?>

                <div class="stats" style="justify-content: flex-start; gap: 40px; margin-top:15px;">
                    <div class="stat-item"><strong><?php echo $tarif_sayisi; ?></strong> Tarif</div>
                    <div class="stat-item"><strong><?php echo $takipci_sayisi; ?></strong> Takipçi</div>
                    <div class="stat-item"><strong><?php echo $takip_edilen; ?></strong> Takip</div>
                </div>
            </div>
        </div>
    </div>

    <h3 style="margin-top:20px; border-bottom:2px solid #ddd; padding-bottom:10px;">Paylaşılan Tarifler</h3>
    <div class="recipe-grid">
        <?php foreach($my_recipes as $recipe): ?>
            <a href="tarif-detay.php?id=<?php echo $recipe['id']; ?>" class="card" style="padding:0; overflow:hidden; display:block; color:inherit;">
                <?php if($recipe['image']): ?>
                    <img src="uploads/<?php echo $recipe['image']; ?>" class="grid-img">
                <?php else: ?>
                    <div style="height:150px; background:#eee; display:flex; align-items:center; justify-content:center;">Resim Yok</div>
                <?php endif; ?>
                <div style="padding:10px;">
                    <h4 style="font-size:14px;"><?php echo htmlspecialchars($recipe['title']); ?></h4>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>