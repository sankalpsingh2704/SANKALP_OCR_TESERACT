<?php
/*--------------------------------------------------------------------------------------------
|    @desc:        processupload.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
include('../config.php');    //include of db config file
$target_dir = "uploads/";
$filename = rand(10000,99999) . ".pdf";
$target_file = "/var/www/html/iqinvoiceadmin/" . $target_dir . $filename;
//echo $target_file;
$uploadOk = 1;
//print_r($_POST);
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if pdf file is a actual pdf or fake pdf
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is a PDF - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not PDF.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "pdf" && $imageFileType != "PDF") {
    echo "Sorry, only PDF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". $filename . " has been uploaded.";
        if(isset($_POST["templid"]))
        {
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

          $sql = "UPDATE `templatemanager` SET `templinvname`='" . $filename . "' WHERE `templid`= " . $_POST['templid'] . "";
          echo $sql;
          if (mysqli_query($conn, $sql)) {
              echo "Record updated successfully";
          } else {
              echo "Error: " . $sql . "<br>" . mysqli_error($conn);
          }

          mysqli_close($conn);
          header("Location: ../index.php");
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
        echo "The file " . $filename . " has not been uploaded.";
        print_r($_FILES);
    }
}
?>
