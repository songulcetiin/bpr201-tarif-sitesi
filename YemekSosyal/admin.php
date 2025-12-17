<?php 
require_once 'includes/db.php'; 

// GÜVENLİK KONTROLÜ
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager')) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h2>Erişim Reddedildi!</h2>
            <p>Bu sayfayı görüntüleme yetkiniz yok.</p>
            <a href='index.php'>Ana Sayfaya Dön</a>
         </div>");
}

// --- AKTİF SEKME KONTROLÜ ---
// URL'den 'tab' verisini al, yoksa varsayılan olarak 'users' seç
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

// --- SİLME İŞLEMLERİ ---
// Silme işleminden sonra kullanıcıyı AYNI SEKMEYE geri gönderiyoruz

if(isset($_GET['sil_tarif'])){
    $id = $_GET['sil_tarif'];
    $pdo->prepare("DELETE FROM recipes WHERE id=?")->execute([$id]);
    header("Location: admin.php?tab=recipes&msg=tarif_silindi");
    exit();
}

if(isset($_GET['sil_user'])){
    $id = $_GET['sil_user'];
    if($id != $_SESSION['user_id']){
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }
    header("Location: admin.php?tab=users&msg=user_silindi");
    exit();
}

if(isset($_GET['sil_yorum'])){
    $id = $_GET['sil_yorum'];
    $pdo->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
    header("Location: admin.php?tab=comments&msg=yorum_silindi");
    exit();
}

