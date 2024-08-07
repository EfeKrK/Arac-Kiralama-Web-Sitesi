<?php
include 'database.php';
include 'bootstrap.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $KullaniciIsim = $_POST['Kullanici_isim'];
    $KullaniciSoyisim = $_POST['Kullanici_soyisim'];
    $KullaniciEposta = $_POST['Kullanici_eposta'];
    $KullaniciSifre = $_POST['Kullanici_sifre'];
    $Kullanicitelefon = $_POST['Kullanici_telefon'];

    $sql = "SELECT * FROM kullanici WHERE Kullanici_eposta = '$KullaniciEposta'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // E-posta adresi zaten kayıtlı, kullanıcıya mesaj göster
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    text: "Bu e-posta adresi zaten kayıtlı!",
                    confirmButtonText: "Tamam"
                });
            </script>';
    } else {
        // SQL sorgusunu hazırla ve veritabanına ekle
        $sql = "INSERT INTO kullanici (Kullanici_isim, Kullanici_soyisim, Kullanici_eposta, Kullanici_sifre, Kullanici_telefon)
                VALUES ('$KullaniciIsim', '$KullaniciSoyisim', '$KullaniciEposta', '$KullaniciSifre', '$Kullanicitelefon')";

        if ($conn->query($sql) === TRUE) {
            // Kayıt başarılı, kullanıcıya mesaj göster ve yönlendir
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Başarılı!",
                        text: "Kayıt başarılı, Girişe yönlendiriliyorsunuz...",
                        confirmButtonText: "Tamam",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "login.php";
                    });
                </script>';
        } else {
            // Kayıt başarısız, kullanıcıya mesaj göster
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Hata!",
                        text: "Kayıt işlemi sırasında bir hata oluştu!",
                        confirmButtonText: "Tamam"
                    });
                </script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
body {
            background-image: url("images/arkaplanimg.jpg"); /* Arka plan resminin URL'sini belirtin */
            background-repeat: no-repeat; /* Arka plan resminin tekrar etmemesini sağlar */
            background-size: cover; /* Arka plan resminin body'ye tam sığdırılmasını sağlar */
            background-attachment: fixed;
        }
    </style>
<body>

<br><br><br><br>
<div class="container mt-5">
    <form action="register.php" method="post">
        <div class="header text-center mb-4">
            <img src="images/CarDuckLogo.png" alt="Resim" class="logo">
            <h1 class="baslik">HOŞGELDİNİZ</h1>
        </div>
        <div class="input-box mb-3">
            <input type="text" class="form-control" placeholder="İsim" id="isim" name="Kullanici_isim" required>
        </div>
        <div class="input-box mb-3">
            <input type="text" class="form-control" placeholder="Soyisim" id="soyisim" name="Kullanici_soyisim" required>
        </div>
        <div class="input-box mb-3">
            <input type="email" class="form-control" placeholder="Eposta Adresi" id="eposta" name="Kullanici_eposta" required>
        </div>
        <div class="input-box mb-3">
            <input type="text" class="form-control" placeholder="Telefon (5** *** ** **)" id="telefon" name="Kullanici_telefon" required>
        </div>
        <div class="input-box mb-3">
            <input type="password" class="form-control" placeholder="Şifre" id="sifre" name="Kullanici_sifre" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Kayıt Ol</button>
    </form>
</div>

</body>
</html>
