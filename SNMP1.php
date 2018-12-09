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
        echo "\nZeile wurde hinzugefügt";
    } else {
        echo "\nNZeile wurde nicht hinzugefügt" . $conn->error;
    }

    return $Zaehler;
}
function get_conn_write($s_name,$u_name,$pwd) {
    $conn_write = new mysqli($s_name, $u_name, $pwd);
    if ($conn_write->connect_error) {
        die("Connection failed: " . $conn_write->connect_error);
    }
    else
        echo "\nVerbunden";

    $sql_write = "use drucker;TRUNCATE TABLE `test`;";

    if ($conn_write->multi_query($sql_write) === TRUE) {
        echo "\nTabelle gelöscht";
    } else {
        echo "\nNe hat nicht gelöscht" . $conn_write->error;
    }
    $conn_write->next_result();
    return $conn_write;
}
function get_conn_read($s_name,$u_name,$pwd){
    $conn_read  = new mysqli($s_name, $u_name, $pwd);
    if ($conn_read->connect_error) {
        die("Connection failed: " . $conn_read->connect_error);
    }
    else
        echo "\nVerbunden";

    $sql_read  = "use drucker;";

    $conn_read->query($sql_read);
    if ($conn_read->query($sql_read) === TRUE) {
        echo "\n";
    } else {
        echo "\nKonnte nicht gelöscht werden" . $conn_read->error;
    }
    $conn_read->next_result();
    return $conn_read;
}

$servername = "localhost";
$username = "root";
$password = "";
$zaehler = 0;

$conn_write = get_conn_write($servername,$username,$password);
$conn_read = get_conn_read($servername,$username,$password);


$conn_write->next_result();
inset_val($zaehler,$conn_write);

$sql_read  = "SELECT * FROM Abfrage;";

$read_result_count_rows = $conn_read->query($sql_read);

$zaehler = $read_result_count_rows->num_rows;

echo "\n\n\n" . $zaehler;

$conn_write->close();