// --- ARAMA TERİMLERİNİ AL ---
$q_user = isset($_GET['q_user']) ? trim($_GET['q_user']) : "";
$q_recipe = isset($_GET['q_recipe']) ? trim($_GET['q_recipe']) : "";
$q_comment = isset($_GET['q_comment']) ? trim($_GET['q_comment']) : "";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - YemekSosyal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; margin: 0; padding: 0; display: flex; flex-direction: column; height: 100vh; }
        
        /* HEADER */
        .admin-header {
            background: #fff; padding: 0 20px; height: 60px; border-bottom: 1px solid #ddd;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 10;
        }

        /* ANA YERLEŞİM (SIDEBAR + CONTENT) */
        .admin-wrapper {
            display: flex;
            flex: 1; /* Ekranın kalanını kapla */
            overflow: hidden; /* Taşmayı engelle */
        }

        /* SOL SIDEBAR */
        .admin-sidebar {
            width: 250px;
            background: #2c3e50;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        
        .sidebar-link {
            padding: 15px 20px;
            color: #bdc3c7;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-link:hover { background: #34495e; color: #fff; }
        
        .sidebar-link.active {
            background: #34495e;
            color: #fff;
            border-left-color: var(--primary);
        }

        /* SAĞ İÇERİK ALANI */
        .admin-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto; /* Sadece içerik kaydırılsın */
            background: #ecf0f1;
        }

        /* Tablolar ve Kartlar */
        .search-form { display: flex; gap: 10px; margin-bottom: 15px; }
        .search-input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; font-weight: 600; color: #555; }
        tr:hover { background-color: #f9f9f9; }
        .action-btn { padding: 5px 10px; border-radius: 4px; color: #fff; font-size: 12px; text-decoration: none; }
        .btn-delete { background-color: #e74c3c; }
        .btn-view { background-color: #3498db; }
        
        .section-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .section-header h2 { margin: 0; color: #333; }
    </style>
</head>
<body>

<div class="admin-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <h2 style="color:var(--primary); margin:0; font-size:20px;"><i class="fas fa-cogs"></i> Yönetim Paneli</h2>
        <span style="background:#333; color:#fff; padding:2px 8px; border-radius:4px; font-size:11px;">
            <?php echo strtoupper($_SESSION['role']); ?>
        </span>
    </div>
    <a href="index.php" class="btn-primary" style="text-decoration:none; padding: 8px 15px; font-size: 14px;">
        <i class="fas fa-arrow-left"></i> Siteye Dön
    </a>
</div>

<div class="admin-wrapper">
    
    <div class="admin-sidebar">
        <a href="?tab=users" class="sidebar-link <?php echo $active_tab == 'users' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Kullanıcılar
        </a>
        <a href="?tab=recipes" class="sidebar-link <?php echo $active_tab == 'recipes' ? 'active' : ''; ?>">
            <i class="fas fa-utensils"></i> Tarifler
        </a>
        <a href="?tab=comments" class="sidebar-link <?php echo $active_tab == 'comments' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Yorumlar
        </a>
    </div>

    <div class="admin-content">

        <?php if ($active_tab == 'users'): ?>
            <div class="section-header">
                <h2>Kullanıcı Yönetimi</h2>
            </div>
            
            <form action="" method="GET" class="search-form">
                <input type="hidden" name="tab" value="users"> <input type="text" name="q_user" class="search-input" placeholder="Kullanıcı adı veya isim ara..." value="<?php echo htmlspecialchars($q_user); ?>">
                <button type="submit" class="btn-primary">Ara</button>
                <?php if($q_user): ?><a href="admin.php?tab=users" class="btn-primary" style="background:#777; text-decoration:none;">Sıfırla</a><?php endif; ?>
            </form>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı</th>
                    <th>Rol</th>
                    <th>Kayıt Tarihi</th>
                    <th style="text-align:right;">İşlem</th>
                </tr>
                <?php
                $sql = "SELECT * FROM users";
                $params = [];
                if($q_user){
                    $sql .= " WHERE username LIKE ? OR full_name LIKE ?";
                    $params = ["%$q_user%", "%$q_user%"];
                }
                $sql .= " ORDER BY id DESC LIMIT 20";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                while($row = $stmt->fetch()){
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="uploads/<?php echo !empty($row['avatar']) ? $row['avatar'] : 'default.png'; ?>" style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                            <div>
                                <div><?php echo htmlspecialchars($row['full_name']); ?></div>
                                <small style="color:#888;">@<?php echo htmlspecialchars($row['username']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="padding:3px 8px; border-radius:4px; font-size:11px; color:#fff; background:<?php echo ($row['role']=='admin'?'#e74c3c':($row['role']=='manager'?'#2c3e50':'#95a5a6')); ?>">
                            <?php echo strtoupper($row['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date("d.m.Y", strtotime($row['created_at'])); ?></td>
                    <td style="text-align:right;">
                        <a href="profil.php?kullanici=<?php echo $row['username']; ?>" target="_blank" class="action-btn btn-view">Profil</a>
                        <?php if($row['id'] != $_SESSION['user_id']): ?>
                            <a href="?sil_user=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Bu kullanıcıyı silersen tüm tarifleri ve yorumları da silinir. Emin misin?')">Sil</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php endif; ?>


        <?php if ($active_tab == 'recipes'): ?>
            <div class="section-header">
                <h2>İçerik (Tarif) Yönetimi</h2>
            </div>

            <form action="" method="GET" class="search-form">
                <input type="hidden" name="tab" value="recipes">
                <input type="text" name="q_recipe" class="search-input" placeholder="Tarif başlığı veya açıklama ara..." value="<?php echo htmlspecialchars($q_recipe); ?>">
                <button type="submit" class="btn-primary">Ara</button>
                <?php if($q_recipe): ?><a href="admin.php?tab=recipes" class="btn-primary" style="background:#777; text-decoration:none;">Sıfırla</a><?php endif; ?>
            </form>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Görsel</th>
                    <th>Başlık</th>
                    <th>Yazar</th>
                    <th style="text-align:right;">İşlem</th>
                </tr>
                <?php
                $sql = "SELECT r.*, u.username FROM recipes r LEFT JOIN users u ON r.user_id = u.id";
                $params = [];
                if($q_recipe){
                    $sql .= " WHERE r.title LIKE ? OR r.description LIKE ?";
                    $params = ["%$q_recipe%", "%$q_recipe%"];
                }
                $sql .= " ORDER BY r.id DESC LIMIT 20";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                while($row = $stmt->fetch()){
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <?php if($row['image']): ?>
                            <img src="uploads/<?php echo $row['image']; ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover;">
                        <?php else: ?>
                            <span style="color:#ccc;">Yok</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td style="text-align:right;">
                        <a href="tarif-detay.php?id=<?php echo $row['id']; ?>" target="_blank" class="action-btn btn-view">Görüntüle</a>
                        <a href="?sil_tarif=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Bu tarifi silmek istediğine emin misin?')">Sil</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php endif; ?>


        <?php if ($active_tab == 'comments'): ?>
            <div class="section-header">
                <h2>Yorum Yönetimi</h2>
            </div>

            <form action="" method="GET" class="search-form">
                <input type="hidden" name="tab" value="comments">
                <input type="text" name="q_comment" class="search-input" placeholder="Yorum içeriğinde ara..." value="<?php echo htmlspecialchars($q_comment); ?>">
                <button type="submit" class="btn-primary">Ara</button>
                <?php if($q_comment): ?><a href="admin.php?tab=comments" class="btn-primary" style="background:#777; text-decoration:none;">Sıfırla</a><?php endif; ?>
            </form>

            <table>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Yorum</th>
                    <th>Yazar</th>
                    <th>Tarif</th>
                    <th style="text-align:right;">İşlem</th>
                </tr>
                <?php
                $sql = "SELECT c.*, u.username, r.title as recipe_title 
                        FROM comments c 
                        LEFT JOIN users u ON c.user_id = u.id 
                        LEFT JOIN recipes r ON c.recipe_id = r.id";
                $params = [];
                if($q_comment){
                    $sql .= " WHERE c.comment LIKE ?";
                    $params = ["%$q_comment%"];
                }
                $sql .= " ORDER BY c.id DESC LIMIT 20";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                while($row = $stmt->fetch()){
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <div style="font-size:13px; color:#333; max-width:300px;">
                            "<?php echo htmlspecialchars($row['comment']); ?>"
                        </div>
                        <small style="color:#999;"><?php echo date("d.m.Y H:i", strtotime($row['created_at'])); ?></small>
                    </td>
                    <td>@<?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <?php if($row['recipe_title']): ?>
                            <span style="color:#666; font-size:12px;"><?php echo htmlspecialchars(mb_substr($row['recipe_title'], 0, 20)); ?>...</span>
                        <?php else: ?>
                            <span style="color:red; font-size:11px;">(Silinmiş Tarif)</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right;">
                        <a href="?sil_yorum=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Yorumu silmek istediğine emin misin?')">Sil</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php endif; ?>

    </div>
</div>

</body>
</html>