
    $('#sube').change(function(){
        var sube_id = $(this).val();
        $.ajax({
            url: 'bosaraclar.php',
            type: 'post',
            data: {sube_id: sube_id},
            success:function(response){
                $('#araclar').html(response);
            }
        });
    });

