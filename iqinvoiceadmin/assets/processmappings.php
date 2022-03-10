<?php
include('../config.php');    //include of db config file
//print_r($_POST);
$values = arrayToCsv($_POST);
$values = str_replace(";\"Submit Query\"","",$values);
echo $values;

$servername = "localhost";
$username = "root";
$password = "iqss@123";
$dbname = "ocr_vendorengine";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "UPDATE `templatemanager` SET `templfields`='" . $values . "' WHERE templid='".$_POST['idoftemplate'] . "'";
echo $sql;
if (mysqli_query($conn, $sql)) {
    echo "New record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);

header("Location: ../index.php");
 ?>
