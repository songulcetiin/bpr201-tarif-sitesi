<?php
// En üstte boşluk olmasın
require 'includes/db.php'; // Session'ı db.php başlatsın

// Session değişkenlerini boşalt
$_SESSION = [];

// Çerezi sil (Cookie)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu yok et
session_destroy();

// Yönlendir
header("Location: login.php");
exit();
?>