<?php

	/* Database Connection */

	$servername = "localhost";
	$username = "root";
	$password = "iqss@123";
	$dbname = "ocr_vendorengine";
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$file_name = "";
	/* Database Connection */
	
	/* File Upload */
	
	if(isset($_FILES['image'])){
      $errors= array();
      //$file_name = $_FILES['image']['name'];
      $file_size =$_FILES['image']['size'];
      $file_tmp =$_FILES['image']['tmp_name'];
      $file_type=$_FILES['image']['type'];
	  
	  
	  $file_name = rand(10000,99999) . ".pdf";
	  
	  //$file_name = 49923 .".pdf";
	  
	  move_uploaded_file($file_tmp,"/var/www/html/iqinvoiceapi/uploads/".$file_name);
	  
	}
	
	/* File Upload */
	// $file_name = 49923 .".pdf";
	/* Perform shell_exec */
	$target_Path = "/var/www/html/iqinvoiceapi/uploads/".$file_name;
	//echo "Filename:".$file_name;
	shell_exec("sh /var/www/html/iqinvoiceapi/ocrthepdf.sh " .$target_Path);
	
	$downloadfile= "";
  if(file_exists($target_Path . ".pdf.xml"))
  {
	
    $downloadfile = $target_Path . "pdf.xml";
	//echo "Existed". $target_Path;
  }
  else {
    $downloadfile = $target_Path .".xml";
	//echo "Not Existed". $target_Path;
  }
	
  $blacklist = "<fontspec";
  $blacklist1 = "<image";
  $blacklist2 = "<page number=";
  $blacklist3 = "</page>";
  $blacklist4 = "<iqss";
  $blacklist5 = "<!DOCTYPE";
  $whitelist = "<dataarea ";
  $i=1;
  $path_to_file = $downloadfile;
  $file_contents = file_get_contents($path_to_file);
  $file_contents = str_replace("<text","  <dataarea",$file_contents);
  $file_contents = str_replace("</text>","</dataarea>",$file_contents);
  $file_contents = str_replace("<b>","",$file_contents);
  $file_contents = str_replace("</b>","",$file_contents);
  $file_contents = str_replace("<i>","",$file_contents);
  $file_contents = str_replace("</i>","",$file_contents);
  $file_contents = str_replace("pdf2xml","iqss",$file_contents);
  $file_contents = str_replace("poppler","iqss",$file_contents);
  $file_contents = str_replace("</page>","",$file_contents);
  $file_contents = str_replace("top=","t=",$file_contents);
  $file_contents = str_replace("left=","l=",$file_contents);
  $file_contents = str_replace("width=","w=",$file_contents);
  $file_contents = str_replace("height=","h=",$file_contents);
  $file_contents = str_replace("font=","f=",$file_contents);


  file_put_contents($path_to_file,$file_contents);

  $rows = file($downloadfile);
  foreach($rows as $key => $row) {
      if(preg_match("/($blacklist)/", $row)) {
          unset($rows[$key]); }
          if(preg_match("/($blacklist1)/", $row)) {
              unset($rows[$key]); }
              if(preg_match("/($blacklist2)/", $row)) {
                  unset($rows[$key]); }
                  if(preg_match("/($blacklist3)/", $row)) {
                      unset($rows[$key]); }
                      if(preg_match("/($blacklist4)/", $row)) {
                          $rows[$key] = "<iqss>"; }
        if(preg_match("/($blacklist5)/", $row)) {
           unset($rows[$key]); }
                            if(preg_match("/($whitelist)/", $row)) {
                                $rows[$key] = str_replace("<dataarea ","  <dataarea id=\"" . $i . "\" ",$rows[$key]);
                                $rows[$key] = str_replace("'"," ",$rows[$key]);
                                $rows[$key] = str_replace("\">","\" val=\"",$rows[$key]);
                                $rows[$key] = str_replace("</dataarea>","\" />",$rows[$key]);
                                $i++;
                            }
  }
  file_put_contents($downloadfile, implode("\n", $rows));
	
	//shell_exec("sh /var/www/html/iqinvoiceapi/ocrthepdf.sh " .$target_Path);
	
	//exec("./ocrthepdf.sh" .$target_Path);
	//set_time_limit (20);
	/* Perform shell_exec */
	
	/* Predict Vendor Name From Algo */
	
	//$xml = simplexml_load_file("uploads/49923.pdf.pdf.xml") or die("Error: Cannot create object");
	//echo "/var/www/html/iqinvoiceapi/uploads/" . $file_name . ".xml";
	$xml = simplexml_load_file("/var/www/html/iqinvoiceapi/uploads/" . $file_name . ".xml") or die("Error: Cannot create object");
	$dataarea = $xml->dataarea;
	//echo "Data area: " . $file_name;
	$bigarray = array();
	$nameslist = array();
	
	for($j = 0; $j < sizeof($dataarea); $j++)
	{	
		$searcharray = explode(" ",$dataarea[$j]['val']);
		$filterarray = array();
		
		for($y = 0; $y < sizeof($searcharray); $y++ )
		{

			$searcharray[$y] = strtoupper($searcharray[$y]);
			array_push($bigarray,$searcharray[$y]);
		}
			
	}
	$accuracy = array();
	$sql = "SELECT templname FROM templatemanager";
	$result = $conn->query($sql);
	
	while($row = $result->fetch_assoc())
	{
		
		$temp =  strtoupper($row["templname"]);
		$namearray = explode(" ",$temp);
		$count = 0;
		
		for($i =0; $i < sizeof($bigarray); $i++)
		{
			for($j =0; $j < sizeof($namearray); $j++)
			{
				
					if($bigarray[$i] !== "" && $namearray[$j] !== "" ){
						if ($bigarray[$i] === $namearray[$j]) {
							
							array_push($nameslist,$namearray[$j]);
							$count++;
					
							}
					}
			}
		}
		array_push($accuracy,$count);
		
	}
	
	$blacklist = array("INVOICE","TAX");
	$size = sizeof($nameslist);
	foreach($nameslist as $x )
	{
		
		for($y = 0; $y < sizeof($blacklist); $y++)
		{
			if($x === $blacklist[$y])
			{
				$key = array_search($x, $nameslist);
				unset($nameslist[$key]);
				
			}
		}
	}
	
	$rawfields = "";
	$templname = "";
	foreach($nameslist as $v){
		
	$sql = "SELECT templname, templfields FROM templatemanager where templname like '%".$v."%'";
	//echo "<br/>Query:".$sql."<br/>";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc())
	{
		//echo "<br/>Template Name:".$row['templname'];
		//echo "<br/>Fields:".$row['templfields'];
		$rawfields = $row["templfields"];
		$templname = $row["templname"];
		//echo "template:".$templname;
		break;
	}
	break;
	}
	
	/* Predict Vendor Name From Algo */
	
	/* Extract Data from fields */
	
	$fieldsname = array("INVOICENO","AMOUNT","INVOICEDATE","PO","PAN","VENDOR");
	
	$fields_collection = explode(";",$rawfields);
	$response = '<?xml version="1.0" encoding="utf-8"?>';
	$response .= "<INVOICE>";
	
	for($i = 1;$i < sizeof($fields_collection)-1; $i++)
	{
		$sql = "SELECT datafield1, datafield2, datafield3, datafield4, datafield5, datafield6, datafield7 FROM templatedata WHERE templatedataid = '".$fields_collection[$i]."'";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0)
		{
			
			while($row = $result->fetch_assoc())
			{
			
				for($j = 0; $j < sizeof($dataarea); $j++)
				{
					
					if($row["datafield2"] == $dataarea[$j]['t'] && $row["datafield3"] == $dataarea[$j]['l'] && $row["datafield4"] == $dataarea[$j]['w'] && $row["datafield5"] == $dataarea[$j]['h'] && $row["datafield6"] == $dataarea[$j]['f'] )
					{
						//echo "<br/>".$fieldsname[$i-1].": ".$dataarea[$j]['val'];
						$response .= "<".$fieldsname[$i-1].">".$dataarea[$j]['val']."</".$fieldsname[$i-1].">";
						
					}
				}
				
				//echo "<br/>".$fieldsname[$i-1].": ".$row["datafield7"]."<br/>";
				
			}
		}
	}
	$response .= "</INVOICE>";
	exit($response);

	/* Extract Data from fields */

?>