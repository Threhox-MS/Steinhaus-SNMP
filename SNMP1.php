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

$conn_write = new mysqli($servername, $username, $password);
$conn_read  = new mysqli($servername, $username, $password);

if (!$conn_write->connect_error && !$conn_read->connect_error) {
    echo "Connected successfully";
}
else
die("Connection failed: " . $conn_write->connect_error);


$sql = "use drucker;TRUNCATE TABLE `test`;";

if ($conn_write->multi_query($sql) === TRUE) {
    echo "\nHat funktioniert";
} else {
    echo "\nNe hat nicht" . $conn_write->error;
}
$conn_write->next_result();
inset_val($Zaehler,$conn_write);
$conn_write->next_result();
inset_val($Zaehler,$conn_write);
$conn_write->next_result();
inset_val($Zaehler,$conn_write);


$conn_write->close();
