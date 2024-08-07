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




// Blog gönderisi ID'sini al
$blogID = $_GET['id'];

// Veritabanından ilgili blog gönderisini al
$blogQuery = "SELECT * FROM blog WHERE id = $blogID";
$blogResult = $conn->query($blogQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/blogdetay.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <title>Araç Kiralama</title>

</head>
<body>
     
    <!-- Navbar -->
    <div class="custom-border ">
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
</div>
    <!-- Main Content -->
    <div class="main-container mt-5">
        
        
        <div class = "Blogdetayi">
        <h1 class="text-center mb-4">Blog Detayları</h1> 
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-5">
                <?php
                // Blog gönderisi varsa göster, yoksa hata mesajı göster
                if ($blogResult->num_rows > 0) {
                    $blog = $blogResult->fetch_assoc();
                    $blogTitle = $blog['baslik'];
                    $blogContent = $blog['icerik'];
                    $blogDate = $blog['olusturma_tarihi'];
                    $blogImage = base64_encode($blog['resim']); // Resmi base64 ile kodlayarak göster
                    ?>
                    <div class="blog-post">
                        <h2 class="blog-title"><?php echo $blogTitle; ?></h2>
                        
                        
                        <div class="blog-image">
                            <img src='data:image/jpeg;base64,<?php echo $blogImage; ?>' alt='<?php echo $blogTitle; ?>' class='img-fluid'>
                        </div>
                        <div class="blog-content">
                            <p><?php echo $blogContent; ?></p>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "<p class='error-message'>Bu blog gönderisi bulunamadı.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="footer-container text-center">
            <span class="text-muted">Araç Kiralama &copy; 2024</span>
        </div>
    </footer>
    <script type="text/javascript" src="js/arac.js"></script>
</body>
</html>
