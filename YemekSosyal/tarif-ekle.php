<?php 
include 'includes/header.php'; 

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $ingr = $_POST['ingredients'];
    $inst = $_POST['instructions'];
    $user_id = $_SESSION['user_id'];
    
    // Resim yükleme
    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); } // Klasör yoksa oluştur
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename);
        $image = $filename;
    }

    $sql = "INSERT INTO recipes (user_id, title, description, image, ingredients, instructions) VALUES (?,?,?,?,?,?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$user_id, $title, $desc, $image, $ingr, $inst]);
    
    header("Location: index.php"); 
    exit();
}
?>

<div style="max-width: 700px; margin: 20px auto;">
    <div class="card">
        <h2 style="color: var(--primary); text-align:center; margin-bottom:20px;">Yeni Tarifini Paylaş</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tarif Başlığı <span style="color:red">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="Örn: Nefis Mercimek Çorbası" required>
            </div>
            
            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" placeholder="Tarifin hakkında kısa bir açıklama..."></textarea>
            </div>

            <div class="form-group">
                <label>Tarif Fotoğrafı</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="form-group">
                <label>Malzemeler (İsteğe Bağlı)</label>
                <textarea name="ingredients" class="form-control" placeholder="Her malzemeyi yeni bir satıra yazın..."></textarea>
            </div>

            <div class="form-group">
                <label>Hazırlanışı (İsteğe Bağlı)</label>
                <textarea name="instructions" class="form-control" style="height:150px;" placeholder="Adım adım tarifin hazırlanışını anlatın..."></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width:100%; font-size:16px;">Tarifi Paylaş <i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>
</body>
</html>