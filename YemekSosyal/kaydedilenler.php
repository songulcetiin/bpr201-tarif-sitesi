<?php 
include 'includes/header.php'; 

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kaydedilen tarifleri ve yazarlarını çekiyoruz (JOIN işlemi)
$sql = "SELECT r.*, u.full_name, u.avatar 
        FROM saved_recipes s 
        JOIN recipes r ON s.recipe_id = r.id 
        JOIN users u ON r.user_id = u.id 
        WHERE s.user_id = ? 
        ORDER BY s.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$saved_recipes = $stmt->fetchAll();
?>

<div style="max-width: 900px; margin: 20px auto;">
    
    <h2 style="color: var(--primary); border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-bookmark"></i> Kaydettiğim Tarifler
    </h2>

    <div class="recipe-grid">
        <?php foreach($saved_recipes as $recipe): ?>
            <a href="tarif-detay.php?id=<?php echo $recipe['id']; ?>" class="card" style="padding:0; overflow:hidden; display:block; color:inherit; position:relative;">
                
                <button onclick="location.href='islem.php?kaydet=<?php echo $recipe['id']; ?>'; return false;" 
                        style="position:absolute; top:10px; right:10px; background:white; border:none; border-radius:50%; width:30px; height:30px; cursor:pointer; box-shadow:0 2px 5px rgba(0,0,0,0.2);">
                    <i class="fas fa-bookmark" style="color:var(--primary);"></i>
                </button>

                <?php if($recipe['image']): ?>
                    <img src="uploads/<?php echo $recipe['image']; ?>" class="grid-img">
                <?php else: ?>
                    <div style="height:150px; background:#eee; display:flex; align-items:center; justify-content:center;">Resim Yok</div>
                <?php endif; ?>
                
                <div style="padding:10px;">
                    <h4 style="font-size:14px; margin-bottom:5px;"><?php echo htmlspecialchars($recipe['title']); ?></h4>
                    <small style="color:#888;">Yazar: <?php echo htmlspecialchars($recipe['full_name']); ?></small>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if(count($saved_recipes) == 0): ?>
        <div class="card" style="text-align:center; padding:40px;">
            <i class="far fa-bookmark" style="font-size:40px; color:#ddd; margin-bottom:10px;"></i>
            <p style="color:gray;">Henüz kaydedilen bir tarif yok.</p>
            <a href="index.php" class="btn-primary" style="margin-top:10px; display:inline-block;">Keşfetmeye Başla</a>
        </div>
    <?php endif; ?>

</div>
</body>
</html>