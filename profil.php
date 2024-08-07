    <?php
    include 'database.php';
    include 'bootstrap.php';
    session_start();

    $welcomeMessage = "";
    $logoutLink = "";
    $loginLink = "<a class='nav-link' href='login.php'>Giriş Yap</a>";
    $signupLink = "<a class='nav-link' href='register.php'>Kayıt Ol</a>";
    $profil = "";

    // Kullanıcı giriş yapmışsa
    if (isset($_SESSION['Kullanici_id'])) {
        $KullaniciID = $_SESSION['Kullanici_id'];

        // Müşteri bilgilerini çek
        $KullaniciQuery = "SELECT * FROM kullanici WHERE Kullanici_id= $KullaniciID";
        $KullaniciResult = $conn->query($KullaniciQuery);

        if ($KullaniciResult->num_rows > 0) {
            $kullanici = $KullaniciResult->fetch_assoc();
            $isim = $kullanici['Kullanici_isim']; 
            $soyisim = $kullanici['Kullanici_soyisim']; 
            $eposta = $kullanici['Kullanici_eposta']; 
            $telefon = $kullanici['Kullanici_telefon'];
            $welcomeMessage = "<h1 id='hosgeldin' class='welcome-message'>Hoşgeldiniz, " . $isim . "</h1>";
            $profil = "<a class='nav-link' href='profil.php'>Profil</a>";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kart_ekle'])) {
            $kartNumarasi = $_POST["kart_numarasi"];
            $kartAdsoyad = $_POST["kart_ad_soyad"];
            $sonKullanimTarihi = $_POST["son_kullanma_tarihi"];
            $CVV = $_POST["cvv"];
            
            // Kart ekleme sorgusu
            $kartEkleQuery = "INSERT INTO kartlar (kullanici_id, kart_ad_soyad, kart_numarasi, son_kullanma_tarihi, cvv) VALUES ('$KullaniciID', '$kartAdsoyad', '$kartNumarasi', '$sonKullanimTarihi', '$CVV')";

            if ($conn->query($kartEkleQuery) === TRUE ) {
                echo "Kart başarıyla eklendi.";
            } else {
                echo "Kart eklenirken bir hata oluştu: " . $conn->error;
            }
        }
        // Kullanıcının kartlarını al
        $KartlarQuery = "SELECT * FROM kartlar WHERE kullanici_id = $KullaniciID";
        $KartlarResult = $conn->query($KartlarQuery);
        if (isset($_GET['kart_sil'])) {
            $kartID = $_GET['kart_sil'];
            $silQuery = "DELETE FROM kartlar WHERE kart_id = $kartID AND kullanici_id = $KullaniciID";
            if ($conn->query($silQuery) === TRUE) {
                // Kart başarıyla silindiği zaman uyarı mesajı göster
                echo '<div class="alert alert-success" role="alert">Kart başarıyla silindi.</div>';
                // Silme işleminden sonra sayfayı yenile
                echo '<meta http-equiv="refresh" content="1;URL=profil.php">';
            } else {
                // Kart silinemediği zaman uyarı mesajı göster
                echo '<div class="alert alert-danger" role="alert">Kart silinirken bir hata oluştu.</div>';
            }
        }
        

        // Profil bilgilerini güncelleme işlemi
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profil_guncelle'])) {
            $yeniIsim = $_POST["isim"];
            $yeniSoyisim = $_POST["soyisim"];
            $yeniEposta = $_POST["eposta"];
            $yeniTelefon = $_POST["telefon"];

            $GuncelleQuery = "UPDATE kullanici SET Kullanici_isim = '$yeniIsim', Kullanici_soyisim = '$yeniSoyisim', Kullanici_eposta = '$yeniEposta', Kullanici_telefon = '$yeniTelefon' WHERE Kullanici_id = $KullaniciID";

            if ($conn->query($GuncelleQuery) === TRUE) {
                header("Location: profil.php");
                exit;
            } else {
                echo "Güncelleme işlemi sırasında bir hata oluştu: " . $conn->error;
                exit;
            }
        }

        // Şifre değiştirme işlemi
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sifre_degistir'])) {
            $mevcutSifre = $_POST["mevcut_sifre"];
            $yeniSifre = $_POST["yeni_sifre"];
            $yeniSifreTekrar = $_POST["yeni_sifre_tekrar"];

            // Mevcut şifre doğrulaması
            $mevcutSifreQuery = "SELECT Kullanici_sifre FROM kullanici WHERE Kullanici_id = $KullaniciID";
            $mevcutSifreResult = $conn->query($mevcutSifreQuery);

            if ($mevcutSifreResult->num_rows > 0) {
                $row = $mevcutSifreResult->fetch_assoc();
                $dogrulananSifre = $row["Kullanici_sifre"];

                if ($dogrulananSifre == $mevcutSifre && $yeniSifre == $yeniSifreTekrar) {
                    // Şifreleri güncelle
                    $sifreGuncelleQuery = "UPDATE kullanici SET Kullanici_sifre = '$yeniSifre' WHERE Kullanici_id = $KullaniciID";
                    if ($conn->query($sifreGuncelleQuery) === TRUE) {
                        header("Location: profil.php");
                        exit;
                    } else {
                        echo "Şifre güncelleme işlemi sırasında bir hata oluştu: " . $conn->error;
                        exit;
                    }
                } else {
                    echo "Mevcut şifreyi doğru girin ve yeni şifrelerin eşleştiğinden emin olun.";
                    exit;
                }
            } else {
                echo "Mevcut şifre doğrulama hatası.";
                exit;
            }
        }

        $logoutLink = "<a class='nav-link' href='logout.php'>Çıkış Yap</a>";
        $loginLink = ""; // Giriş yap linkini görünmez yap
        $signupLink = ""; // Kayıt ol linkini görünmez yap
    }



    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/profil.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    
    
        <title>Profil</title>
    
    </head>
    <body>
        
        <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg   #ff7b00 fixed-top">
        <div class="container">
        <a class="navbar-brand" href="#">
                    <img src="images/CarDuckLogo.png" style="max-width:300px;height: 120px" alt="Resim" class="logo">
            <a class="navbar-brand" href="#">Araç Kiralama</a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Anasayfa |</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="hakkimizda.php">Hakkımızda |</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletisim.php">İletişim |</a>
                    </li>
                    
                    <?php echo $loginLink ;  ?>
                
                    <?php echo $signupLink ; ?>
                    
                    <?php echo $welcomeMessage ; ?>
                    <?php echo $profil; ?>
                
                    <?php echo $logoutLink ; ?>
                
                </ul>
            </div>
        </div>
    </nav>


    <div class = "containertop">
    <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Sekmeli menü -->
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" id="profil-bilgileri-tab" data-toggle="tab" href="#profil-bilgileri" role="tab" aria-controls="profil-bilgileri" aria-selected="true">Profil Bilgileri</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sifre-degistir-tab" data-toggle="tab" href="#sifre-degistir" role="tab" aria-controls="sifre-degistir" aria-selected="false">Şifre Değiştir</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="kart-islemleri-tab" data-toggle="tab" href="#kart-islemleri" role="tab" aria-controls="kart-islemleri" aria-selected="false">Kartlarım</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="rezervasyonlar-tab" data-toggle="tab" href="#rezervasyonlar" role="tab" aria-controls="rezervasyonlar" aria-selected="false">Rezervasyonlarım</a>
                        </li>
                    </ul>

                    <!-- Sekmeli menü içeriği -->
                    <div class="tab-content">
                        <!-- Profil bilgileri sekmesi -->
                        <div class="tab-pane fade show active" id="profil-bilgileri" role="tabpanel" aria-labelledby="profil-bilgileri-tab">
                            <div class="card">
                                <h5 class="card-header">Profil Bilgileri</h5>
                                <div class="card-body">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="form-group">
                                            <label for="isim">İsim:</label>
                                            <input type="text" class="form-control" id="isim" name="isim" value="<?php echo $isim; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="soyisim">Soyisim:</label>
                                            <input type="text" class="form-control" id="soyisim" name="soyisim" value="<?php echo $soyisim; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="eposta">E-Posta:</label>
                                            <input type="email" class="form-control" id="eposta" name="eposta" value="<?php echo $eposta; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="telefon">Telefon:</label>
                                            <input type="text" class="form-control" id="telefon" name="telefon" value="<?php echo $telefon; ?>">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary" name="profil_guncelle">Bilgileri Güncelle</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Şifre değiştirme sekmesi -->
                        <div class="tab-pane fade" id="sifre-degistir" role="tabpanel" aria-labelledby="sifre-degistir-tab">
                            <div class="card">
                                <h5 class="card-header">Şifre Değiştirme</h5>
                                <div class="card-body">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="form-group">
                                            <label for="mevcut_sifre">Mevcut Şifre:</label>
                                            <input type="password" class="form-control" id="mevcut_sifre" name="mevcut_sifre" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="yeni_sifre">Yeni Şifre:</label>
                                            <input type="password" class="form-control" id="yeni_sifre" name="yeni_sifre" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="yeni_sifre_tekrar">Yeni Şifre (Tekrar):</label>
                                            <input type="password" class="form-control" id="yeni_sifre_tekrar" name="yeni_sifre_tekrar" required>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary" name="sifre_degistir">Şifreyi Değiştir</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Kart İşlemleri Menüsü -->
                        <div class="tab-pane fade" id="kart-islemleri" role="tabpanel" aria-labelledby="kart-islemleri-tab">
                                        <div class="card">
                                            <h5 class="card-header">Kart İşlemleri</h5>
                                            <div class="card-body">
                                                <!-- Kart Ekle Formu -->
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                    <div class="form-group">
                                                        <label for="kart_ad_soyad">Kart Sahibi Adı Soyadı:</label>
                                                        <input type="text" class="form-control" id="kart_ad_soyad" name="kart_ad_soyad" required>
                                                    </div>
                                                    <div class="form-group">
                                                    <div class="form-group">
                                                <label for="kart_numarasi">Kart Numarası:</label>
                                                <input type="text" class="form-control" id="kart_numarasi" name="kart_numarasi" required oninput="formatKartNumarasi(this)">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="son_kullanma_tarihi">Son Kullanma Tarihi:</label>
                                                        <input type="date" class="form-control" id="son_kullanma_tarihi" name="son_kullanma_tarihi" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cvv">CVV:</label>
                                                        <input type="text" class="form-control" id="cvv" name="cvv" required>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-primary" name="kart_ekle">Kart Ekle</button>
                                                    </div>
                                                </form>

                                                <!-- Kullanıcıya ait kartları listeleme -->
                                                <h5 class="mt-4">Eklenen Kartlar</h5>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Kart Sahibi</th>
                                                            <th>Kart Numarası</th>
                                                            <th>Son Kullanma Tarihi</th>
                                                            <th>İşlemler</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php


                                                        if ($KartlarResult->num_rows > 0) {
                                                            while ($kart = $KartlarResult->fetch_assoc()) {
                                                                echo "<tr>";
                                                                echo "<td>" . $kart["kart_id"] . "</td>";
                                                                echo "<td>" . $kart["kart_ad_soyad"] . "</td>";
                                                                echo "<td>" . $kart["kart_numarasi"] . "</td>";
                                                                echo "<td>" . $kart["son_kullanma_tarihi"] . "</td>";
                                                                echo "<td><a href='profil.php?kart_sil=" . $kart["kart_id"] . "' class='btn btn-danger name='kart_sil''>Sil</a></td>";
                                                                echo "</tr>";
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='5'>Henüz kart eklenmemiş.</td></tr>";
                                                        }
                                                        ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                    <!-- Rezervasyonlar sekmesi -->
                        <div class="tab-pane fade" id="rezervasyonlar" role="tabpanel" aria-labelledby="rezervasyonlar-tab">
                                    <div class="card">
                                        <h5 class="card-header">Rezervasyonlar</h5>
                                        <div class="card-body">
                                            <!-- Rezervasyonları listeleme -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                            <th>Araç Bilgisi</th>
                                                        <th>Başlangıç Tarihi</th>
                                                        <th>Bitiş Tarihi</th>
                                                        <th>Rezervasyon Tarihi</th>
                                                        <!-- Diğer gereken sütun başlıkları eklenebilir -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    
                                                    // Kullanıcının rezervasyonlarını al
                                                    $RezervasyonlarQuery = "SELECT * FROM rezervasyon WHERE kullanici_id = $KullaniciID";
                                                    $RezervasyonlarResult = $conn->query($RezervasyonlarQuery);

                                                    if ($RezervasyonlarResult->num_rows > 0) {
                                                        while ($rezervasyon = $RezervasyonlarResult->fetch_assoc()) {
                                                            echo "<td>" . $rezervasyon["rezervasyon_id"] . "</td>";
                                                            $aracID = $rezervasyon["arac_id"];
                                                            // Araç bilgisini çekmek için araç tablosundan sorgu yapın
                                                            $AracQuery = "SELECT * FROM araclar WHERE Arac_id = $aracID";
                                                            $AracResult = $conn->query($AracQuery);
                                                            if ($AracResult->num_rows > 0) {
                                                                $arac = $AracResult->fetch_assoc();
                                                                // Araç bilgisini kullanarak marka ve modeli gösterin
                                                                echo "<td>" . $arac['Arac_marka'] . ' ' . $arac['Arac_model'] . "</td>";
                                                            } else {
                                                                echo "<td>Bilinmeyen Araç</td>";
                                                            }
                                                            echo "<td>" . $rezervasyon["baslangic_tarihi"] . "</td>";
                                                            echo "<td>" . $rezervasyon["bitis_tarihi"] . "</td>";
                                                            echo "<td>" . $rezervasyon["rezervasyon_tarihi"] . "</td>";
                                                        
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='3'>Henüz rezervasyon yapılmamış.</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                        </div>             
                    </div>
                </div>
            </div>
        </div>
        </div>

    
        <!-- Footer -->
        <footer class="footer mt-auto py-3 bg-light">
            <div class="footer-container text-center">
                <span class="text-muted">Araç Kiralama &copy; 2024. Tüm hakları saklıdır.</span>
            </div>
        </footer>
        <script type="text/javascript" src="js/logout.js"></script>
        <script type="text/javascript" src="js/kart.js"></script>
    </body>
    </html>
