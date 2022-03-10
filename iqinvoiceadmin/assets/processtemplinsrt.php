<?php
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

$sql = "INSERT INTO `templatemanager` (`templname`, `templvendrid`) VALUES ('" . $_POST['templname'] . "','" . $_POST['vendorsel'] . "')";
echo $sql;
if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);

header("Location: ../index.php");
 ?>
