<?php
include 'database.php';

$sube_id = $_POST['sube_id'];

$sql = "SELECT * FROM Araclar WHERE Arac_durum='Bos' AND sube_id=$sube_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<label for='arac'>Araç:</label>";
    echo "<select name='arac' id='arac'>";
    while($row = $result->fetch_assoc()) {
        echo "<option value='".$row['Arac_id']."'>".$row['Arac_marka']." ".$row['Arac_model']."</option>";
    }
    echo "</select>";
} else {
    echo "Seçilen şubede boş araç bulunamadı.";
}

$conn->close();
?>
