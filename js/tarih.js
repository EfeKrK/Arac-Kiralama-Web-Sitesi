$(document).ready(function(){
    $.datetimepicker.setLocale('tr');
    
    // Başlangıç tarihi datetimepicker
    $('#baslangic_tarihi').datetimepicker({
        format:'y-m-d',
        timepicker:false,
        minDate: 0,
        step: 30,
        scrollMonth : false,
        scrollInput : false,
        onClose: function(selectedDate){
            // Başlangıç tarihi seçildiğinde bitiş tarihi input'unu aç
            $('#bitis_tarihi').prop('disabled', false);
            // Başlangıç tarihi değiştiğinde bitiş tarihine geçmiş tarihi seçmesini engelle
            $('#bitis_tarihi').datetimepicker('setOptions', {minDate: selectedDate});
        },
        onChangeDateTime:function(dp,$input){
            $('#bitis_tarihi').datetimepicker({minDate: $input.val()}); // Başlangıç tarihinden sonraki tarihlerin seçilebilir olması
            $('#bitis_tarihi').val(''); // Başlangıç tarihi değiştirildiğinde bitiş tarihini sıfırlama
        },
    });

    // Bitiş tarihi datetimepicker
    $('#bitis_tarihi').datetimepicker({
        format:'y-m-d',
        timepicker:false,
        minDate: 0,
        step: 30,
        scrollMonth : false,
        scrollInput : false,
        onClose: function(selectedDate){
            // Bitiş tarihi seçildiğinde başlangıç tarihini ileri atan kullanıcıya uyarı ver
            var baslangicTarihi = $('#baslangic_tarihi').val();
            if(selectedDate < baslangicTarihi){
                alert("Başlangıç tarihi bitiş tarihinden büyük olamaz!");
                $('#bitis_tarihi').val('');
            }
        }
        
    });


});

// Tarihi "gün.ay.yıl" formatına dönüştüren yardımcı işlev
function formatDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    
    // Gün ve ay tek haneli ise başlarına sıfır ekleme
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    
    return day + '.' + month + '.' + year;
}

// Tarih için kontrol
document.getElementById('baslangic_tarihi').addEventListener('change', function() {
    var baslangicTarihi = new Date(this.value);
    var bitisTarihi = new Date(document.getElementById('bitis_tarihi').value);

    if (baslangicTarihi > bitisTarihi) {
        alert("Başlangıç tarihi, bitiş tarihinden büyük olamaz veya bitiş tarihine eşit olmalıdır!");
        this.value = '';
    }
});