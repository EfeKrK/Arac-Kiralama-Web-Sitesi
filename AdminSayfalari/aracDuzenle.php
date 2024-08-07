<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}


// Eğer bir araç ID'si belirtilmişse
if(isset($_GET['id'])) {
    $arac_id = $_GET['id'];

    // Belirtilen aracın bilgilerini veritabanından al
    $query = "SELECT * FROM araclar WHERE Arac_id = $arac_id";
    $result = mysqli_query($conn, $query);

    // Araç varsa, bilgileri al
    if(mysqli_num_rows($result) > 0) {
        $arac = mysqli_fetch_assoc($result);
    } else {
        echo "Belirtilen araç bulunamadı.";
        exit; // İşlemi sonlandır
    }
} else {
    echo "Araç ID belirtilmedi.";
    exit; // İşlemi sonlandır
}

// Tüm şubeleri veritabanından çek
$query = "SELECT * FROM subeler";
$result = mysqli_query($conn, $query);

// Şubeleri diziye aktar
$subeler = [];
while ($row = mysqli_fetch_assoc($result)) {
    $subeler[] = $row;
}

// Eğer form gönderildiyse
if(isset($_POST['kaydet'])) {
    $arac_id = $_POST['arac_id'];
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $yil = $_POST['yil'];
    $renk = $_POST['renk'];
    $gunluk_ucret = $_POST['gunluk_ucret'];
    
    $sube_id = $_POST['sube'];

    // Yeni resim yüklendiyse
    if(isset($_FILES['yeni_resim']) && $_FILES['yeni_resim']['error'] == 0) {
        $yeni_resim = addslashes(file_get_contents($_FILES['yeni_resim']['tmp_name']));
        $update_query = "UPDATE araclar SET Arac_marka = '$marka', Arac_model = '$model', Arac_yil = $yil, Arac_renk = '$renk', Arac_gunluk_ucret = $gunluk_ucret,  Arac_Görsel = '$yeni_resim', sube_id = '$sube_id' WHERE Arac_id = $arac_id";
    } else {
        // Yeni resim yüklenmediyse, sadece metin bilgilerini güncelle
        $update_query = "UPDATE araclar SET Arac_marka = '$marka', Arac_model = '$model', Arac_yil = $yil, Arac_renk = '$renk', Arac_gunluk_ucret = $gunluk_ucret,  sube_id = '$sube_id' WHERE Arac_id = $arac_id";
    }

    // Güncelleme sorgusunu çalıştır
    if(mysqli_query($conn, $update_query)) {
        echo "Araç başarıyla güncellendi.";
        header("Refresh:0");
    } else {
        echo "Araç güncellenirken bir hata oluştu: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Araç Düzenle</title>
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
        <h2>Araç Bilgilerini Düzenle</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="arac_id" value="<?php echo $arac['Arac_id']; ?>">
            <label for="marka">Marka:</label>
            <input type="text" id="marka" name="marka" value="<?php echo $arac['Arac_marka']; ?>"><br>
            <label for="model">Model:</label>
            <input type="text" id="model" name="model" value="<?php echo $arac['Arac_model']; ?>"><br>
            <label for="yil">Yıl:</label>
            <input type="text" id="yil" name="yil" value="<?php echo $arac['Arac_yil']; ?>"><br>
            <label for="renk">Renk:</label>
            <input type="text" id="renk" name="renk" value="<?php echo $arac['Arac_renk']; ?>"><br>
            <label for="gunluk_ucret">Günlük Ücret:</label>
            <input type="text" id="gunluk_ucret" name="gunluk_ucret" value="<?php echo $arac['Arac_gunluk_ucret']; ?>"><br>
                      
            <label for="sube">Şube:</label>
            <select id="sube" name="sube">
    <?php foreach ($subeler as $sube): ?>
        <option value="<?php echo $sube['Sube_id']; ?>" <?php if($sube['Sube_id'] == $arac['sube_id']) echo 'selected'; ?>><?php echo $sube['Sube_adi']; ?></option>
    <?php endforeach; ?>
</select><br>
            <label for="resim">Mevcut Resim:</label>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($arac['Arac_Görsel']); ?>" alt="Mevcut Resim" width="35%" height="35%"><br><br>            
            <label for="yeni_resim">Yeni Resim:</label>
            <input type="file" id="yeni_resim" name="yeni_resim"><br>
            <input type="submit" name="kaydet" value="Kaydet">
        </form>
    </div>
</body>
</html>
