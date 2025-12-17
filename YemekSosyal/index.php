<?php 
require_once 'includes/db.php'; 
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'includes/header.php'; 

$uid = $_SESSION['user_id'];
$search_mode = false;
$search_term = "";

// ARAMA YAPILDI MI?
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search_mode = true;
    $q = trim($_GET['q']);
    $search_term = "%" . $q . "%"; 

    // 1. KULLANICILARI ARA
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE full_name LIKE ? OR username LIKE ?");
    $user_stmt->execute([$search_term, $search_term]);
    $found_users = $user_stmt->fetchAll();

    // 2. TARİFLERİ ARA
    $recipe_stmt = $pdo->prepare("SELECT r.*, u.username, u.full_name, u.avatar 
                                  FROM recipes r JOIN users u ON r.user_id = u.id 
                                  WHERE r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?
                                  ORDER BY r.created_at DESC");
    $recipe_stmt->execute([$search_term, $search_term, $search_term]);
    $stmt = $recipe_stmt; 

} else {
    // NORMAL AKIŞ (Arama yoksa)
    $stmt = $pdo->query("SELECT r.*, u.username, u.full_name, u.avatar FROM recipes r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
}

// *** SAĞ MENÜ İÇİN POPÜLER ŞEFLER SORGUSU ***
// En çok tarif paylaşan 7 kullanıcıyı çekiyoruz
$pop_chef_sql = "SELECT u.*, COUNT(r.id) as tarif_sayisi 
                 FROM users u 
                 LEFT JOIN recipes r ON u.id = r.user_id 
                 GROUP BY u.id 
                 HAVING tarif_sayisi > 0 
                 ORDER BY tarif_sayisi DESC 
                 LIMIT 7";
$popular_chefs = $pdo->query($pop_chef_sql)->fetchAll();
?>

<div class="main-layout">
    <aside class="left-sidebar">
        <a href="kaydedilenler.php" class="card" style="display:block; text-decoration:none; color:inherit;">
            <h3 class="sidebar-title" style="color:var(--primary);"><i class="fas fa-bookmark"></i> Kaydedilenler</h3>
            <p style="color:#777; font-size:13px;">Favori tariflerin burada.</p>
        </a>
    </aside>

    <main class="feed">
        
        <?php if ($search_mode): ?>
            <h2 style="margin-bottom:20px; color:#555;">
                "<span style="color:var(--primary);"><?php echo htmlspecialchars($q); ?></span>" için arama sonuçları:
            </h2>

            <?php if (count($found_users) > 0): ?>
                <div class="card" style="margin-bottom:20px;">
                    <h4 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:5px;">Kullanıcılar</h4>
                    <?php foreach($found_users as $fu): ?>
                        <a href="profil.php?kullanici=<?php echo $fu['username']; ?>" class="user-result-card" style="text-decoration:none; color:inherit;">
                            <img src="uploads/<?php echo !empty($fu['avatar']) ? $fu['avatar'] : 'default.png'; ?>" 
                                 style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                            <div>
                                <div style="font-weight:bold;"><?php echo htmlspecialchars($fu['full_name']); ?></div>
                                <div style="font-size:12px; color:#888;">@<?php echo htmlspecialchars($fu['username']); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        while ($row = $stmt->fetch()) {
            $pid = $row['id'];
            // Basitlik adına sorguları döngü içinde yapıyoruz
            $is_liked = $pdo->query("SELECT count(*) FROM likes WHERE user_id=$uid AND recipe_id=$pid")->fetchColumn();
            $is_saved = $pdo->query("SELECT count(*) FROM saved_recipes WHERE user_id=$uid AND recipe_id=$pid")->fetchColumn();
            $like_count = $pdo->query("SELECT count(*) FROM likes WHERE recipe_id=$pid")->fetchColumn();
        ?>
            <div class="card">
                <div class="post-header">
                    <a href="profil.php?kullanici=<?php echo $row['username']; ?>">
                        <img src="uploads/<?php echo !empty($row['avatar']) ? $row['avatar'] : 'default.png'; ?>" class="avatar">
                    </a>
                    <div>
                        <div class="username">
                            <a href="profil.php?kullanici=<?php echo $row['username']; ?>">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                            </a>
                            <span style="font-weight:normal; color:#888; font-size:13px;">@<?php echo htmlspecialchars($row['username']); ?></span>
                        </div>
                        <div class="time"><?php echo date("d M Y, H:i", strtotime($row['created_at'])); ?></div>
                    </div>
                </div>

                <h2 class="post-title">
                    <a href="tarif-detay.php?id=<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                </h2>

                <?php if(!empty($row['image'])): ?>
                    <a href="tarif-detay.php?id=<?php echo $row['id']; ?>">
                        <img src="uploads/<?php echo $row['image']; ?>" class="post-image" alt="Tarif Resmi">
                    </a>
                <?php endif; ?>

                <div class="post-content">
                    <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                </div>

                <div style="margin-top:15px; display:flex; gap:20px; color:gray; border-top: 1px solid #eee; padding-top: 15px;">
                    <a href="islem.php?begen=<?php echo $pid; ?>" style="text-decoration:none; color: <?php echo $is_liked ? 'red' : 'inherit'; ?>;">
                        <i class="<?php echo $is_liked ? 'fas' : 'far'; ?> fa-heart"></i> 
                        <?php echo $like_count > 0 ? $like_count . ' Beğeni' : 'Beğen'; ?>
                    </a>
                    <a href="tarif-detay.php?id=<?php echo $pid; ?>" style="text-decoration:none; color:inherit;">
                        <i class="far fa-comment"></i> Yorum Yap
                    </a>
                    <a href="islem.php?kaydet=<?php echo $pid; ?>" style="color: <?php echo $is_saved ? 'var(--primary)' : 'inherit'; ?>; margin-left:auto;">
                        <i class="<?php echo $is_saved ? 'fas' : 'far'; ?> fa-bookmark"></i>
                    </a>
                </div>
            </div>
        <?php } ?>
        
        <?php if($stmt->rowCount() == 0): ?>
            <div class="card" style="text-align:center; padding:40px;">
                <i class="fas fa-search" style="font-size:40px; color:#ddd; margin-bottom:10px;"></i>
                <p style="color:gray;">
                    <?php echo $search_mode ? 'Aradığınız kriterlere uygun sonuç bulunamadı.' : 'Henüz hiç tarif yok.'; ?>
                </p>
                <?php if($search_mode): ?>
                    <a href="index.php" class="btn-primary" style="margin-top:10px; display:inline-block;">Tüm Tarifleri Gör</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <aside class="right-sidebar">
        <div class="card">
            <h3 class="sidebar-title" style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
                <i class="fas fa-crown" style="color:#ffd700;"></i> Popüler Şefler
            </h3>
            
            <ul class="sidebar-menu">
                <?php foreach($popular_chefs as $chef): ?>
                    <li style="margin-bottom: 15px;">
                        <a href="profil.php?kullanici=<?php echo $chef['username']; ?>" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                            <img src="uploads/<?php echo !empty($chef['avatar']) ? $chef['avatar'] : 'default.png'; ?>" 
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #eee;">
                            
                            <div style="flex: 1;">
                                <div style="font-weight: bold; font-size: 14px; color: var(--text-dark);">
                                    <?php echo htmlspecialchars($chef['full_name']); ?>
                                </div>
                                <div style="font-size: 12px; color: #888;">
                                    @<?php echo htmlspecialchars($chef['username']); ?>
                                </div>
                            </div>
                            
                            <span style="background: var(--bg-color); padding: 2px 8px; border-radius: 10px; font-size: 11px; color: var(--text-grey); font-weight: bold;">
                                <?php echo $chef['tarif_sayisi']; ?> Tarif
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>

                <?php if(count($popular_chefs) == 0): ?>
                    <li style="color: #999; font-size: 13px; text-align: center;">Henüz popüler bir şef yok.</li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>
</div>

</body>
</html>