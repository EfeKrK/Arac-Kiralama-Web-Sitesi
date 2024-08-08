<?php
session_start();
include 'database.php';
include 'bootstrap.php';

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
        $welcomeMessage = "<h1 id='hosgeldin' class='welcome-message'>Hoşgeldiniz, " . $isim . "</h1>";
        $profil = "<a class='nav-link' href='profil.php'>Profil</a>";
    }
    
    $logoutLink = "<a class='nav-link' href='logout.php'>Çıkış Yap</a>";
    $loginLink = ""; // Giriş yap linkini görünmez yap
    $signupLink = ""; // Kayıt ol linkini görünmez yap
}

// Aracı getir
if(isset($_GET['arac_id'])) {
    $arac_id = $_GET['arac_id'];
    
    $sql = "SELECT * FROM araclar WHERE Arac_id=$arac_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $arac = $result->fetch_assoc();

        // Aracın adını ve modelini al
        $secilen_arac_ad_model = $arac['Arac_marka'] . ' ' . $arac['Arac_model'];
    } else {
        // Arac bulunamadı, hata mesajı gösterilebilir
        echo "Arac bulunamadı.";
        exit;
    }
} else {
    // Arac ID belirtilmemiş, hata mesajı gösterilebilir
    echo "Arac ID belirtilmemiş.";
    exit;
}

$gunluk_bedel = $arac['Arac_gunluk_ucret'];

$gunsayisi = $_SESSION['gun_sayisi'];
if ($gunsayisi == 0) {
    $gunsayisi = 1;
}else {
    $gunsayisi += 1; // Başlangıç tarihinin de dahil edilmesi gerekiyor
}

$toplam_bedel = $arac['Arac_gunluk_ucret'] * $gunsayisi;

// Kullanıcının kayıtlı kartlarını alalım
if ($KullaniciID != 0) {
    $KartlarQuery = "SELECT * FROM kartlar WHERE kullanici_id = $KullaniciID";
    $KartlarResult = $conn->query($KartlarQuery);
} else {
    header("Location: login.php");
}

// Form gönderildiğinde rezervasyonu ekle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kirala'])) {
    $kart_id = $_POST['kayitli_kart'];
    $toplam_ucret = $_POST['toplam_ucret'];
    $baslangic_tarihi = $_POST['baslangic_tarihi'];
    $bitis_tarihi = $_POST['bitis_tarihi'];
    $alis_sube_id = $_POST['alis_sube_id'];
    $varis_sube_id = $_POST['varis_sube_id'];
    $rezervasyon_durumu = 1;

    // Eğer kullanıcı yeni bir kart ekliyorsa, kart bilgilerini al
    if (!empty($_POST['kart_ad_soyad']) && !empty($_POST['kart_numarasi']) && !empty($_POST['son_kullanma_tarihi']) && !empty($_POST['cvv'])) {
        $kart_ad_soyad = $_POST['kart_ad_soyad'];
        $kart_numarasi = $_POST['kart_numarasi'];
        $son_kullanma_tarihi = $_POST['son_kullanma_tarihi'];
        $cvv = $_POST['cvv'];
        $kart_id = ""; // Yeni kart eklenirken kart_id boş olarak kalacak

        // Yeni kart bilgilerini veritabanına ekle
        $kartEkleStmt = $conn->prepare("INSERT INTO kartlar (kullanici_id, kart_ad_soyad, kart_numarasi, son_kullanma_tarihi, cvv) VALUES (?, ?, ?, ?, ?)");
        $kartEkleStmt->bind_param("issss", $KullaniciID, $kart_ad_soyad, $kart_numarasi, $son_kullanma_tarihi, $cvv);
        $kartEkleStmt->execute();
        $kart_id = $kartEkleStmt->insert_id; // Yeni eklenen kartın ID'sini al
        $kartEkleStmt->close();
    }

    // Rezervasyon bilgilerini ekle
    $stmt = $conn->prepare("INSERT INTO rezervasyon (kullanici_id, arac_id, baslangic_tarihi, bitis_tarihi, toplam_ucret, kart_id, alis_sube_id, varis_sube_id, rezervasyon_durumu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdiiii", $KullaniciID, $arac_id, $baslangic_tarihi, $bitis_tarihi, $toplam_ucret, $kart_id, $alis_sube_id, $varis_sube_id, $rezervasyon_durumu);

    if ($stmt->execute()) {
        // Rezervasyon başarılı olduğunda
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Rezervasyon Başarılı",
            text: "Rezervasyonunuz başarıyla tamamlandı.",
            showConfirmButton: false,
            timer: 2000 // 2 saniye sonra otomatik kapanacak
        }).then(function() {
            // İşlem tamamlandıktan sonra yönlendirme yapabilirsiniz
            window.location.href = "profil.php";
        });
        </script>';
    } else {
        // Rezervasyon başarısız olduğunda
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Rezervasyon Başarısız",
            text: "Rezervasyon işlemi gerçekleştirilemedi. Lütfen tekrar deneyin."
        });
        </script>';
    }

    $stmt->close();
}
// Alış şubesini belirle
if(isset($_GET['sube']) && !empty($_GET['sube'])) {
    $alis_sube = $_GET['sube'];
    
    $sql = "SELECT * FROM subeler WHERE Sube_id=$alis_sube";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $sube = $result->fetch_assoc();

        // Alış şube adını al
        $alis_sube_ad = $sube['Sube_adi'];
        
    } else {
        // Şube bulunamadı, hata mesajı gösterilebilir
        echo "Alış şube bulunamadı.";
        exit;
    }
} else {
    // Alış şube parametresi belirtilmemiş veya boş, hata mesajı gösterilebilir
    echo "Alış şube belirtilmemiş veya boş.";
    exit;
}

