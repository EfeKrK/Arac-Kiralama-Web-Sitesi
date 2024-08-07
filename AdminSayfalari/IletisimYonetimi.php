<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}

// İletişim bilgilerini veritabanından al
$query = "SELECT * FROM iletisim ORDER BY idiletisim DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/AdminSayfa.css">
    <link rel="stylesheet" href="css/IletisimYonetimi.css">  
</head>
<body>
    <div class="sidebar">
        <h2>Admin Paneli</h2>
        <div class="menu">
            <ul>
                
                <li><a href="AdminSayfasi.php">Araç Yönetimi</a></li>
                <li><a href="BlogYonetimi.php">Blog Yönetimi</a></li>
                <li><a href="HakkimizdaYonetimi.php">Hakkımızda Yönetimi</a></li>
                <li><a href="IletisimYonetimi.php" class="active">İletişim Yönetimi</a></li>
                <li><a href="RezervasyonYonetimi.php">Rezervasyon Yönetimi</a></li>
                <li><a href="KullanicilariYonet.php">Kullanıcıları Yönet</a></li>
                <li class = "cikisyap"><a href="adminlogout.php">Çıkış Yap</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h2 class="IletisimYonetimi">İletişim Yönetimi</h2>
        <table>
            <tr>
                <th>Ad Soyad</th>
                <th>E-Posta</th>
                <th>Telefon</th>
                <th>Konu</th>
                <th>Mesaj</th>
            </tr>
            <?php
            // Her bir iletişim bilgisini döngüyle listele
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr onclick='window.location=\"IletisimDetay.php?id=" . $row['idiletisim'] . "\";'>";
                echo "<td>" . $row['adsoyad'] . "</td>";
                echo "<td>" . $row['eposta'] . "</td>";
                echo "<td>" . $row['telno'] . "</td>";
                echo "<td>" . $row['konu'] . "</td>";
                echo "<td>";
                // Mesajın uzunluğunu kontrol et
                if (strlen($row['mesaj']) > 90) {
                    // Eğer mesaj 50 karakterden uzunsa, sadece ilk 50 karakteri göster ve bağlantı ekle
                    echo substr($row['mesaj'], 0, 90) . '... (Devamı İçin Tıklayınız)';
                } else {
                    // Eğer mesaj 90 karakterden kısa ise tamamını göster
                    echo $row['mesaj'];
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
