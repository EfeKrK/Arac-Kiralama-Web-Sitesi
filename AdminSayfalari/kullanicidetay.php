<?php
include 'database.php';

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['adminid'])) {
    header("Location: ../adminlogin.php"); // Giriş sayfasına yönlendir
    exit();
}

// Kullanıcı bilgilerini al
if(isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT * FROM kullanici WHERE Kullanici_id = $user_id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}

// Kullanıcının rezervasyonlarını ve kartlarını sil
if(isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    // Kullanıcının rezervasyonlarını sil
    $delete_reservations_query = "DELETE FROM rezervasyon WHERE kullanici_id = $delete_id";
    mysqli_query($conn, $delete_reservations_query);
    // Kullanıcının kartlarını sil
    $delete_cards_query = "DELETE FROM kartlar WHERE kullanici_id = $delete_id";
    mysqli_query($conn, $delete_cards_query);
    // Kullanıcıyı sil
    $delete_query = "DELETE FROM kullanici WHERE Kullanici_id = $delete_id";
    mysqli_query($conn, $delete_query);
    // Sayfayı yenile
    header("Location: KullanicilariYonet.php");
    exit();
}

// Kullanıcı bilgilerini güncelle
if(isset($_POST['update'])) {
    $update_id = $_POST['update_id'];
    $new_name = $_POST['new_name'];
    $new_surname = $_POST['new_surname'];
    $new_email = $_POST['new_email'];
    $new_phone = $_POST['new_phone'];
    
    $update_query = "UPDATE kullanici SET Kullanici_isim = '$new_name', Kullanici_soyisim = '$new_surname', Kullanici_eposta = '$new_email', kullanici_telefon = '$new_phone' WHERE Kullanici_id = $update_id";
    mysqli_query($conn, $update_query);
    // Sayfayı yenile
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Detayları</title>
    <link rel="stylesheet" href="css/AdminSayfa.css"> 
    <link rel="stylesheet" href="css/kullanicidetay.css"> 
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
                <li><a href="RezervasyonYonetimi.php">Rezervasyon Yönetimi</a></li>
                <li><a href="KullanicilariYonet.php" class="active">Kullanıcıları Yönet</a></li>
                <li class = "cikisyap"><a href="adminlogout.php">Çıkış Yap</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h2>Kullanıcı Detayları</h2>
        <?php if(isset($user)): ?>
        <form method="post">
            <input type="hidden" name="update_id" value="<?php echo $user['Kullanici_id']; ?>">
            <label for="new_name">İsim:</label>
            <input type="text" id="new_name" name="new_name" value="<?php echo $user['Kullanici_isim']; ?>"><br>
            <label for="new_surname">Soyisim:</label>
            <input type="text" id="new_surname" name="new_surname" value="<?php echo $user['Kullanici_soyisim']; ?>"><br>
            <label for="new_email">E-posta:</label>
            <input type="email" id="new_email" name="new_email" value="<?php echo $user['Kullanici_eposta']; ?>"><br>
            <label for="new_phone">Telefon:</label>
            <input type="tel" id="new_phone" name="new_phone" value="<?php echo $user['Kullanici_telefon']; ?>"><br>
            <input type="submit" name="update" value="Bilgileri Güncelle">
        </form>
        <form method="post">
            <input type="hidden" name="delete_id" value="<?php echo $user['Kullanici_id']; ?>">
            <input type="submit" name="delete" value="Kullanıcıyı Sil" class="silbuton">
        </form>
        <p>Kullanıcı silindiğinde ona ait rezervasyonlar ve kartlar tamamen silinir!</p>
        <?php else: ?>
        <p>Kullanıcı bulunamadı.</p>
        <?php endif; ?>
    </div>
</body>
</html>

