<?php
/**
 * Creator: Tim Weißenfels
 * Date: 08.12.2018
 * Time: 20:21
 */

function inset_val(&$Zaehler,&$conn) {

    $Name = snmp2_walk("192.168.0.115", "public", ".1.3.6.1.2.1.1.5");
    $Ort  = snmp2_walk("192.168.0.115", "public", ".1.3.6.1.2.1.1.6");

    $Name = explode(" ",$Name[0]);
    $Ort = explode(" ",$Ort[0]);

    $Zaehler++;
    $sql = "use drucker; INSERT INTO`test`(`Anzahl_Drucker`, `Name`, `ORT`)VALUES($Zaehler,'$Name[1]','$Ort[1]');";

    if ($conn->multi_query($sql) === TRUE) {
        echo "\nHat funktioniert";
    } else {
        echo "\nNe hat nicht" . $conn->error;
    }

    return $Zaehler;
}

$servername = "localhost";
$username = "root";
$password = "";
$Zaehler = 0;

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";


$sql = "use drucker;TRUNCATE TABLE `test`;";

if ($conn->multi_query($sql) === TRUE) {
    echo "\nHat funktioniert";
} else {
    echo "\nNe hat nicht" . $conn->error;
}
$conn->next_result();
#mysqli_free_result($test);
inset_val($Zaehler,$conn);
$conn->next_result();
inset_val($Zaehler,$conn);
$conn->next_result();
inset_val($Zaehler,$conn);
$conn->next_result();
inset_val($Zaehler,$conn);

$conn->close();
