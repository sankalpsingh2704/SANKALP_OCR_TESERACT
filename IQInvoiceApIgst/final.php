<?php

	/* Database Connection */

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
	
	if(isset($_FILES['image'])){
      $errors= array();
      //$file_name = $_FILES['image']['name'];
      $file_size =$_FILES['image']['size'];
      $file_tmp =$_FILES['image']['tmp_name'];
      $file_type=$_FILES['image']['type'];
	  
	  
	  $file_name = rand(10000,99999) . ".pdf";
	  
	  //$file_name = 49923 .".pdf";
	  
	  move_uploaded_file($file_tmp,"/var/www/html/iqinvoiceapigst/uploads/".$file_name);
	  
	}
	
	/* File Upload */
	// $file_name = 49923 .".pdf";
	/* Perform shell_exec */
	$target_Path = "/var/www/html/iqinvoiceapigst/uploads/".$file_name;
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
	$xml = simplexml_load_file("/var/www/html/iqinvoiceapigst/uploads/" . $file_name . ".xml") or die("Error: Cannot create object");
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
	
	$blacklist = array("INVOICE","TAX","PRIVATE","LIMITED","VENDOR");
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
	
	$fieldsname = array("INVOICENO","AMOUNT","INVOICEDATE","PO","PAN","VENDOR","GSTIN");
	
	$fields_collection = explode(";",$rawfields);
	$response = '<?xml version="1.0" encoding="utf-8"?>';
	$response .= "<INVOICE>";
	
	for($i = 1;$i < sizeof($fields_collection); $i++)
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
	
	/* Item Section */
	$startarray = array();
	$endarray = array();
	$itemtype;
	$offset;
	$pickarray = array();
	$rowcollect = array();
	$attriarray = array();
	$mystring = "";
	
	function searchId($array,$dataarea)
	{
		for($i = 0; $i < sizeof($dataarea); $i++){
			if($array['t'] == $dataarea[$i]['t'] && $array['l'] == $dataarea[$i]['l'] && $array['w'] == $dataarea[$i]['w'] && $array['h'] == $dataarea[$i]['h'] && $array['f'] == $dataarea[$i]['f'])
			{
				//echo "<br/>Array:";
				//print_r($array);
				return $dataarea[$i]['id'];
			}
		}
	}
	
	//$sql = "SELECT * from itemdata where templateid = '31435.pdf'";
	$sql = "SELECT * from itemdata where templateid = '".$templatefile."' order by itemnumber";

	
	$result = $conn->query($sql);
	//echo "Output:".count($result);
	while($row = $result->fetch_assoc()){
		
		$sarray = explode(";",$row['startfield']);
		$startarray = array("id" => $sarray[0],"t" => $sarray[1],"l" => $sarray[2],"w"=>$sarray[3],"h"=>$sarray[4] ,"f"=>$sarray[5]);
		
		$earray = explode(";",$row['endfield']);
		
		$endarray = array("id" => $earray[0],"t" => $earray[1],"l" => $earray[2],"w"=>$earray[3],"h"=>$earray[4] ,"f"=>$earray[5]);
		$itemtype = $row["itemtype"];
		
		$offset = $row["Offset"];
		$map = $row["map"];
		
		array_push($rowcollect,array("startarray"=>$startarray,"endarray"=>$endarray,"type"=>$itemtype,"map" => $map));
		//echo"<br/>";
		//print_r($startarray);
		//echo"<br/>";
		//print_r($endarray);
		
		if($itemtype === 'Title')
		{
			//echo "Title";
			$sleft = 113-10;
			$eleft = 146 + 31 + 10;
			$titlearray = array('sleft' => $startarray["l"] - $offset,'eleft' => $endarray["l"] + $endarray["w"] + $offset,"map" => $map );
			
			array_push($pickarray,$titlearray);
			//print_r($pickarray);
		}
		else{
			
		}
		
	}
	
	//echo "current array:<br/>";
	
	//print_r($pickarray);
	//echo "<br/>";
	//print_r($rowcollect);
	$mattr = array("Id","Code","Name","Qty","Rate","Amount");
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
	//print_r($picker);
	for($i = 0; $i < count($rowcollect); $i++)
	{
		$startarray = $rowcollect[$i]["startarray"];
		$endarray = $rowcollect[$i]["endarray"];
		
		if($rowcollect[$i]["type"] == "Data")
		   $mystring .=	dofetch($startarray,$endarray,$dataarea,$picker);
	}
	
	function linechange($dataarea,$i){
		$prev = intval($dataarea[$i-1]['t']);
		$cur = intval($dataarea[$i]['t']);
		if(!($dataarea[$i-1]['val'] == "|" || $dataarea[$i]['val'] == "|"))
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
		
		// Search from db to DOC
	
	
		// Fetch logic
		
		$mystring = "";
		$nextline = 0;
		$sleft = 113-10;
		$eleft = 146 + 31 + 10;
		//echo "sleft:".$sleft;
		//echo "eleft:".$eleft;
		//$pickarray = array(0 => array('sleft' => 103,'eleft' => 187),1 => array('sleft' => 513,'eleft' => 565),2 => array('sleft' => 571,'eleft' => 620),3 => array('sleft' => 627,'eleft' => 676),4 => array('sleft' => 683,'eleft' => 775),5 => array('sleft' => 185,'eleft' => 427));
		//$pickarray = array(0 => array('sleft' => 185,'eleft' => 427));
		//$pickarray = array();
		//$compare_height = 0;
		//$next_height = 0;
		$order = 0;
		$attr = 0;
		$picseq = 0;
		
		//$close = 0;
		for($i = $startid-1; $i <= $endid-1; $i++ ){
			
			//$newresponse .= $attributes[0]."=S'";
			if(!($i == $startid -1))
			{
				
				$nextline = linechange($dataarea,$i);
				
			}
			if($nextline == 1)
			{
				//$mystring .= "<br/>";
				//$response .= '<br/>';
			}
			if($dataarea[$i]['val'] === "|")
			{
				//$mystring .= " ";
				//continue;
			}
			else{
				$found = 0;
				$cfound = 0;
				$notfound = 0;
				for($j = 0; $j< count($pickarray); $j++)
				{
					//
					$cfound ++;
					//echo "<br/>Loop:".$i;
					
					if($dataarea[$i]['l'] > $pickarray[$j]['sleft'] && $dataarea[$i]['l'] < $pickarray[$j]['eleft'])
					{
						
						
						if($order == $j)
						{
							
							if($j == 0)
							{
								$mystring .= "<br/>".$dataarea[$i]['val'];
								//echo "<br/>" .$dataarea[$i]['val'];
							}
							else{
								$mystring .= " ".$dataarea[$i]['val'];
								//echo " " .$dataarea[$i]['val'];
							}
							
						}
						else
						{
							
							$mystring .= "<br/>".$dataarea[$i]['val'];
							//echo "<br/>" .$dataarea[$i]['val'];
							$order = $j;
							
						}
						
						$found = 1;
					}
					else{
							
						
							
					}
					
					
					
				}
				
				if($found == 0)
				{
					$mystring .= "";
					//$dataarea[$i]['val']
				}
					
			}
			//$mystring .="<br/>";
		}
		//echo $dataarea[130]['val'];
		//echo "<br/>".$mystring;
		return $mystring;
		
		
	}
	
	//echo "<br/>";
	$myarray = explode("<br/>",$mystring);
	//echo "<br/>";
	//print_r($myarray);
	$attributes = array("Id","Code","Name","Qty","Rate","Amount","IGST","SGST","CGST");
	
	$cycle = intval((sizeof($myarray)-1)/6);
	$rem = (sizeof($myarray)-1)%6;
	$loopsize = ($cycle * 6) + $rem + (6 - $rem);
	
	$cycle = $loopsize / 6;
	/*$attrstr = "<?xml version='1.0' encoding='utf-8'?>"; */
	$attrstr = "<ITEMS>";
	
	for($i = 0; $i < $cycle ; $i++)
	{
		$attrstr .= "<ITEM ".$attributes[0]."='".blankcheck($myarray[$i*6 + 1])."' ".$attributes[1]."='".blankcheck($myarray[$i*6 + 2])."' ".$attributes[2]."='".blankcheck($myarray[$i*6 + 3])."' ".$attributes[3]."='".blankcheck($myarray[$i*6 + 4])."' ".$attributes[4]."='".blankcheck($myarray[$i*6 + 5])."' ".$attributes[5]."='".blankcheck($myarray[$i*6 + 6])."' ";
		$attrstr .= $attributes[6]."=' ' ".$attributes[7]."=' ' ".$attributes[8]."=' ' ></ITEM>";
	}
	function blankcheck($chk){
		
		if($chk == null || $chk == "")
		{
			return " ";
		}
		return $chk;
	}
	$attrstr .= "</ITEMS>";
	//echo $attrstr;
	$response .= $attrstr;
	
	/* Item Section */
	
	
	$response .= "</INVOICE>";
	exit($response);

	/* Extract Data from fields */

?>