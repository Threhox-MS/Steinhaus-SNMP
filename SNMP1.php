<?php

/**
 * Creator: Tim Weißenfels
 * Date: 08.12.2018
 * Time: 20:21
 */

#FUNCTIONS
function inset_val(&$Zaehler,&$conn,$ip_addr,$object_id1,$object_id2) {

    $Name = snmp2_walk($ip_addr, "public", $object_id1);
    $Ort  = snmp2_walk($ip_addr, "public", $object_id2);

    $Name = explode(" ",$Name[0]);
    $Ort = explode(" ",$Ort[0]);

    $Zaehler++;
    $sql = "use drucker; INSERT INTO`test`(`Anzahl_Drucker`, `Name`, `ORT`)VALUES($Zaehler,'$Name[1]','$Ort[1]');";

    if ($conn->multi_query($sql) === TRUE) {
        #echo "\nZeile wurde hinzugefügt";
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
        echo "\nFEHLER" . $conn_read->error;
    }
    $conn_read->next_result();
    return $conn_read;
}
function data_from_Abfrage_to_test(&$conn) {
    $sql = "SELECT * FROM typzuoid";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            #echo "\nName: " . $row["Name"]. " - Ort: " . $row["ORT"]. " - IP_Adresse " . $row["IP_Adresse"];
            inset_val($count,$conn,$row["IP_Adresse"],$row["Name"], $row["ORT"]);
            $conn->next_result();
        }
    } else {
        echo "0 results";
    }
    return $result->num_rows;
}
#FUNCTIONS

#VARIABLEN
$servername = "localhost";
$username = "root";
$password = "";
$zaehler = 0;
#VARIABLEN

#ESTABLISH CONNECTIONS
$conn_write = get_conn_write($servername,$username,$password);
$conn_read  = get_conn_read($servername,$username,$password);
#ESTABLISH CONNECTIONS

#UPDATE TABLE typzuoid column IP_ADRESSE
$sql = "UPDATE typzuoid INNER JOIN resolve
	SET typzuoid.IP_Adresse = resolve.IP_Adresse
	WHERE typzuoid.Typ = resolve.Typ_Name;";
$conn_read->query($sql);
#UPDATE TABLE typzuoid column IP_ADRESSE

$zaehler = data_from_Abfrage_to_test($conn_read);

#UGLY HTML
echo "<html lang=\"de\">

<head>
    <style>
        #customers {
            font-family: \"Trebuchet MS\", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td, #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even){background-color: #f2f2f2;}

        #customers tr:hover {background-color: #ddd;}

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>

<table id=\"customers\">
    <tr>
        <th>Anzahl</th>
        <th>Name</th>
        <th>Ort</th>";
        $sql = "SELECT * FROM test;";
        $result = $conn_write->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>" . "<td>" . $row["Anzahl_Drucker"]. "</td>" . "<td>" . $row["Name"]. "</td>" . "<td>" . $row["ORT"]. "</td>" . "</tr>";
    }
} else {
    echo "0 results"; }
        echo"
    </tr>

</table>

</body>

</body>
</html>";
#UGLY HTML

#CLOSE CONNECTIONS
$conn_write->close();
$conn_read->close();
#CLOSE CONNECTIONS
