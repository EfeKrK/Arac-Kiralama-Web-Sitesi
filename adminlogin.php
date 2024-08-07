<?php
include 'database.php';
include 'bootstrap.php';
session_start();




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $kullaniciadi = $_POST['kullaniciadi'];
    $sifre = $_POST['sifre'];

    // Veritabanında bu kullanıcıyı kontrol et
    $sql = "SELECT * FROM admins WHERE kullaniciadi = '$kullaniciadi' AND sifre = '$sifre'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $adminid = $row['adminid'];

         // Şifre kontrolü
         if (password_verify($sifre, $row['sifre']) || $kullaniciadi == $row['kullaniciadi']) {
             // Giriş başarılı, oturumu başlat
             $_SESSION['adminid'] = $row['adminid'];
                $_SESSION['adminid'] = $adminid;

                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Başarılı!",
                        text: "Giriş başarılı, admin paneline yönlendiriliyorsunuz.",
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        didClose: () => {
                            window.location.href = "AdminSayfalari/AdminSayfasi.php";
                        }
                    });
                </script>';
                 
                exit();
         }  

            
            
        } else {
            // Hatalı giriş, SweetAlert2 ile kullanıcıya uyarı göster
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Hata!",
                        text: "Hatalı şifre veya kullanıcı adı!",
                        confirmButtonText: "Tamam"
                    });
                </script>';
        }
    } 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<style>
        body {
            background-image: url("images/arkaplanimg.jpg"); /* Arka plan resminin URL'sini belirtin */
            background-repeat: no-repeat; /* Arka plan resminin tekrar etmemesini sağlar */
            background-size: cover; /* Arka plan resminin body'ye tam sığdırılmasını sağlar */
            background-attachment: fixed;
        }

                .logo {
            
            margin-left: -50px;
            width: 450px; /* Resmin genişliği */
            height: auto; /* Resmin orijinal en-boy oranını korur */
            

        }

        .container {
            width: 420px;
            margin: auto;
            background-color: rgb(63, 85, 136, 0.8);
            color: rgb(56, 44, 44);
            border-radius: 10px;
            padding: 30px 40px;
            margin-top: -44%;
            transition: transform 0.5s ease-in-out; /* Geçiş efekti için transform özelliği, daha yavaş ve yumuşak */
        }

        .container:hover {
            transform: scale(1.05); /* Mouse ile üstüne gelindiğinde büyüme efekti */
        }
        
        
        .baslik {
            margin: 0;
            font-size: 24px;
            color: rgb(203,165,108);
        }
        .input-box {
            margin: 15px 0;
        }

        .ustkisim {
            background-image: url('kanyon.jpg');
            height: 100vh;
            width: 100%;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            border:none;
            outline: none;
            border: 2px solid rgba(0, 0, 0, 0.2); /* Renk değeri düzeltildi */
            border-radius: 40px;
            font-size: 16px;
            color: black;
            padding: 0 45px 0 20px; /* Sağa ve sola padding eklendi */
        }
        .btn {
            background-color: #5cb85c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            margin-bottom:15px;
        }

        .btn:hover {
            background-color: #5cb85c;
            color: #fff;
            
        }

        .link {
            display: block;
            margin: 10px 0;
            color: #007bff;
            cursor: pointer;
            text-decoration: none;
            color:white;
        }
        .link:hover {
            text-decoration: underline;
            color:white;
        }
        .hidden {
            display: none;
        }
        .mesaj {
            margin-top: 20px;
            font-size: 14px;
            color:white;
        }
        .mesaj a {
            color: white;
            text-decoration: none;
        }
        .mesaj a:hover {
            text-decoration: underline;
        }
        .container .input-box{
        position: relative; /* Eklendi */
        width: 100%;
        height: 50px;
        margin: 30px 0;
    }
    </style>
<body>
  <div class="ustkisim">
    <!-- Navbar kodları buraya eklenebilir -->
  </div>
  <script>
        function showForgotPasswordForm() {
            document.getElementById('forgot-password-form').classList.remove('hidden');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
<?php
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        echo "<script>
            Swal.fire({
                icon: '" . $message['type'] . "',
                title: '" . ($message['type'] == 'success' ? 'Başarılı!' : 'Hata!') . "',
                text: '" . $message['text'] . "',
                confirmButtonText: 'Tamam'
            });
        </script>";
        unset($_SESSION['message']);
    }
    ?>
    <div class="tamamı">
    <div class="container">
        <form action="adminlogin.php" method="post">
            <div class="header">
                <img src="images/CarDuckLogo.png" alt="Resim" class="logo">
                <h1 class="baslik">YÖNETİCİ GİRİŞİ</h1>
            </div>
            <div class="input-box">
                <input placeholder="Kullanıcı Adı" id="kullaniciadi" name="kullaniciadi" required>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Şifre" id="sifre" name="sifre" required>
            </div>
            <button type="submit" class="btn">Giriş Yap</button>
        </form>

        <!--
        <a class="link" onclick="showForgotPasswordForm()">Şifremi Unuttum</a>

        
        <form id="forgot-password-form" action="mailgonder.php" method="POST" class="hidden">
            <div class="input-box">
                <input type="email" name="eposta" placeholder="Eposta giriniz" required />
            </div>
            <button class="btn" type="submit">GÖNDER</button>
        </form>
        

        <p class="mesaj">Üye Değil Misin? - <a href="register.php" class="hesapolustur">Hesap Oluştur</a></p>
        -->
        <p class="mesaj"><a href="login.php" class="admingiris">Müşteri Girişine Dön</a></p>
    </div>
    </div>
    
</body>
</html>
