// Sayfadaki tüm DOM (HTML elementleri) yüklendikten sonra bu kod çalışır
document.addEventListener("DOMContentLoaded", function() {

    // Sayfadaki tüm "beğen" butonlarını seç
    const likeButtons = document.querySelectorAll(".like-button");

    // Her bir buton için bir olay dinleyici (event listener) ekle
    likeButtons.forEach(button => {
        button.addEventListener("click", function() {
            
            // Tıklanan butonun içindeki kalp ikonunu bul
            const icon = this.querySelector("i");
            
            // Tıklanan butonun içindeki beğeni sayısı yazısını (span) bul
            const likeCountSpan = this.querySelector(".like-count");
            
            // Beğeni sayısını yazıdan alıp sayıya çevir
            let currentLikes = parseInt(likeCountSpan.textContent);

            // Butonun "liked" (beğenilmiş) class'ı var mı kontrol et
            if (this.classList.contains("liked")) {
                // Eğer beğenilmişse:
                // 1. "liked" class'ını kaldır
                this.classList.remove("liked");
                
                // 2. İkonu "boş kalp" yap
                icon.classList.remove("fa-solid"); // Dolu ikonu kaldır
                icon.classList.add("fa-regular"); // Boş ikonu ekle
                
                // 3. Beğeni sayısını 1 azalt
                currentLikes--;

            } else {
                // Eğer beğenilmemişse:
                // 1. "liked" class'ını ekle
                this.classList.add("liked");
                
                // 2. İkonu "dolu kalp" yap
                icon.classList.remove("fa-regular"); // Boş ikonu kaldır
                icon.classList.add("fa-solid"); // Dolu ikonu ekle
                
                // 3. Beğeni sayısını 1 arttır
                currentLikes++;
            }

            // Güncellenen beğeni sayısını tekrar yazı olarak ekrana yaz
            likeCountSpan.textContent = currentLikes;
        });
    });
});