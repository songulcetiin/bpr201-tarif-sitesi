<?php 
include 'includes/header.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: index.php"); exit(); }
$recipe_id = $_GET['id'];
$my_id = $_SESSION['user_id'];

// Tarifi Çek
$stmt = $pdo->prepare("SELECT r.*, u.full_name, u.username, u.avatar 
                       FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();
if (!$recipe) { header("Location: index.php"); exit(); }

// Beğeni ve Kaydetme Kontrolü
$liked = $pdo->query("SELECT count(*) FROM likes WHERE user_id=$my_id AND recipe_id=$recipe_id")->fetchColumn();
$saved = $pdo->query("SELECT count(*) FROM saved_recipes WHERE user_id=$my_id AND recipe_id=$recipe_id")->fetchColumn();
$like_count = $pdo->query("SELECT count(*) FROM likes WHERE recipe_id=$recipe_id")->fetchColumn();

// Yorumları Çek
$comments_stmt = $pdo->prepare("SELECT c.*, u.full_name, u.username, u.avatar 
                                FROM comments c JOIN users u ON c.user_id = u.id 
                                WHERE c.recipe_id = ? ORDER BY c.created_at DESC");
$comments_stmt->execute([$recipe_id]);
$comments = $comments_stmt->fetchAll();
?>

<div class="container" style="max-width: 800px; margin-top: 20px;">
    <div class="card">
        <div class="post-header" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
            <a href="profil.php?kullanici=<?php echo $recipe['username']; ?>">
                <img src="uploads/<?php echo !empty($recipe['avatar']) ? $recipe['avatar'] : 'default.png'; ?>" class="avatar">
            </a>
            <div>
                <a href="profil.php?kullanici=<?php echo $recipe['username']; ?>" class="username" style="font-size: 16px;">
                    <?php echo htmlspecialchars($recipe['full_name']); ?>
                </a>
                <div style="color: #888; font-size: 13px;">@<?php echo $recipe['username']; ?></div>
            </div>
        </div>

        <h1 style="color: var(--primary); margin-bottom: 15px;"><?php echo htmlspecialchars($recipe['title']); ?></h1>

        <?php if($recipe['image']): ?>
            <img src="uploads/<?php echo $recipe['image']; ?>" style="width: 100%; border-radius: 10px; margin-bottom: 20px;">
        <?php endif; ?>

        <div style="display:flex; gap:20px; margin-bottom:20px; font-size:18px;">
            <a href="islem.php?begen=<?php echo $recipe_id; ?>" style="color: <?php echo $liked ? 'red' : 'gray'; ?>;">
                <i class="<?php echo $liked ? 'fas' : 'far'; ?> fa-heart"></i> <?php echo $like_count; ?> Beğeni
            </a>
            <a href="islem.php?kaydet=<?php echo $recipe_id; ?>" style="color: <?php echo $saved ? 'var(--primary)' : 'gray'; ?>;">
                <i class="<?php echo $saved ? 'fas' : 'far'; ?> fa-bookmark"></i> <?php echo $saved ? 'Kaydedildi' : 'Kaydet'; ?>
            </a>
        </div>

        <p style="font-size: 16px; margin-bottom: 20px;"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
        
        <?php if (!empty(trim($recipe['ingredients']))): ?>
        <div style="background: #fff5f5; padding: 20px; border-radius: 10px; border-left: 5px solid var(--primary); margin-bottom: 20px;">
            <h3 style="color: var(--primary); margin-bottom: 10px;">Malzemeler</h3>
            <p><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty(trim($recipe['instructions']))): ?>
        <div style="padding: 20px; border: 1px solid #eee; border-radius: 10px; margin-bottom:20px;">
            <h3 style="color: var(--primary); margin-bottom: 10px;">Hazırlanışı</h3>
            <p><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
        </div>
        <?php endif; ?>
        
        <h3 style="margin-bottom:15px; border-top:1px solid #eee; padding-top:20px;">Yorumlar (<?php echo count($comments); ?>)</h3>
        
        <form action="islem.php" method="POST" style="margin-bottom:30px; display:flex; gap:10px;">
            <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
            <input type="text" name="comment" class="form-control" placeholder="Yorum yaz..." required>
            <button type="submit" name="yorum_yap" class="btn-primary">Gönder</button>
        </form>

        <?php foreach($comments as $c): ?>
            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <a href="profil.php?kullanici=<?php echo $c['username']; ?>">
                    <img src="uploads/<?php echo !empty($c['avatar']) ? $c['avatar'] : 'default.png'; ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                </a>
                <div>
                    <div style="font-weight:bold;">
                        <a href="profil.php?kullanici=<?php echo $c['username']; ?>"><?php echo htmlspecialchars($c['full_name']); ?></a>
                        <small style="color:#999; margin-left:5px;"><?php echo date("d M H:i", strtotime($c['created_at'])); ?></small>
                    </div>
                    <p style="margin-top:2px;"><?php echo htmlspecialchars($c['comment']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>