
    // Rezervasyon başarılı alert
    function rezervasyonBasarili() {
        Swal.fire({
            icon: 'success',
            title: 'Rezervasyon Başarılı',
            text: 'Rezervasyonunuz başarıyla tamamlandı.',
            showConfirmButton: false,
            timer: 2000 // 2 saniye sonra otomatik kapanacak
        }).then(function() {
            // İşlem tamamlandıktan sonra yönlendirme yapabilirsiniz
            window.location.href = 'profil.php';
        });
    }

    // Rezervasyon başarısız alert
    function rezervasyonBasarisiz() {
        Swal.fire({
            icon: 'error',
            title: 'Rezervasyon Başarısız',
            text: 'Rezervasyon işlemi gerçekleştirilemedi. Lütfen tekrar deneyin.'
        });
    }
