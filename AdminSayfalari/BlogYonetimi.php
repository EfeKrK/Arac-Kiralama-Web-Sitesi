
<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}

// Mevcut blogları veritabanından al
$query = "SELECT * FROM blog";
$result = mysqli_query($conn, $query);

// Blogları silme işlemini burada gerçekleştir
if(isset($_POST['sil'])) {
    $silinenler = $_POST['sil'];
    
    foreach($silinenler as $blog_id) {
        // Silme sorgusu
        $sil_query = "DELETE FROM blog WHERE id = $blog_id";
        
        // Sorguyu çalıştır
        if(mysqli_query($conn, $sil_query)) {
            echo "Blog(ID: $blog_id) başarıyla silindi.<br>";
            header("Refresh:0");
        } else {
            echo "Blog(ID: $blog_id) silinirken bir hata oluştu: " . mysqli_error($conn) . "<br>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/AdminSayfa.css">
    <link rel="stylesheet" href="css/BlogYonetimi.css">
    <!-- Harici CSS dosyalarını buraya ekleyebilirsin -->
</head>
<body>
    <div class="sidebar">
        <h2>Admin Paneli</h2>
        <div class="menu">
            <ul>
                
                <li><a href="AdminSayfasi.php">Araç Yönetimi</a></li>
                <li><a href="BlogYonetimi.php" class="active">Blog Yönetimi</a></li>
                <li><a href="HakkimizdaYonetimi.php">Hakkımızda Yönetimi</a></li>
                <li><a href="IletisimYonetimi.php">İletişim Yönetimi</a></li>
                <li><a href="RezervasyonYonetimi.php">Rezervasyon Yönetimi</a></li>
                <li><a href="KullanicilariYonet.php">Kullanıcıları Yönet</a></li>
                <li class = "cikisyap"><a href="adminlogout.php">Çıkış Yap</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
    <h2 class="BlogYonetimi">Blog Yönetimi</h2>
    <a href="BlogEkle.php" class="add-button">Yeni Blog Ekle</a> <!-- Yeni blog ekleme butonu -->
    <form method="POST" action=""> <!-- Silme formu -->
    <input type="submit" value="Seçili Blogları Sil" class="delete-button"> <!-- Seçilen blogları silme butonu -->
        <div class="card-container">
            <!-- Her bir blogu döngüyle listele -->
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='card'>";
                echo '<img src="data:image/jpeg;base64,' . base64_encode($row['resim']) . '" alt="' . $row['baslik'] . '">';
                echo "<div class='card-content'>";
                echo "<h3><strong>" . $row['baslik'] . "</strong></h3>"; // Sadece başlık yazdırılıyor, bağlantı kaldırıldı
                echo "<p> Eklenme Tarihi:   " . $row['olusturma_tarihi'] . "</p>";
                echo "<br>";
                echo "Seç: <input type='checkbox' name='sil[]' value='" . $row['id'] . "' class='checkbox'>"; // Her blog için bir seçim kutusu oluştur
                echo "<br>";
                echo "<br>";
                echo "<button type='button' onclick=\"window.location.href='blogduzenle.php?id=" . $row['id'] . "'\">Blog'u Düzenle</button>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
        
    </form>
    
</div>
</body>
</html>
