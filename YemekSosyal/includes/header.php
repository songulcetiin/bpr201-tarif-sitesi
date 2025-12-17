<?php
// Veritabanı bağlantısını çağır (Session burada başlar)
require_once 'includes/db.php';

// *** GÜVENLİK KONTROLÜ ***
// Eğer kullanıcı giriş yapmamışsa (user_id session'da yoksa)
if (!isset($_SESSION['user_id'])) {
    // Giriş sayfasına yönlendir
    header("Location: login.php");
    exit(); // Kodun geri kalanını okumasını engelle
}

// Oturum açan kullanıcının bilgilerini (Navbar için) çekelim
// Bu sayede her sayfada tekrar tekrar sorgu yazmak zorunda kalmayız
$global_user_id = $_SESSION['user_id'];
$global_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$global_stmt->execute([$global_user_id]);
$global_user = $global_stmt->fetch();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YemekSosyal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header>
    <div class="container navbar">
        <a href="index.php" class="logo">
            <i class="fas fa-utensils"></i> YemekSosyal
        </a>
        <div class="search-box">
            <form action="index.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Tarif veya kullanıcı ara..." 
                       value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
        </div>
          <nav class="nav-links"> 
            <a href="tarif-ekle.php" style="color: var(--primary);"><i class="fas fa-plus-circle"></i> Tarif Ekle</a>
            
            <a href="profil.php">
                Profilim
            </a>

            
            
            <a href="logout.php" style="color:var(--primary); font-size:14px;"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </nav>
    </div>
</header>
<div class="container">