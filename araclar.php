<?php
include 'database.php';
include 'bootstrap.php';
session_start();
$welcomeMessage = "";
$logoutLink = "";
$profil = "";
$loginLink = "<a class='nav-link'  href='login.php'>Giriş Yap</a>";
$signupLink = "<a class='nav-link' href='register.php'>Kayıt Ol</a>";
// Kullanıcı giriş yapmışsa
if (isset($_SESSION['Kullanici_id'])) {
    $kullaniciID = $_SESSION['Kullanici_id'];
    // Müşteri bilgilerini çek
    $kullaniciQuery = "SELECT * FROM kullanici WHERE Kullanici_id = $kullaniciID";
    $kullaniciResult = $conn->query($kullaniciQuery);
    if ($kullaniciResult->num_rows > 0) {
        $kullanici = $kullaniciResult->fetch_assoc();
        $isim = $kullanici['Kullanici_isim'];
        $welcomeMessage = "<h1 id='hosgeldin' class='welcome-message'>Hoşgeldiniz, " . $isim . "</h1>";
        $profil = "<a class='nav-link' href='profil.php'>Profil</a>";

    }
    $logoutLink = "<a class='nav-link' href='logout.php'>Çıkış Yap</a>";
    $loginLink = ""; // Giriş yap linkini görünmez yap
    $signupLink = ""; // Kayıt ol linkini görünmez yap
}
// Seçilen tarih aralığı
$baslangic_tarihi = $_GET['baslangic_tarihi'];
$bitis_tarihi = $_GET['bitis_tarihi'];
// Seçilen tarih aralığına göre gün sayısı hesapla
$baslangic = strtotime($baslangic_tarihi);
$bitis = strtotime($bitis_tarihi);
$gun_sayisi = ($bitis - $baslangic) / (60 * 60 * 24);

$_SESSION['gun_sayisi'] = $gun_sayisi;
// Eğer başlangıç ve bitiş tarihleri aynı günse, en az 1 günlük ücret uygula
if ($gun_sayisi == 0) {
    $gun_sayisi = 1;
}else {
    $gun_sayisi += 1; // Başlangıç tarihinin de dahil edilmesi gerekiyor
}
// Toplam kiralama bedeli için değişken
$toplam_bedel = 0;
$sube_id = $_GET['alis_sube'];
// Veritabanından araçların günlük ücretlerini al
$sql = "SELECT Arac_gunluk_ucret FROM araclar WHERE sube_id=$sube_id";
$result = $conn->query($sql);

// Her bir aracın günlük ücreti ile gün sayısını çarpıp toplam fiyatı hesapla
while($row = $result->fetch_assoc()) {
    $gunluk_ucret = $row['Arac_gunluk_ucret'];
    $toplam_bedel += $gunluk_ucret * $gun_sayisi;
}
$_SESSION['toplam_bedel'] = $toplam_bedel;
// Tarih aralığı
$baslangic_tarihi = isset($_GET['baslangic_tarihi']) ? $_GET['baslangic_tarihi'] : "Belirtilmedi";
$bitis_tarihi = isset($_GET['bitis_tarihi']) ? $_GET['bitis_tarihi'] : "Belirtilmedi";
$tarih_araligi = $baslangic_tarihi . ' - ' . $bitis_tarihi;
// Adım
$adim = isset($_GET['adim']) ? $_GET['adim'] : 1; 



// Alış şubesi
if(isset($_GET['alis_sube']) && !empty($_GET['alis_sube'])) {
    $alis_sube = $_GET['alis_sube'];

    $sql = "SELECT * FROM subeler WHERE Sube_id=$alis_sube";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $sube = $result->fetch_assoc();

        // Aracın adını ve modelini al
        $alis_sube_ad = $sube['Sube_adi'];
    } else {
        // Arac bulunamadı, hata mesajı gösterilebilir
        echo "Sube bulunamadı.";
        exit;
    }

} else {
    echo "Alış şubesi belirtilmemiş.";
    exit;
}
// Varış şubesi (varsayılan olarak alış şubesi ile aynı)
if(isset($_GET['varis_sube']) && !empty($_GET['varis_sube'])) {
    $varis_sube = $_GET['varis_sube'];
    
    $sql = "SELECT * FROM subeler WHERE Sube_id=$varis_sube";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $sube = $result->fetch_assoc();

        // Varış şube adını al
        $varis_sube_ad = $sube['Sube_adi'];
        
    } else {
        // Şube bulunamadı, hata mesajı gösterilebilir
        echo "Varış şube bulunamadı.";
        exit;
    }
} else {
    // Eğer varış şubesi belirtilmemişse, alış şubesini varsayılan olarak kullan
    $varis_sube = $alis_sube; // varış şubesini alış şubesiyle aynı yap
    $varis_sube_ad = $alis_sube_ad; // varış şube adını da alış şube adıyla aynı yap
}


