<?php

$file_name = 49923 .".pdf";
$filename = 49923 .".pdf";
$target_Path = "/var/www/html/iqinvoiceapi/uploads/".$file_name;
$Path = "/var/www/html/iqinvoiceapi/uploads/";
	
	echo "target".$target_Path;
	echo shell_exec("sh /var/www/html/iqinvoiceapi/ocrthepdf.sh " .$target_Path);
	
	
	$downloadfile= "";
  if(file_exists($Path .$filename . ".pdf.xml"))
  {
	
    $downloadfile = $Path . $filename . ".pdf.xml";
	echo "Existed". $downloadfile;
  }
  else {
    $downloadfile = $Path . $filename .".xml";
	echo "Not Existed". $downloadfile;
  }
  //$downloadfile = str_replace("/var/www/html/","",$downloadfile);
  $blacklist = "<fontspec";
  $blacklist1 = "<image";
  $blacklist2 = "<page number=";
  $blacklist3 = "</page>";
  $blacklist4 = "<iqss";
  $blacklist5 = "<!DOCTYPE";
  $whitelist = "<dataarea ";
  $i=1;
  $path_to_file = $downloadfile;
  echo $path_to_file . "<br/>";
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

	echo $file_contents . "<br/>";
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
	
	
	//exec("./ocrthepdf.sh" .$target_Path);
	set_time_limit(20);
	/* Perform shell_exec */
	
	/* Predict Vendor Name From Algo */
	
	//$xml = simplexml_load_file("uploads/49923.pdf.pdf.xml") or die("Error: Cannot create object");
	$xml = simplexml_load_file("/var/www/html/iqinvoiceapi/uploads/".$file_name.".pdf.xml") or die("Error: Cannot create object");
	echo $xml;


?>