function formatKartNumarasi(input) {
    // Boşlukları ve tireleri kaldır
    var kartNumarasi = input.value.replace(/\s+/g, '').replace(/-/g, '');

    // Kart numarasını 16 karakterden fazla girilemeyecek şekilde kontrol et
    if (kartNumarasi.length > 16) {
        alert("Kredi kartı numarası 16 haneden uzun olamaz!");
        input.value = input.value.slice(0, 16); // 16 karakterden fazlasını kes
        return;
    }

    // Kart numarasını 4'ü 4'e gruplayarak biçimlendir
    var formatted = '';
    for (var i = 0; i < kartNumarasi.length; i++) {
        if (i % 4 == 0 && i > 0) {
            formatted += ' '; // Her 4 basamakta bir boşluk ekle
        }
        formatted += kartNumarasi.charAt(i);
    }

    // Biçimlendirilmiş kart numarasını giriş alanına yaz
    input.value = formatted;
}
