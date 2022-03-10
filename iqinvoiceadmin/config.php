<?php
/*--------------------------------------------------------------------------------------------
|    @desc:        config.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
$mysql_hostname = "localhost";  //your mysql host name
$mysql_user = "root";			//your mysql user name
$mysql_password = "iqss@123";			//your mysql password
$mysql_database = "ocr_vendorengine";	//your mysql database

$bd = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Opps some thing went wrong");
mysqli_select_db($bd, $mysql_database) or die("Error on database connection");



function mysqli_result($res,$row=0,$col=0){
  //return mysql result using mysql functions as this function doesnt exist in mysqli
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}

function returnfilename($id)
{
  $mysql_hostname = "localhost";  //your mysql host name
  $mysql_user = "root";			//your mysql user name
  $mysql_password = "iqss@123";			//your mysql password
  $mysql_database = "ocr_vendorengine";	//your mysql database
  $retval = '';
//write function to get the file name from template for any given id
  $conn = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
  $sql = "SELECT `templinvname` FROM templatemanager WHERE `templid`=" . $id;

  $templresult = mysqli_query($conn, $sql);
  if (mysqli_num_rows($templresult) > 0) {
  // output data of each row
    while($row = mysqli_fetch_assoc($templresult)) {
          $retval = $row['templinvname'];
    }
  }
return $retval;
}

function parsexmltodatabase($downloadfile)
{
  $rows = file($downloadfile);
  foreach($rows as $key => $row) {
    if(strpos($row, "dataarea") != false) {
        $row= str_replace("\"", "" , $row);
        $row= str_replace("/>", "" , $row);
        $row= str_replace("'", "" , $row);
        $row= str_replace(",", "" , $row);
        $row= str_replace(" t", "" , $row);
        $row= str_replace(" l", "" , $row);
        $row= str_replace(" w", "" , $row);
        $row= str_replace(" h", "" , $row);
        $row= str_replace(" f", "" , $row);
        $row= str_replace(" val", "" , $row);
        $filename = explode(".pdf.xml", $downloadfile);
        $filename[0] = str_replace("uploads/", "", $filename[0]);
        $filename[0] = str_replace("/var/www/html/iqinvoiceadmin/", "", $filename[0]);

        if(strpos($filename[0], ".pdf") == false)
        {
            $filename[0] = $filename[0] . ".pdf";
        }
        $value = explode("=",$row);
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

        $sql = "INSERT INTO `templatedata`( `templatefilename`, `datafield1`, `datafield2`, `datafield3`, `datafield4`, `datafield5`, `datafield6`, `datafield7`) VALUES ('" . $filename[0] . "','" . $value[1] . "','" . $value[2] . "','" . $value[3] . "','" . $value[4] . "','" . $value[5] . "','" . $value[6] . "','" . $value[7] . "')";
      //  echo $sql;
        if (mysqli_query($conn, $sql)) {
        //    echo "New record created successfully";
        } else {
          //  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        mysqli_close($conn);

    }
  }
}

function arrayToCsv( array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $output = array();
    foreach ( $fields as $field ) {
        if ($field === null && $nullToMysqlNull) {
            $output[] = 'NULL';
            continue;
        }

        // Enclose fields containing $delimiter, $enclosure or whitespace
        if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
            $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        }
        else {
            $output[] = $field;
        }
    }

    return implode( $delimiter, $output );
}


function deletetemplate($id)
{
  //write a function to delete the template
}

 ?>