// Varış şubesi (varsayılan olarak alış şubesi ile aynı)
if(isset($_GET['varis_sube']) && !empty($_GET['varis_sube'])) {
    $varis_sube = $_GET['varis_sube'];
    $sql = "SELECT * FROM subeler WHERE Sube_id=$varis_sube";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sube = $result->fetch_assoc();

        $varis_sube_ad = $sube['Sube_adi'];
    }
} else {
    // Eğer varış şubesi belirtilmemişse, alış şubesini varsayılan olarak kullan
    $varis_sube_ad = $alis_sube_ad;
}

// Tarih aralığı
$baslangic_tarihi = isset($_GET['baslangic_tarihi']) ? $_GET['baslangic_tarihi'] : "Belirtilmedi";
$bitis_tarihi = isset($_GET['bitis_tarihi']) ? $_GET['bitis_tarihi'] : "Belirtilmedi";
$tarih_araligi = $baslangic_tarihi . ' - ' . $bitis_tarihi;

// Adım
$adim = isset($_GET['adim']) ? $_GET['adim'] : 3; 

// Aktif tik işaretini oluştur
function aktifTik($hedefAdim, $simdikiAdim) {
    if ($hedefAdim == $simdikiAdim) {
        return '<span class="tik">&#10003;</span>';
    } else {
        return '';
    }
}

$gunluk_bedel = $arac['Arac_gunluk_ucret'];


$gunsayisi = $_SESSION['gun_sayisi'];
if ($gunsayisi == 0) {
    $gunsayisi = 1;
}else {
    $gunsayisi += 1; // Başlangıç tarihinin de dahil edilmesi gerekiyor
}

$toplam_bedel = $arac['Arac_gunluk_ucret'] * $gunsayisi;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/rezervasyon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
    <title>Araç Kiralama</title>
</head>
<body>
    <!-- Navbar -->
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
    <li class="<?php echo $adim == 2 ? 'active' : ''; ?>"><?php echo aktifTik(2, $adim); ?> Seçilen Araç: <?php echo $secilen_arac_ad_model; ?></li>
<li class="<?php echo $adim == 3 ? 'active' : ''; ?>"><?php echo aktifTik(3, $adim); ?> Ödeme Bilgileri</li>
        <!-- Diğer adımlar buraya eklenebilir -->
    </ul>
</nav>

