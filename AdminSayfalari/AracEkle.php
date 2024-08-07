<?php
// Veritabanı bağlantısını sağlayan dosyayı içe aktar
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}



// Eğer form gönderildiyse
if(isset($_POST['ekle'])) {
    // Formdan gelen verileri al
    $marka = $_POST['marka']; // Aracın markası
    $model = $_POST['model']; // Aracın modeli
    $yil = $_POST['yil']; // Aracın üretim yılı
    $renk = $_POST['renk']; // Aracın rengi
    $gunluk_ucret = $_POST['gunluk_ucret']; // Aracın günlük kiralama ücreti
    $sube_id = $_POST['sube']; // Aracın hangi şubede bulunduğu

    // Resim dosyasını işleme
    $resimDosyasi = $_FILES['resim']['tmp_name']; // Yüklenen resmin geçici dosya adı
    $resimIcerik = file_get_contents($resimDosyasi); // Resmin içeriği

    // Şube bilgilerini al
    $stmt = mysqli_prepare($conn, "SELECT Sube_adi FROM subeler WHERE Sube_id = ?"); // Şube adını alacak sorguyu hazırla
    mysqli_stmt_bind_param($stmt, "i", $sube_id); // Şube id'sini bağla
    mysqli_stmt_execute($stmt); // Sorguyu çalıştır
    mysqli_stmt_bind_result($stmt, $sube_adi); // Sonuçları bağla
    mysqli_stmt_fetch($stmt); // Sonuçları al
    mysqli_stmt_close($stmt); // Sorgu bağlantısını kapat

    // SQL sorgusunu hazırla
    $sorgu = "INSERT INTO araclar (Arac_marka, Arac_model, Arac_yil, Arac_renk, Arac_gunluk_ucret, sube_id, Arac_Görsel) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Sorguyu hazırla ve bağlantıyı kullan
    $stmt = mysqli_prepare($conn, $sorgu); // Sorguyu hazırla
    if ($stmt) {
        // Değişkenleri bağla
        mysqli_stmt_bind_param($stmt, "sssidss", $marka, $model, $yil, $renk, $gunluk_ucret, $sube_id, $resimIcerik);

        // Sorguyu çalıştır
        if(mysqli_stmt_execute($stmt)) {
            echo "Yeni araç başarıyla eklendi."; // Başarı mesajı
        } else {
            echo "Araç eklenirken bir hata oluştu: " . mysqli_error($conn); // Hata mesajı
        }

        // İşlem sonrasında bağlantıyı kapat
        mysqli_stmt_close($stmt); // Sorgu bağlantısını kapat
    } else {
        echo "Sorgu hazırlanırken bir hata oluştu: " . mysqli_error($conn); // Hata mesajı
    }
}

// Şubeleri veritabanından al
$query = "SELECT Sube_id, Sube_adi FROM subeler"; // Şubeleri alacak sorguyu hazırla
$result = mysqli_query($conn, $query); // Sorguyu çalıştır ve sonucu al
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Yeni Araç Ekle</title>
    <link rel="stylesheet" href="css/AdminSayfa.css">
    <link rel="stylesheet" href="css/AracDuzenle.css">
    
</head>
<body>
    <div class="sidebar">
        <h2>Admin Paneli</h2>
        <div class="menu">
            <ul>
                
                <li><a href="AdminSayfasi.php" class="active">Araç Yönetimi</a></li>
                <li><a href="BlogYonetimi.php">Blog Yönetimi</a></li>
                <li><a href="HakkimizdaYonetimi.php">Hakkımızda Yönetimi</a></li>
                <li><a href="IletisimYonetimi.php">İletişim Yönetimi</a></li>
                <li><a href="RezervasyonYonetimi.php">Rezervasyon Yönetimi</a></li>
                <li><a href="KullanicilariYonet.php">Kullanıcıları Yönet</a></li>
                <li class = "cikisyap"><a href="adminlogout.php">Çıkış Yap</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h2>Araç Ekle</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="marka">Marka:</label>
            <input type="text" id="marka" name="marka" required><br>
            <label for="model">Model:</label>
            <input type="text" id="model" name="model" required><br>
            <label for="yil">Yıl:</label>
            <input type="text" id="yil" name="yil" required><br>
            <label for="renk">Renk:</label>
            <input type="text" id="renk" name="renk" required><br>
            <label for="gunluk_ucret">Günlük Ücret:</label>
            <input type="text" id="gunluk_ucret" name="gunluk_ucret" required><br>
            
            <label for="sube">Şube:</label>
            <select id="sube" name="sube" required>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['Sube_id'] . "'>" . $row['Sube_adi'] . "</option>";
                }
                ?>
            </select><br>
            <label for="resim">Araç Resmi:</label>
            <input type="file" id="resim" name="resim"><br>
            <input type="submit" name="ekle" value="Araç Ekle">
        </form>
    </div>
</body>
</html>
