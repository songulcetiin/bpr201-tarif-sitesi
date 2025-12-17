<?php
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- BEĞENİ İŞLEMİ ---
if (isset($_GET['begen'])) {
    $recipe_id = $_GET['begen'];
    // Daha önce beğenmiş mi?
    $check = $pdo->prepare("SELECT * FROM likes WHERE user_id=? AND recipe_id=?");
    $check->execute([$user_id, $recipe_id]);
    
    if ($check->rowCount() > 0) {
        // Beğeniyi Kaldır
        $pdo->prepare("DELETE FROM likes WHERE user_id=? AND recipe_id=?")->execute([$user_id, $recipe_id]);
    } else {
        // Beğeni Ekle
        $pdo->prepare("INSERT INTO likes (user_id, recipe_id) VALUES (?,?)")->execute([$user_id, $recipe_id]);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']); // Geldiği sayfaya geri dön
    exit();
}

// --- KAYDETME İŞLEMİ ---
if (isset($_GET['kaydet'])) {
    $recipe_id = $_GET['kaydet'];
    $check = $pdo->prepare("SELECT * FROM saved_recipes WHERE user_id=? AND recipe_id=?");
    $check->execute([$user_id, $recipe_id]);
    
    if ($check->rowCount() > 0) {
        $pdo->prepare("DELETE FROM saved_recipes WHERE user_id=? AND recipe_id=?")->execute([$user_id, $recipe_id]);
    } else {
        $pdo->prepare("INSERT INTO saved_recipes (user_id, recipe_id) VALUES (?,?)")->execute([$user_id, $recipe_id]);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// --- TAKİP İŞLEMİ ---
if (isset($_GET['takip'])) {
    $target_id = $_GET['takip']; // Kimi takip edeceğiz?
    
    if ($target_id != $user_id) { // Kendini takip edemezsin
        $check = $pdo->prepare("SELECT * FROM follows WHERE follower_id=? AND following_id=?");
        $check->execute([$user_id, $target_id]);
        
        if ($check->rowCount() > 0) {
            // Takipten Çık
            $pdo->prepare("DELETE FROM follows WHERE follower_id=? AND following_id=?")->execute([$user_id, $target_id]);
        } else {
            // Takip Et
            $pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?,?)")->execute([$user_id, $target_id]);
        }
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// --- YORUM YAPMA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yorum_yap'])) {
    $recipe_id = $_POST['recipe_id'];
    $comment = htmlspecialchars($_POST['comment']);
    
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment) VALUES (?,?,?)");
        $stmt->execute([$user_id, $recipe_id, $comment]);
    }
    header("Location: tarif-detay.php?id=" . $recipe_id);
    exit();
}

// --- TARİF SİLME İŞLEMİ (Hem Kullanıcı Hem Admin İçin) ---
if (isset($_GET['sil_tarif'])) {
    $r_id = $_GET['sil_tarif'];
    
    // Tarifi kimin yazdığını öğrenelim
    $stmt = $pdo->prepare("SELECT user_id FROM recipes WHERE id = ?");
    $stmt->execute([$r_id]);
    $recipe = $stmt->fetch();

    if ($recipe) {
        // Eğer tarifi yazan kişiysem VEYA Admin/Manager isem silebilirim
        if ($recipe['user_id'] == $user_id || $_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager') {
            $pdo->prepare("DELETE FROM recipes WHERE id = ?")->execute([$r_id]);
        }
    }
    
    // Geldiği yere geri gönder (Admin paneli veya Profil)
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>