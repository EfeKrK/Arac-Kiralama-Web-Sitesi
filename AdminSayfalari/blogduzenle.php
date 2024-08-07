<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}


// Eğer bir blog ID'si belirtilmişse
if(isset($_GET['id'])) {
    $blog_id = $_GET['id'];

    // Belirtilen blogun bilgilerini veritabanından al
    $query = "SELECT * FROM blog WHERE id = $blog_id";
    $result = mysqli_query($conn, $query);

    // Blog varsa, bilgileri al
    if(mysqli_num_rows($result) > 0) {
        $blog = mysqli_fetch_assoc($result);
    } else {
        echo "Belirtilen blog bulunamadı.";
        exit; // İşlemi sonlandır
    }
} else {
    echo "Blog ID belirtilmedi.";
    exit; // İşlemi sonlandır
}

// Eğer form gönderildiyse
if(isset($_POST['kaydet'])) {
    $blog_id = $_POST['blog_id'];
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];

    // Yeni resim yüklendiyse
    if(isset($_FILES['yeni_resim']) && $_FILES['yeni_resim']['error'] == 0) {
        $yeni_resim = addslashes(file_get_contents($_FILES['yeni_resim']['tmp_name']));
        $update_query = "UPDATE blog SET baslik = '$baslik', icerik = '$icerik', resim = '$yeni_resim' WHERE id = $blog_id";
    } else {
        // Yeni resim yüklenmediyse, sadece metin bilgilerini güncelle
        $update_query = "UPDATE blog SET baslik = '$baslik', icerik = '$icerik' WHERE id = $blog_id";
    }

    // Güncelleme sorgusunu çalıştır
    if(mysqli_query($conn, $update_query)) {
        echo "Blog başarıyla güncellendi.";
        header("Refresh:0");
    } else {
        echo "Blog güncellenirken bir hata oluştu: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Blog Düzenle</title>
    <link rel="stylesheet" href="css/AdminSayfa.css">
    <link rel="stylesheet" href="css/BlogDuzenle.css"> <!-- Harici CSS dosyası -->
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
        <h2>Blog Düzenle</h2>
        <!-- Blog düzenleme formu -->
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
            <label for="baslik">Başlık:</label><br>
            <input type="text" id="baslik" name="baslik" value="<?php echo $blog['baslik']; ?>"><br>
            <label for="icerik">İçerik:</label><br>
            <textarea id="icerik1" name="icerik"><?php echo $blog['icerik']; ?></textarea><br>
            <label for="resim">Mevcut Resim:</label><br>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($blog['resim']); ?>" alt="Mevcut Resim" width="35%" height="35%"><br><br>
            <label for="yeni_resim">Yeni Resim:</label><br>
            <input type="file" id="yeni_resim" name="yeni_resim"><br>
            <input type="submit" name="kaydet" value="Kaydet">
        </form>
    </div>
</body>
</html>