<div class = "containertop">
    <div class="container">
        <h2 class="mb-4">Rezervasyon Yap</h2>
        <div class="row">
            <!-- Sol Tarafta Kart Seçimi -->
            <div class="col-md-6">
                <form id="rezervasyonForm" action="rezervasyon.php?arac_id=<?php echo $arac_id; ?>&sube=<?php echo $_GET['sube']; ?>&varis_sube=<?php echo $_GET['varis_sube']; ?>&baslangic_tarihi=<?php echo $_GET['baslangic_tarihi']; ?>&bitis_tarihi=<?php echo $_GET['bitis_tarihi']; ?>" method="POST" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="kayitli_kart">Kayıtlı Kart Seçin:</label>
                        <select class="form-control" id="kayitli_kart" name="kayitli_kart" onchange="disableOtherInput(this)">
                            <option value="">Kart Seçin</option>
                            <?php
                            if ($KartlarResult->num_rows > 0) {
                                while ($kart = $KartlarResult->fetch_assoc()) {
                                    echo "<option value='" . $kart["kart_id"] . "'>" . $kart["kart_numarasi"] . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Kayıtlı kart bulunmamaktadır.</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h4>Toplam Ücret: <?php echo $toplam_bedel; ?> ₺</h4>
                            <input type="hidden" name="toplam_ucret" value="<?php echo $toplam_bedel; ?>">
                            <input type="hidden" name="arac_id" value="<?php echo $arac_id; ?>">
                            <input type="hidden" name="baslangic_tarihi" value="<?php echo $_GET['baslangic_tarihi']; ?>">
                            <input type="hidden" name="bitis_tarihi" value="<?php echo $_GET['bitis_tarihi']; ?>">
                            <input type="hidden" name="alis_sube_id" value="<?php echo $_GET['sube']; ?>">
                            <input type="hidden" name="varis_sube_id" value="<?php echo $_GET['varis_sube']; ?>">
                        </div>
                    </div>
                    <!-- Rezervasyon Butonu -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="kirala" class="btn btn-primary" name="kirala">Rezervasyon Yap</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Sağ Tarafta Yeni Kart Ekleme -->
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">Yeni Kart Ekle</h5>
                    <div class="card-body">
                        <form id="yeniKartEkleForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="form-group">
                                <label for="kart_ad_soyad">Kart Sahibi Adı Soyadı:</label>
                                <input type="text" class="form-control" id="kart_ad_soyad" name="kart_ad_soyad">
                            </div>
                            <div class="form-group">
                                <label for="kart_numarasi">Kart Numarası:</label>
                                <input type="text" class="form-control" id="kart_numarasi" name="kart_numarasi" required oninput="formatKartNumarasi(this)">
                            </div>
                            <div class="form-group">
                                <label for="son_kullanma_tarihi">Son Kullanma Tarihi:</label>
                                <input type="date" class="form-control" id="son_kullanma_tarihi" name="son_kullanma_tarihi">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV:</label>
                                <input type="text" class="form-control" id="cvv" name="cvv">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <script>
        function disableOtherInput(selectElement) {
            var otherInput = document.getElementById('yeniKartEkleForm');
            if (selectElement.value !== '') {
                otherInput.querySelectorAll('input').forEach(function (input) {
                    input.disabled = true;
                });
            } else {
                otherInput.querySelectorAll('input').forEach(function (input) {
                    input.disabled = false;
                });
            }
        }

        function validateForm() {
            var kayitliKart = document.getElementById('kayitli_kart').value;
            var kartAdSoyad = document.getElementById('kart_ad_soyad').value;
            var kartNumarasi = document.getElementById('kart_numarasi').value;
            var sonKullanmaTarihi = document.getElementById('son_kullanma_tarihi').value;
            var cvv = document.getElementById('cvv').value;

            if (kayitliKart === '' && (kartAdSoyad === '' || kartNumarasi === '' || sonKullanmaTarihi === '' || cvv === '')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kart Bilgisi Eksik',
                    text: 'Lütfen kayıtlı bir kart seçin veya yeni kart bilgilerini eksiksiz doldurun.'
                });
                return false;
            }
            return true;
        }
    </script>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="footer-container text-center">
            <span class="text-muted">Araç Kiralama &copy; 2024. Tüm hakları saklıdır.</span>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
    <script type="text/javascript" src="js/arac.js"></script>
    <script type="text/javascript" src="js/logout.js"></script>
    <script type="text/javascript" src="js/tarih.js"></script>
    <script type="text/javascript" src="js/kart.js"></script>
    <script type="text/javascript" src="js/rezervasyon.js"></script>
</body>
</html>
