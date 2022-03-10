<?php


$server = '192.168.1.243\SQLEXPRESS';

// Connect to MSSQL
$link = mssql_connect($server, 'sa', '!q$\$sql2014');

if (!$link) {
    die('Something went wrong while connecting to MSSQL');
}


/*

$serverName = "192.168.1.243"; //serverName\instanceName
$connectionInfo = array( "Database"=>"IQInvoiceKun2", "UID"=>"sa", "PWD"=>"!q$\$sql2014");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
echo "Here";
if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
*/


?>