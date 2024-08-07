<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}

// Rezervasyonları veritabanından al
$query = "SELECT * FROM rezervasyon";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/AdminSayfa.css"> 
    <link rel="stylesheet" href="css/RezervasyonYonetimi.css">
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
        <h2 class="RezervasyonYonetimi">Rezervasyon Yönetimi</h2>
        <div class="rezervasyonlar">
            <table>
                <tr>
                    <th>Rezervasyon ID</th>
                    <th>Kullanıcı Adı - Soyadı</th>
                    <th>Araba Marka</th>
                    <th>Araba Model</th>
                    <th>Başlangıç Tarihi</th>
                    <th>Bitiş Tarihi</th>
                    <th>Alış Şube</th>
                    <th>Varış Şube</th>
                    <th>Rezerve Edilme Tarihi</th>
                    <th>Toplam Ücret</th>
                </tr>
                <?php
                // Her bir rezervasyon için döngü oluştur
                while ($row = mysqli_fetch_assoc($result)) {
                    // Kullanıcı adı ve soyadı bilgisini al
                    $kullanici_query = "SELECT CONCAT(Kullanici_isim, ' ', Kullanici_soyisim) AS ad_soyad FROM kullanici WHERE Kullanici_id = {$row['kullanici_id']}";
                    $kullanici_result = mysqli_query($conn, $kullanici_query);
                    $kullanici_row = mysqli_fetch_assoc($kullanici_result);

                    // Araç bilgilerini al
                    $arac_query = "SELECT Arac_marka, Arac_model FROM araclar WHERE Arac_id = {$row['arac_id']}";
                    $arac_result = mysqli_query($conn, $arac_query);
                    $arac_row = mysqli_fetch_assoc($arac_result);

                    // Alış ve varış şube bilgilerini al
                    $alis_sube_query = "SELECT Sube_adi FROM subeler WHERE Sube_id = {$row['alis_sube_id']}";
                    $alis_sube_result = mysqli_query($conn, $alis_sube_query);
                    $alis_sube_row = mysqli_fetch_assoc($alis_sube_result);

                    $varis_sube_query = "SELECT Sube_adi FROM subeler WHERE Sube_id = {$row['varis_sube_id']}";
                    $varis_sube_result = mysqli_query($conn, $varis_sube_query);
                    $varis_sube_row = mysqli_fetch_assoc($varis_sube_result);

                    // Rezervasyon bilgilerini ekrana yazdır
                    echo "<tr onclick='window.location=\"rezervasyondetay.php?id={$row['rezervasyon_id']}\"'>";
                    echo "<td>{$row['rezervasyon_id']}</td>";
                    echo "<td>{$kullanici_row['ad_soyad']}</td>";
                    if ($arac_row) {
                        echo "<td>{$arac_row['Arac_marka']}</td>";
                        echo "<td>{$arac_row['Arac_model']}</td>";
                    } else {
                        echo "<td>Araç Silinmiş</td>";
                        echo "<td>Araç Silinmiş</td>";
                    }
                    echo "<td>{$row['baslangic_tarihi']}</td>";
                    echo "<td>{$row['bitis_tarihi']}</td>";
                    echo "<td>{$alis_sube_row['Sube_adi']}</td>";
                    echo "<td>{$varis_sube_row['Sube_adi']}</td>";
                    echo "<td>{$row['rezervasyon_tarihi']}</td>";
                    echo "<td>{$row['toplam_ucret']} TL</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <p class="detay-uyarisi">Satıra tıklayarak detaylara gidiniz.</p>
    </div>
</body>
</html>
