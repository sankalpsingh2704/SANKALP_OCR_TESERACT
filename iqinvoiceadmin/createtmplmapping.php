<?php
/*--------------------------------------------------------------------------------------------
|    @desc:        createtmplmapping.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
include("assets/header.php");
include("config.php");
if(isset($_GET['id']))
{
  echo "<div align='center'><h2>Processing your PDF now..</h2>";
  echo "<div align='left' style='padding-left:25%;'><p>Steps performed for converting the template</p>";
  echo "<p>1. OCR the PDF file provided by you</p>";
  echo "<p>2. Let you choose the parameters required for creating the template</p>";
  echo "<p>3. Creating the template</p>";
  echo "<p>4. All done.. you can now process these invoices in the front end or via API.</p>";
  $filename = "/var/www/html/iqinvoiceadmin/uploads/" . returnfilename($_GET['id']) . ".pdf.xml";
  $filename1 = "/var/www/html/iqinvoiceadmin/uploads/" . returnfilename($_GET['id']) . ".xml";

  if(file_exists($filename) || file_exists($filename1))
  {
    echo "<a href=\"selecttempldetails.php?id=" . $_GET['id'] . "\" class=\"myButton\">Proceed to Editing Template</a></div></div>";
  }
  else {
    echo "<a href=\"processpdffile.php?id=" . $_GET['id'] . "\" class=\"myButton\">Proceed to creating template</a></div></div>";
  }

}
else {
  echo "Invalid call.. Redirecting you to home page..";
  echo "<script type='text/javascript'>window.setTimeout(function(){
        window.location.href = \"index.php\";
    }, 5000);</script>";
}
include("assets/footer.php");
 ?>
