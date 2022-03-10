<?php

	/* Database Connection */
	//echo "sankalp";
	$servername = "localhost";
	$username = "root";
	$password = "iqss@123";
	$dbname = "ocr_vendorenginegst";
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$file_name = "";
	/* Database Connection */
	
	/* File Upload */
	$file_name = 49924 .".pdf";
	//$file_name = 31435 .".pdf";
	
	if(isset($_FILES['image'])){
		//echo "sankalp";
      		$errors= array();
      		//$file_name = $_FILES['image']['name'];
      		$file_size =$_FILES['image']['size'];
      		$file_tmp =$_FILES['image']['tmp_name'];
      		$file_type=$_FILES['image']['type'];
	  
	  
	  //$file_name = rand(10000,99999) . ".pdf";
	  
	  $file_name = 49924 .".pdf";
	  //$file_name = 31435 .".pdf";
	  echo $filename;
	  move_uploaded_file($file_tmp,"uploads/".$file_name);
	  
	}
	
	/* File Upload */
	// $file_name = 49923 .".pdf";
	/* Perform shell_exec */
	$target_Path = "uploads/".$file_name;
	//echo "Filename:".$file_name;
	shell_exec("sh ocrthepdf.sh " .$target_Path);
	
	$downloadfile= "";
  if(file_exists($target_Path . ".pdf.xml"))
  {
	
    $downloadfile = $target_Path . ".pdf.xml";
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

	if(file_exists($target_Path . ".pdf.xml"))
  	{
	
    		$file_name = $file_name . ".pdf";
		//echo "Existed". $file_name;
  	}
  	else {
    		$file_name = $file_name;
		//echo "Not Existed". $file_name;
  	}

	//echo "Data area: " . $file_name;
	$xml = simplexml_load_file("uploads/" . $file_name . ".xml") or die("Error: Cannot create object");
	//$xml = simplexml_load_file("/var/www/html/iqinvoiceapi/uploads/31435.pdf.pdf.xml") or die("Error: Cannot create object");

	$dataarea = $xml->dataarea;
	//print_r($dataarea);
	
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
	//print_r($nameslist);
	$blacklist = array("INVOICE","TAX","PRIVATE","LIMITED","VENDOR","PACKAGING");
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
	//print_r($nameslist);
	$rawfields = "";
	$templname = "";
	$templatefile = "";
	foreach($nameslist as $v){
		
	$sql = "SELECT templname, templfields , templinvname FROM templatemanager where templname like '%".$v."%'";
	//echo "<br/>Query:".$sql."<br/>";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc())
	{
		//echo "<br/>Template Name:".$row['templname'];
		//echo "<br/>Fields:".$row['templfields'];
		$rawfields = $row["templfields"];
		$templname = $row["templname"];
		$templatefile = $row["templinvname"];
		//echo "template:".$templname;
		break;
	}
	break;
	}
	
	/* Predict Vendor Name From Algo */
	
	/* Extract Data from fields */
	//echo "sankalp";
	$fieldsname = array("INVOICENO","AMOUNT","INVOICEDATE","PO","PAN","VENDOR","GSTIN");
	
	$fields_collection = explode(";",$rawfields);
	
	//echo "Some";
	/*
	$dev = array();
	for($i = 1; $i < sizeof($fields_collection); $i++)
	{
		$sql = "SELECT datafield1, datafield2, datafield3, datafield4, datafield5, datafield6, datafield7 FROM templatedata WHERE templatedataid = '".$fields_collection[$i]."'";
		$result = $conn->query($sql);
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{

				//echo "<br/>".$row["datafield7"];
				for($j = 0; $j < sizeof($dataarea); $j++){
					echo "<br/>".$dataarea[$j]['val'];
					echo $row["datafield7"];
					if(strcasecmp(strtolower($row["datafield7"]),$dataarea[$j]['val']) == 0)
					{
						
						echo "true";
					}
					else{
						echo "false";
					}
			      
				}
			}
		}

	}
	*/
	
	$response = '<?xml version="1.0" encoding="utf-8"?>';
	$response .= "<INVOICE>";
	//echo "Size:".sizeof($fields_collection);
	for($i = 1;$i < sizeof($fields_collection); $i++)
	{
		$sql = "SELECT datafield1, datafield2, datafield3, datafield4, datafield5, datafield6, datafield7 FROM templatedata WHERE templatedataid = '".$fields_collection[$i]."'";
		//echo "Query:".$sql;
		$result = $conn->query($sql);
		
		if($result->num_rows > 0)
		{
			
			while($row = $result->fetch_assoc())
			{
			
				for($j = 0; $j < sizeof($dataarea); $j++)
				{
					//$row["datafield6"] == $dataarea[$j]['f'] 
					
					if(($row["datafield2"] - 5) <= $dataarea[$j]['t'] && ($row["datafield3"]-5) <= $dataarea[$j]['l'] && ($row["datafield2"] + 5) >= $dataarea[$j]['t'] && ($row["datafield3"] + 5) >= $dataarea[$j]['l'] )
					{
						//echo "<br/>".$fieldsname[$i-1].": ".$dataarea[$j]['val'];
						$response .= "<".$fieldsname[$i-1].">".$dataarea[$j]['val']."</".$fieldsname[$i-1].">";
						
					}
				}
				
				//echo "<br/>".$fieldsname[$i-1].": ".$row["datafield7"]."<br/>";
				
			}
		}
	}
	//echo "<br/>resp:".$response;
	/* Item Section */
	$startarray = array();
	$endarray = array();
	$itemtype;
	$offset;
	$pickarray = array();
	$rowcollect = array();
	$attriarray = array();
	$mystring = "";
	$output = array();
	
	function searchId($array,$dataarea)
	{
		for($i = 0; $i < sizeof($dataarea); $i++){
			if($array['t'] == $dataarea[$i]['t'] && $array['l'] == $dataarea[$i]['l'] && $array['w'] == $dataarea[$i]['w'] && $array['h'] == $dataarea[$i]['h'] && $array['f'] == $dataarea[$i]['f'])
			{
				return $dataarea[$i]['id'];
			}
		}
	}	
	//$sql = "SELECT * from itemdata where templateid = '31435.pdf'";
	$sql = "SELECT * from itemdata where templateid = '".$templatefile."' order by itemnumber";
	//echo $sql;
	
	$result = $conn->query($sql);
	
	while($row = $result->fetch_assoc()){
		
		$sarray = explode(";",$row['startfield']);
		$startarray = array("id" => $sarray[0],"t" => $sarray[1],"l" => $sarray[2],"w"=>$sarray[3],"h"=>$sarray[4] ,"f"=>$sarray[5]);
		
		$earray = explode(";",$row['endfield']);
		
		$endarray = array("id" => $earray[0],"t" => $earray[1],"l" => $earray[2],"w"=>$earray[3],"h"=>$earray[4] ,"f"=>$earray[5]);
		$itemtype = $row["itemtype"];
		
		$offset = $row["Offset"];
		$map = $row["map"];
		
		array_push($rowcollect,array("startarray"=>$startarray,"endarray"=>$endarray,"type"=>$itemtype,"map" => $map));
		
		
		if($itemtype === 'Title')
		{
			
			$sleft = 113-10;
			$eleft = 146 + 31 + 10;
			$titlearray = array('sleft' => $startarray["l"] - $offset,'eleft' => $endarray["l"] + $endarray["w"] + $offset,"map" => $map );
			
			array_push($pickarray,$titlearray);
			
		}
		else{
			
		}
		
	}


	$mattr = array();
	$sql = "select Fields ,Id from itemtofetch order by Id";
	$hsn = 0;
	$result = $conn->query($sql);

	
	while($row = $result->fetch_assoc()){
		array_push($mattr,$row["Fields"]);
		if($row["Fields"] == "Code"){
			$hsn = $row["Id"];
		}
	}
	//print_r($rowcollect);
	$picker = array();
	for($i = 0 ; $i < count($mattr); $i++ )
	{
		for($j = 0;$j < count($pickarray); $j++){
			if($mattr[$i] == $pickarray[$j]["map"]){
				array_push($picker,$pickarray[$j]);
			}
		}
	}
	//echo "<br/>Picker:<br/>";
	//print_r("picker".$picker);
	for($i = 0; $i < count($rowcollect); $i++)
	{
		$startarray = $rowcollect[$i]["startarray"];
		$endarray = $rowcollect[$i]["endarray"];
		
		if($rowcollect[$i]["type"] == "Data")
		{
		   //echo "data";
		   $output = dofetch($startarray,$endarray,$dataarea,$picker);
		   //$print_r($output);
		}
		
	}
	
	function linechange($dataarea,$p,$i){
		//$prev = intval($dataarea[$i-1]['t']);
		$prev = intval($dataarea[$p]['t']);
		$cur = intval($dataarea[$i]['t']);
		if(!($dataarea[$p]['val'] == "|" || $dataarea[$i]['val'] == "|"))
		{
			if($cur <= $prev +15 && $cur >= $prev -15 ){
				return 0;
				//echo "<br/> I:".$i."Cur:".$dataarea[$i-1]['val']." ".$dataarea[$i]['val'];
			}
			else{
				//echo "<br/>Next Line after:".$dataarea[$i]['val'];
				return 1;
			}
		}
		else{
			return 0;
		}
	}
	
	function dofetch($startarray,$endarray,$dataarea,$pickarray){
		$res = "";
		//$attributes = array("Id","Code","Name","Qty","Rate","Unit","Amount","IGST","SGST","CGST");
		
		$startid = intval(searchId($startarray,$dataarea));
		$endid = intval(searchId($endarray,$dataarea));
		//echo "startid:".$startid;
		//echo "endid:".$endid;
		// Search from db to DOC
	
	
		// Fetch logic
		
		$mystring = "";
		$nextline = 0;
		$sleft = 113-10;
		$eleft = 146 + 31 + 10;
		
		$order = 0;
		$attr = 0;
		$picseq = 0;
		//$fieldlist = array("Id","Name","Code","Qty","Rate","Amount");
		//$close = 0;
		$last = 0;
		$outputarray = array();
		$nxt = 0;
		$pre = 0;
		$pdata = 0;
		$datastring = "";
		for($x = 0; $x < count($pickarray);$x++)
		{
			$fieldarray = array($pickarray[$x]["map"] => array());
			
			//print_r($fieldarray);
			for($i = $startid-1; $i <= $endid-1; $i++ ){
			//echo "match data:".$dataarea[$i]['l']."=";
			//$newresponse .= $attributes[0]."=S'";
			
			
			if($dataarea[$i]['l'] > $pickarray[$x]['sleft'] && $dataarea[$i]['l'] < $pickarray[$x]['eleft'])
			{
				//$mystring .= "<br/>".$dataarea[$i]['val'];
				
				$nextline = linechange($dataarea,$pdata,$i);
				//echo "<br/>nextline:".$nextline."Data".$dataarea[$i]['val'];
				if($last == 1){
					$lastarray = array($pickarray[$x-1]["map"] => array());
					
					array_push($outputarray[$x-1][$pickarray[$x-1]["map"]],$datastring);
					
					$last = 0;
				}
				else if($nextline != 0 ){
						//echo "<br/>".$datastring;
						if($i!== ($startid-1)){
							array_push($fieldarray[$pickarray[$x]["map"]],$datastring);
						}
						
						$datastring = "";
					}
				
				if($pickarray[$pre]["map"] == $pickarray[$x]["map"]){
					
					if($nextline == 0){ 
						
						$datastring .= " ".$dataarea[$i]['val'];
						//echo "<br/>nextline".$nextline."Data".$dataarea[$i]['val']."pos:".$i;
					}
					else{
						
						$datastring = $dataarea[$i]['val'];
					}
				}
				else{
					
					$datastring = $dataarea[$i]['val'];
					
				}
				
				
				$pdata = $i;
				$pre = $x;
				
				
			}
			
			if($i == $endid -1){
					$last = 1;
					
					if((count($pickarray)-1) == $x){
						array_push($fieldarray[$pickarray[$x]["map"]],$datastring);
					}
				}
				
			
			}
			//print_r($fieldarray);
			array_push($outputarray,$fieldarray);
			
		}
		/*
		for($i = 0; $i < count($outputarray);$i++)
		{
			echo "<br/>";
			echo $pickarray[$i]["map"];
			echo "<br/>";
			
			for($j = 0; $j < count($outputarray[$i][$pickarray[$i]["map"]]); $j++)
			{
				echo "<br/>".$outputarray[$i][$pickarray[$i]["map"]][$j];
			}
				
			echo "<br/>";
		}
		*/
		return $outputarray;
			
		
		
	}	

	$loopcount = count($output[$hsn]["Code"]);
	$counter = array();
	for($i = 0; $i < count($picker); $i++)
	{
		  $x = count($output[$i][$picker[$i]["map"]])/$loopcount;
		  
		  $r = (int)$x;
		  
		  if($r < 1){
			  $r = 1;
		  }
		  array_push($counter,$r);
		  
	}
	//print_r($output);
	//echo $hsn;
	/*
	for($t=0; $t < $loopcount; $t++)
	{
		for($i = 0; $i < count($picker); $i++){
			echo "<br/>".$output[$i][$picker[$i]["map"]][$counter[2]*$t];
			//$counter[$i]
		}
		echo "<br/>";
	}
	*/
	//print_r($output[$hsn]["Code"]);
	$xmloutput = "";
	for($t=0; $t < $loopcount; $t++)
	{
		$attr = "<ITEM ";
		for($i = 0; $i < count($picker); $i++){
			
			$x  = $output[$i][$picker[$i]["map"]][$counter[$hsn]*$t];
			if($x == "" || $x == null )
				$x = " ";
			$attr .= " ".$picker[$i]["map"]."='".$x."'";
		
		}
		$attr .= " ></ITEM>";
		$xmloutput .= $attr; 
		//echo $attr;
	}
	//echo $xmloutput;
	
	$response .= "<ITEMS>";
	$response .= $xmloutput;
	$response .= "</ITEMS>";
	/* Item Section */
	
	
	$response .= "</INVOICE>";
	exit($response);

	/* Extract Data from fields */

?>