// Eğer varış şubesi alış şubesi ile aynı değilse, sadece uygun varış şubesine sahip araçları seç
if ($alis_sube != $varis_sube) {
    $sql .= " AND sube_id=$varis_sube";
}

// Başlangıç ve bitiş tarihleri
if(isset($_GET['baslangic_tarihi']) && isset($_GET['bitis_tarihi']) && !empty($_GET['baslangic_tarihi']) && !empty($_GET['bitis_tarihi'])) {
    $baslangic_tarihi = $_GET['baslangic_tarihi'];
    $bitis_tarihi = $_GET['bitis_tarihi'];
} else {
    echo "Başlangıç veya bitiş tarihi belirtilmemiş.";
    exit;
}


// Aktif tik işaretini oluştur
function aktifTik($hedefAdim, $simdikiAdim) {
    if ($hedefAdim == $simdikiAdim) {
        return '<span class="tik">&#10003;</span>';
    } else {
        return '';
    }
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/araclar.css">
    <link rel="stylesheet" href="css/aracdetay.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/sayfalama.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <title>Araçlar</title>
   

</head>
<body>
<div class="custom-border">
    <nav class="navbar navbar-expand-lg navbar-light bg-warning fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/CarDuckLogo.png" style="max-width: 300px; height: 120px;" alt="Resim" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hakkimizda.php">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletisim.php">İletişim</a>
                    </li>
                    
                    <?php echo $loginLink; ?>
                    <?php echo $signupLink; ?>
                    <?php echo $welcomeMessage; ?>
                    <?php echo $profil; ?>
                    <?php echo $logoutLink; ?>
                    
                </ul>
            </div>
        </div>
    </nav>
</div>


<nav class="detaylar">
    <ul>
    <li class="<?php echo $adim == 1 ? 'active' : ''; ?>"><?php echo aktifTik(1, $adim); ?> Tarih Aralığı: <?php echo $tarih_araligi; ?> | Alış Şube: <?php echo $alis_sube_ad; ?> |  Varış Şube: <?php echo $varis_sube_ad; ?></li>
        <li class="<?php echo $adim == 2 ? 'active' : ''; ?>"><?php echo aktifTik(2, $adim); ?> Seçilen Araç: </li>
        <li class="<?php echo $adim == 3 ? 'active' : ''; ?>"><?php echo aktifTik(3, $adim); ?> Ödeme Bilgileri</li>
       
    </ul>
</nav>


<!-- Ana içerik -->
<div class = "containertop">
<div class="container mt-5">
    <div class="row justify-content-center mt-5">
        <?php
        $limit = 9; // Her sayfada gösterilecek araç sayısı
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // Sayfa numarasını al
        $start = ($page - 1) * $limit; // Başlangıç indeksi hesapla
        
// Araçları seçerken rezervasyon durumunu ve tarih aralığını kontrol eden sorgu
$araclarQuery = "SELECT * FROM araclar A 
                WHERE A.Arac_id NOT IN (
                    SELECT R.Arac_id FROM rezervasyon R 
                    WHERE R.rezervasyon_durumu = 1 
                    AND ('$baslangic_tarihi' BETWEEN R.baslangic_tarihi AND R.bitis_tarihi) 
                    OR ('$bitis_tarihi' BETWEEN R.baslangic_tarihi AND R.bitis_tarihi)
                ) 
                AND A.sube_id = $alis_sube";
$araclarResult = $conn->query($araclarQuery);

// Rezervasyon tarihlerini kontrol eden sorgu
$rezervasyonCheckQuery = "SELECT * FROM rezervasyon WHERE Arac_id = 'Arac_id'
                        AND ((baslangic_tarihi <= '$baslangic_tarihi' AND bitis_tarihi >= '$baslangic_tarihi') 
                        OR (baslangic_tarihi <= '$bitis_tarihi' AND bitis_tarihi >= '$bitis_tarihi')) 
                        AND rezervasyon_durumu = 1";
$rezervasyonCheckResult = $conn->query($rezervasyonCheckQuery);
        
        if ($araclarResult->num_rows === 0 && $rezervasyonCheckResult->num_rows === 0) {
            echo '<div class="col-md-12"><p class="text-center">Müsait aracımız kalmamıştır.</p></div>';
        } else {
            while($row = $araclarResult->fetch_assoc()) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['Arac_marka']; ?></h5>
                            <p class="card-text">Model: <?php echo $row['Arac_model']; ?></p>
                            <p class="card-text">Yıl: <?php echo $row['Arac_yil']; ?></p>
                            <p class="card-text">Renk: <?php echo $row['Arac_renk']; ?></p>
                            <p class="card-text">Toplam Ücret: <?php echo $row['Arac_gunluk_ucret'] * $gun_sayisi; ?> ₺</p>
                            <?php 
                            // Aracın görsel varsa
                            if ($row['Arac_Görsel']) {
                                echo '<img src="data:image/jpeg;base64,'.base64_encode($row['Arac_Görsel']).'" class="card-img-top" alt="Arac_Görsel">';
                            }
                            ?>
                        </div>
                        <div class="card-footer">
                        <a href="aracdetay.php?id=<?php echo $row['Arac_id']; ?>&sube=<?php echo $sube_id; ?>&varis_sube=<?php echo $varis_sube; ?>&baslangic_tarihi=<?php echo $baslangic_tarihi; ?>&bitis_tarihi=<?php echo $bitis_tarihi; ?>" class="btn btn-primary btn-block">Detayları Gör</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
</div>

<!-- Sayfalama Butonları -->
<div class="container mt-3">
    <div class="row justify-content-center">
        <ul class="pagination">
            <?php
            $sql = "SELECT COUNT(*) as total FROM araclar WHERE sube_id=$sube_id";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total_pages = ceil($row["total"] / $limit); // Toplam sayfa sayısını hesapla
            $previous = $page - 1;
            $next = $page + 1;
            ?>
            <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                <a class="page-link" href="araclar.php?alis_sube=<?php echo $_GET['alis_sube']; ?>&varis_sube=<?php echo $_GET['varis_sube']; ?>&baslangic_tarihi=<?php echo $_GET['baslangic_tarihi']; ?>&bitis_tarihi=<?php echo $_GET['bitis_tarihi']; ?>&page=<?php echo $previous; ?>">Önceki</a>
            </li>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if($page == $i) { echo 'active'; } ?>">
                <a class="page-link" href="araclar.php?alis_sube=<?php echo $_GET['alis_sube']; ?>&varis_sube=<?php echo $_GET['varis_sube']; ?>&baslangic_tarihi=<?php echo $_GET['baslangic_tarihi']; ?>&bitis_tarihi=<?php echo $_GET['bitis_tarihi']; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>

                </li>
            <?php endfor; ?>
            <li class="page-item <?php if($page >= $total_pages){ echo 'disabled'; } ?>">
                <a class="page-link" href="araclar.php?alis_sube=<?php echo $_GET['alis_sube']; ?>&varis_sube=<?php echo $_GET['varis_sube']; ?>&baslangic_tarihi=<?php echo $_GET['baslangic_tarihi']; ?>&bitis_tarihi=<?php echo $_GET['bitis_tarihi']; ?>&page=<?php echo $next; ?>">Sonraki</a>
            </li>
        </ul>
    </div>
</div>


     <!-- Footer -->
     <footer class="footer mt-auto py-3 bg-light">
        <div class="footer-container text-center">
            <span class="text-muted">Araç Kiralama &copy; 2024. Tüm hakları saklıdır.</span>
        </div>
    </footer>

    <script type="text/javascript" src="js/arac.js"></script>
    <script type="text/javascript" src="js/tarih.js"></script>



</body>
</html>
</html>
