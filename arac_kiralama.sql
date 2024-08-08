-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 07 Ağu 2024, 20:48:37
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `carduckc_arac_kiralama`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `adminid` int(11) NOT NULL,
  `kullaniciadi` varchar(45) DEFAULT NULL,
  `sifre` varchar(45) DEFAULT NULL,
  `isim` varchar(45) DEFAULT NULL,
  `soyisim` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`adminid`, `kullaniciadi`, `sifre`, `isim`, `soyisim`) VALUES
(4, 'admin', '123', 'admin1', 'admin');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `araclar`
--

CREATE TABLE `araclar` (
  `Arac_id` int(11) NOT NULL,
  `Arac_marka` varchar(50) DEFAULT NULL,
  `Arac_model` varchar(50) DEFAULT NULL,
  `Arac_yil` int(11) DEFAULT NULL,
  `Arac_renk` varchar(50) DEFAULT NULL,
  `Arac_gunluk_ucret` decimal(10,2) DEFAULT NULL,
  `sube_id` int(11) DEFAULT NULL,
  `Arac_Görsel` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `icerik` text NOT NULL,
  `olusturma_tarihi` timestamp NULL DEFAULT current_timestamp(),
  `resim` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `hakkimizda`
--

CREATE TABLE `hakkimizda` (
  `idhakkimizda` int(11) NOT NULL,
  `baslik` varchar(255) DEFAULT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `iletisim`
--

CREATE TABLE `iletisim` (
  `idiletisim` int(11) NOT NULL,
  `adsoyad` varchar(45) DEFAULT NULL,
  `eposta` varchar(45) DEFAULT NULL,
  `telno` varchar(45) DEFAULT NULL,
  `konu` varchar(45) DEFAULT NULL,
  `mesaj` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kartlar`
--

CREATE TABLE `kartlar` (
  `kart_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `kart_ad_soyad` varchar(255) DEFAULT NULL,
  `kart_numarasi` varchar(20) DEFAULT NULL,
  `son_kullanma_tarihi` date DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanici`
--

CREATE TABLE `kullanici` (
  `Kullanici_id` int(11) NOT NULL,
  `Kullanici_isim` varchar(50) DEFAULT NULL,
  `Kullanici_soyisim` varchar(50) DEFAULT NULL,
  `Kullanici_eposta` varchar(100) DEFAULT NULL,
  `Kullanici_sifre` varchar(100) DEFAULT NULL,
  `Kullanici_telefon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rezervasyon`
--

CREATE TABLE `rezervasyon` (
  `rezervasyon_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `arac_id` int(11) NOT NULL,
  `baslangic_tarihi` date NOT NULL,
  `bitis_tarihi` date NOT NULL,
  `toplam_ucret` decimal(10,2) NOT NULL,
  `kart_id` int(11) DEFAULT NULL,
  `alis_sube_id` int(11) DEFAULT NULL,
  `varis_sube_id` int(11) DEFAULT NULL,
  `rezervasyon_tarihi` timestamp NULL DEFAULT current_timestamp(),
  `rezervasyon_durumu` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `subeler`
--

CREATE TABLE `subeler` (
  `Sube_id` int(11) NOT NULL,
  `Sube_adi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`adminid`);

--
-- Tablo için indeksler `araclar`
--
ALTER TABLE `araclar`
  ADD PRIMARY KEY (`Arac_id`),
  ADD KEY `sube_id` (`sube_id`);

--
-- Tablo için indeksler `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `hakkimizda`
--
ALTER TABLE `hakkimizda`
  ADD PRIMARY KEY (`idhakkimizda`);

--
-- Tablo için indeksler `iletisim`
--
ALTER TABLE `iletisim`
  ADD PRIMARY KEY (`idiletisim`);

--
-- Tablo için indeksler `kartlar`
--
ALTER TABLE `kartlar`
  ADD PRIMARY KEY (`kart_id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `kullanici`
--
ALTER TABLE `kullanici`
  ADD PRIMARY KEY (`Kullanici_id`);

--
-- Tablo için indeksler `rezervasyon`
--
ALTER TABLE `rezervasyon`
  ADD PRIMARY KEY (`rezervasyon_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `alis_sube_id` (`alis_sube_id`),
  ADD KEY `varis_sube_id` (`varis_sube_id`);

--
-- Tablo için indeksler `subeler`
--
ALTER TABLE `subeler`
  ADD PRIMARY KEY (`Sube_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `adminid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `araclar`
--
ALTER TABLE `araclar`
  MODIFY `Arac_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `hakkimizda`
--
ALTER TABLE `hakkimizda`
  MODIFY `idhakkimizda` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `iletisim`
--
ALTER TABLE `iletisim`
  MODIFY `idiletisim` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kartlar`
--
ALTER TABLE `kartlar`
  MODIFY `kart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici`
--
ALTER TABLE `kullanici`
  MODIFY `Kullanici_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `rezervasyon`
--
ALTER TABLE `rezervasyon`
  MODIFY `rezervasyon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `subeler`
--
ALTER TABLE `subeler`
  MODIFY `Sube_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `araclar`
--
ALTER TABLE `araclar`
  ADD CONSTRAINT `araclar_ibfk_1` FOREIGN KEY (`sube_id`) REFERENCES `subeler` (`Sube_id`);

--
-- Tablo kısıtlamaları `kartlar`
--
ALTER TABLE `kartlar`
  ADD CONSTRAINT `kartlar_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`Kullanici_id`);

--
-- Tablo kısıtlamaları `rezervasyon`
--
ALTER TABLE `rezervasyon`
  ADD CONSTRAINT `rezervasyon_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`Kullanici_id`),
  ADD CONSTRAINT `rezervasyon_ibfk_2` FOREIGN KEY (`alis_sube_id`) REFERENCES `subeler` (`Sube_id`),
  ADD CONSTRAINT `rezervasyon_ibfk_3` FOREIGN KEY (`varis_sube_id`) REFERENCES `subeler` (`Sube_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
