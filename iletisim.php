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
        $welcomeMessage = "<h1 id='hosgeldin' class='welcome-message'>Hoşgeldiniz, " . $isim . "</h1>";
        $profil = "<a class='nav-link' href='profil.php'>Profil</a>";
    }

    $logoutLink = "<a class='nav-link' href='logout.php'>Çıkış Yap</a>";
    $loginLink = ""; // Giriş yap linkini görünmez yap
    $signupLink = ""; // Kayıt ol linkini görünmez yap
}
// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $adSoyad = $_POST['adSoyad'];
    $eposta = $_POST['eposta'];
    $telefon = $_POST['telefon'];
    $konu = $_POST['konu'];
    $mesaj = $_POST['mesaj'];

    // Veritabanına ekle
    $insertQuery = "INSERT INTO iletisim (adsoyad, eposta, telno, konu, mesaj) VALUES ('$adSoyad', '$eposta', '$telefon', '$konu', '$mesaj')";

    if ($conn->query($insertQuery) === TRUE) {
        echo "<p style='color: green; text-align: center;'>Mesajınız başarıyla gönderildi.</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Hata: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/iletisim.css">
    <link rel="stylesheet" href="css/footer.css">
    <br>
    <br>
    <br><br>
    <title>İletişim</title>
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
                        <a class="nav-link active" href="iletisim.php">İletişim</a>
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
        
               
    <!-- Ana içerik -->
    <div class="main-container mt-5">
        
        <div class="row justify-content-center">
        
            <div class="col-md-6">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1 class="text-center mb-4">İletişim Formu</h1>
                    <div class="form-group">
                        <label for="adSoyad">Ad Soyad:</label>
                        <input type="text" id="adSoyad" name="adSoyad" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="eposta">E-posta:</label>
                        <input type="email" id="eposta" name="eposta" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="telefon">Telefon:</label>
                        <input type="tel" id="telefon" name="telefon" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="konu">Konu:</label>
                        <input type="text" id="konu" name="konu" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mesaj">Mesaj:</label>
                        <textarea id="mesaj" name="mesaj" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Gönder</button>
                </form>
            </div>
        </div>
    </div>

   <!-- Footer -->
   <footer class="footer mt-auto py-3 bg-black">
        <div class="footer-container text-center">
            <span class="text-muted">Araç Kiralama &copy; 2024. Tüm hakları saklıdır.</span>
        </div>
    </footer>

    


    <script type="text/javascript" src="js/arac.js"></script>
</body>
</html>