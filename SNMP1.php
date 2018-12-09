<?php
/**
 * Creator: Tim Weißenfels
 * Date: 08.12.2018
 * Time: 20:21
 */

function inset_val(&$Zaehler,&$conn,$ip_addr,$object_id1,$object_id2) {

    $Name = snmp2_walk($ip_addr, "public", $object_id1);
    $Ort  = snmp2_walk($ip_addr, "public", $object_id2);

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
function data_from_Abfrage_to_test(&$conn) {
    $sql = "SELECT * FROM abfrage";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "\nName: " . $row["Name"]. " - Ort: " . $row["ORT"]. " - Ip-Adresse " . $row["IP-Adresse"]. "<br>";
            inset_val($count,$conn,$row["IP-Adresse"],$row["Name"], $row["ORT"]);
            $conn->next_result();
        }
    } else {
        echo "0 results";
    }
}

#VARIABLEN
$servername = "localhost";
$username = "root";
$password = "";
#VARIABLEN

#ESTABLISH CONNECTIONS
$conn_write = get_conn_write($servername,$username,$password);
$conn_read = get_conn_read($servername,$username,$password);
#ESTABLISH CONNECTIONS

data_from_Abfrage_to_test($conn_read);

#CLOSE CONNECTIONS
$conn_write->close();
$conn_read->close();
#CLOSE CONNECTIONS
