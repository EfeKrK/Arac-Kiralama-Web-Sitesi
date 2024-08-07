<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}

// URL'den rezervasyon ID'sini al
$rezervasyon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Rezervasyon detaylarını veritabanından al
$query = "SELECT * FROM rezervasyon WHERE rezervasyon_id = $rezervasyon_id";
$result = mysqli_query($conn, $query);
$rezervasyon = mysqli_fetch_assoc($result);

// Kullanıcı bilgilerini al
$kullanici_query = "SELECT CONCAT(Kullanici_isim, ' ', Kullanici_soyisim) AS ad_soyad FROM kullanici WHERE Kullanici_id = {$rezervasyon['kullanici_id']}";
$kullanici_result = mysqli_query($conn, $kullanici_query);
$kullanici = mysqli_fetch_assoc($kullanici_result);

// Araç bilgilerini al
$arac_query = "SELECT Arac_marka, Arac_model FROM araclar WHERE Arac_id = {$rezervasyon['arac_id']}";
$arac_result = mysqli_query($conn, $arac_query);
$arac = mysqli_fetch_assoc($arac_result);

// Alış ve varış şube bilgilerini al
$alis_sube_query = "SELECT Sube_adi FROM subeler WHERE Sube_id = {$rezervasyon['alis_sube_id']}";
$alis_sube_result = mysqli_query($conn, $alis_sube_query);
$alis_sube = mysqli_fetch_assoc($alis_sube_result);

$varis_sube_query = "SELECT Sube_adi FROM subeler WHERE Sube_id = {$rezervasyon['varis_sube_id']}";
$varis_sube_result = mysqli_query($conn, $varis_sube_query);
$varis_sube = mysqli_fetch_assoc($varis_sube_result);

// Tüm şubeleri al
$sube_query = "SELECT * FROM subeler";
$sube_result = mysqli_query($conn, $sube_query);

// Mevcut araçları al (rezervasyon tarihleriyle çakışmayanlar hariç)
$araclar_query = "
    SELECT Arac_id, Arac_marka, Arac_model 
    FROM araclar 
    WHERE Arac_id NOT IN (
        SELECT arac_id 
        FROM rezervasyon 
        WHERE (
            (baslangic_tarihi <= '{$rezervasyon['baslangic_tarihi']}' AND bitis_tarihi >= '{$rezervasyon['baslangic_tarihi']}') OR
            (baslangic_tarihi <= '{$rezervasyon['bitis_tarihi']}' AND bitis_tarihi >= '{$rezervasyon['bitis_tarihi']}') OR
            (baslangic_tarihi >= '{$rezervasyon['baslangic_tarihi']}' AND bitis_tarihi <= '{$rezervasyon['bitis_tarihi']}')
        )
    ) OR Arac_id = {$rezervasyon['arac_id']}
";
$araclar_result = mysqli_query($conn, $araclar_query);

// Silme işlemi
if (isset($_POST['delete'])) {
    $delete_query = "DELETE FROM rezervasyon WHERE rezervasyon_id = $rezervasyon_id";
    mysqli_query($conn, $delete_query);
    header("Location: RezervasyonYonetimi.php");
    exit;
}

// Güncelleme işlemi
if (isset($_POST['update'])) {
    $alis_sube_id = intval($_POST['alis_sube']);
    $varis_sube_id = intval($_POST['varis_sube']);
    $arac_id = intval($_POST['arac']);

    $update_query = "
    UPDATE rezervasyon 
    SET varis_sube_id = $varis_sube_id, arac_id = $arac_id 
    WHERE rezervasyon_id = $rezervasyon_id
    ";
    mysqli_query($conn, $update_query);
    header("Location: rezervasyondetay.php?id=$rezervasyon_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyon Detayları</title>
    <link rel="stylesheet" href="css/AdminSayfa.css">
    <link rel="stylesheet" href="css/rezervasyondetay.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Paneli</h2>
        <div class="menu">
            <ul>
                
                <li><a href="AdminSayfasi.php">Araç Yönetimi</a></li>
                <li><a href="BlogYonetimi.php">Blog Yönetimi</a></li>
                <li><a href="HakkimizdaYonetimi.php">Hakkımızda Yönetimi</a></li>
                <li><a href="IletisimYonetimi.php">İletişim Yönetimi</a></li>
                <li><a href="RezervasyonYonetimi.php" class="active">Rezervasyon Yönetimi</a></li>
                <li><a href="KullanicilariYonet.php">Kullanıcıları Yönet</a></li>
                <li class = "cikisyap"><a href="adminlogout.php">Çıkış Yap</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h2>Rezervasyon Detayları</h2>
        <div class="rezervasyon-detay">
            <p><strong>Rezervasyon ID:</strong> <?php echo $rezervasyon['rezervasyon_id']; ?></p>
            <p><strong>Kullanıcı Adı - Soyadı:</strong> <?php echo $kullanici['ad_soyad']; ?></p>
            <?php if ($arac): ?>
                <p><strong>Araba Marka:</strong> <?php echo $arac['Arac_marka']; ?></p>
                <p><strong>Araba Model:</strong> <?php echo $arac['Arac_model']; ?></p>
            <?php else: ?>
                <p><strong>Araba:</strong> Araç Silinmiş</p>
            <?php endif; ?>
            <p><strong>Başlangıç Tarihi:</strong> <?php echo $rezervasyon['baslangic_tarihi']; ?></p>
            <p><strong>Bitiş Tarihi:</strong> <?php echo $rezervasyon['bitis_tarihi']; ?></p>
            <p><strong>Alış Şube:</strong> <?php echo $alis_sube['Sube_adi']; ?></p>
            <p><strong>Varış Şube:</strong> <?php echo $varis_sube['Sube_adi']; ?></p>
            <p><strong>Rezerve Edilme Tarihi:</strong> <?php echo $rezervasyon['rezervasyon_tarihi']; ?></p>
            <p><strong>Toplam Ücret:</strong> <?php echo $rezervasyon['toplam_ucret']; ?> TL</p>
        </div>
        <form method="POST">
            
            <div>
                <label for="varis_sube">Varış Şube:</label>
                <select name="varis_sube" id="varis_sube">
                    <?php
                    // Şubeleri yeniden çek
                    $sube_result = mysqli_query($conn, $sube_query);
                    while ($sube = mysqli_fetch_assoc($sube_result)): ?>
                        <option value="<?php echo $sube['Sube_id']; ?>" <?php if ($sube['Sube_id'] == $rezervasyon['varis_sube_id']) echo 'selected'; ?>>
                            <?php echo $sube['Sube_adi']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="arac">Araç:</label>
                <select name="arac" id="arac">
                    <?php while ($arac_option = mysqli_fetch_assoc($araclar_result)): ?>
                        <option value="<?php echo $arac_option['Arac_id']; ?>" <?php if ($arac_option['Arac_id'] == $rezervasyon['arac_id']) echo 'selected'; ?>>
                            <?php echo $arac_option['Arac_marka'] . ' - ' . $arac_option['Arac_model']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <button type="submit" name="update" class = "guncelle">Güncelle</button>
                <button type="submit" name="delete" class="sil">Sil</button>
            </div>
        </form>
    </div>
</body>
</html>
