<script>
    // Blog linkine tıklandığında sayfanın ilgili bölümüne kaydırma
    document.querySelector('a[href="#blog"]').addEventListener('click', function(event) {
        event.preventDefault(); // Sayfanın yeniden yüklenmesini engelle
        document.getElementById('blog').scrollIntoView({ behavior: 'smooth' }); // Blog bölümüne kaydır
    });
</script>