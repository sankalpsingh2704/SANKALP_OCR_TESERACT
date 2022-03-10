<?php
/*--------------------------------------------------------------------------------------------
|    @desc:        proceesspdffile.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
if(isset($_GET['id']))
{
  include('config.php');    //include of db config file
  $target_Path = "/var/www/html/iqinvoiceadmin/uploads/";
  $Path = "/var/www/html/iqinvoiceadmin/uploads/";
  $filn = returnfilename($_GET['id']);
  //$target_Path = $target_Path.basename( $_FILES['userFile']['name'] );
  $target_Path = $target_Path.$filn;
  //$filename=basename( $_FILES['userFile']['name'] );
  $filename=$filn ;

  exec("sudo sh ocrthepdf.sh " . $target_Path);
  $downloadfile= "";
  if(file_exists($Path .$filename . ".pdf.xml"))
  {
    $downloadfile = $Path . $filename . ".pdf.xml";
  }
  else {
    $downloadfile = $Path . $filename .".xml";
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
  parsexmltodatabase($downloadfile);
    header("Location:selecttempldetails.php?id=". $_GET['id']);
}
else {
  echo "Invalid call.. Redirecting you to home page..";
  echo "<script type='text/javascript'>window.setTimeout(function(){
        window.location.href = \"index.php\";
    }, 5000);</script>";
}
?>
