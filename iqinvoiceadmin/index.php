<?php
include("assets/header.php");
/*--------------------------------------------------------------------------------------------
|    @desc:        index.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
include('config.php');    //include of db config file
include ('paginate.php'); //include of paginat page
$connect = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
$per_page = 10;         // number of results to show per page
$result = mysqli_query($connect, "SELECT * FROM vendorsmaster");
$total_results = mysqli_num_rows($result);
$total_pages = ceil($total_results / $per_page);//total pages we going to have

//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}
// display pagination
$page = intval($_GET['page']);

$tpages=$total_pages;
if ($page <= 0)
    $page = 1;
?>

    <div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">

					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						 <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
					</button> <a class="navbar-brand" href="#"><img width="60%" height="60%" src="assets/iqlogo.png" /> </a>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Enter a string ">
						</div>
						<button type="submit" class="btn btn-primary btn btn-primary-default">
							Search By Content
						</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="http://www.iqinvoice.com">About</a>
						</li>
					</ul>
				</div>

			</nav>
			<div class="row">
				<div class="col-md-6">
					<h2>
						Template Manager
					</h2>
					<p>
						To create and manage templates, you can use this section.
					</p>
          <h3>
            Create a New Template
          </h3>
          <p>
						If you have a new invoice, then you can create a new template so that the system can detect it from the pattern that you will teach it. You can click the button below to create a new template invoice.
					</p>
          <p>
            <a class="btn btn-primary" href="templadd.php">Create a New Template Here »</a>
          </p>
          <p>
              <table class='table table-bordered'>
                <thead>
                  <td style="width: 10%;">Slno</td>
                  <td style="width: 40%; ">Template Name</td>
                  <td style="width: 40%; ">Vendor Name</td>
                  <td style="width: 10%; ">Action</td>
                </thead>
                <tbody>
                  <?php
                    $conn = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
                    $sql = "SELECT * FROM templatemanager";
                    $templresult = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($templresult) > 0) {
                    // output data of each row
                    $i=1;
                    while($row = mysqli_fetch_assoc($templresult)) {
                      $query1 = "SELECT `vendorname` FROM `vendorsmaster` WHERE `vendorid` = " . $row['templvendrid'] ;
                      $row1 = mysqli_query($conn,$query1);
                      $result1 = mysqli_fetch_array($row1);
                  ?>
                  <tr>
                    <td style="width: 10%;"><?php echo $i; ?></td>
                    <td style="width: 40%; "><?php echo $row['templname']; ?></td>
                    <td style="width: 40%; "><?php echo $result1[0]; ?></td>
                    <?php
                    $i++;
                    if($row['templfields'] == null && $row['templinvname'] == null)
                    { ?>
                	<td style="width: 10%; "><p>
          						<a class="btn btn-primary" href="addinvoice.php?id=<?php echo $row['templid']; ?>">Add Invoice to Template »</a>
          					</p>
                <?php
                    }
                    else
                    {

                    ?>
                    <td style="width: 10%; "><p>
          						<a class="btn btn-primary view-pdf" id="view-pdf" name="view-pdf" href="<?php echo 'uploads/'.$row['templinvname']; ?>">View Invoice »</a>
          					</p>
                    <?php if ($row['templfields'] == null)
                    {
                    ?>
                    <p>
          						<a class="btn btn-primary" href="createtmplmapping.php?id=<?php echo $row['templid']; ?>">Create data  Mappings »</a>
          					</p>
                    <?php
                    } else {
                    ?>
                    <p>
          						<a class="btn btn-primary" href="createtmplmapping.php?id=<?php echo $row['templid']; ?>">Edit Mappings »</a>
          					</p>
                    <?php
                    }
                     ?>
                    <p>
          						<a class="btn btn-primary" href="deltempl.php?id=<?php echo $row['templid']; ?>">Delete Template »</a>
          					</p></td>
                  </tr>
                  <?php
                    }
                } } else {
                  echo '<tr colspan="4"><td>No Data in database</td></tr>';
                }
                  ?>
                </tbody>
              </table>
          </p>

				</div>
        <div class="col-md-6">
          <h2>
            Vendor Manager
          </h2>
          <p>
						To create and manage vendors, you can use this section.
					</p>
          <h3>
            Create a New Vendor
          </h3>
          <p>
						You can create a new vendor here. This will enable you to map a vendor name as found in an invoice. This enables the system to detect an invoice effectively.
					</p>
          <p>
            <a class="btn btn-primary" href="vendoradd.php">Add a New Vendor Here »</a>
          </p>
          <p>
              <div class="row">
            <div class="span10">
                <div class="mini-layout">
                 <?php
                    $reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages;
                    echo '<div class="pagination"><ul>';
                    if ($total_pages > 1) {
                        echo paginate($reload, $show_page, $total_pages);
                    }
                    echo "</ul></div>";
                    // display data in table
                    echo "<table class='table table-bordered'>";
                    echo "<thead><tr><th>Vendor Name</th> <th>Vendor Address</th></tr></thead>";
                    // loop through results of database query, displaying them in the table
                    for ($i = $start; $i < $end; $i++) {
                        // make sure that PHP doesn't try to show results that don't exist
                        if ($i == $total_results) {
                            break;
                        }

                        // echo out the contents of each row into a table
                        //echo "<tr " . $cls . ">";
                        echo "<tr>";
                        echo '<td>' . mysqli_result($result, $i, 'vendorname') . '</td>';
                        echo '<td>' . mysqli_result($result, $i, 'vendoraddress') . '</td>';
                        echo "</tr>";
                    }
                    // close table>
                echo "</table>";
            // pagination
            ?>
            </div>
        </div>
    </div>
          </p>
        </div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
  /*
  * This is the plugin
  */
  (function(a){a.createModal=function(b){defaults={title:"",message:"!",closeButton:true,scrollable:false};var b=a.extend({},defaults,b);var c=(b.scrollable===true)?'style="max-width: 100%; max-height: 100%;"':"";html='<div style="min-width:50%; min-height:50%;" class="modal fade" id="myModal">';html+='<div class="modal-dialog modal-lg">';html+='<div style="min-width:100%; min-height:100%;" class="modal-content modal-lg">';html+='<div style="min-width:100%; min-height:100%;"  class="modal-header modal-lg">';html+='<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';if(b.title.length>0){html+='<h4 class="modal-title modal-lg">'+b.title+"</h4>"}html+="</div>";html+='<div style="min-width:100%; min-height:100%;" class="modal-body modal-lg" '+c+">";html+=b.message;html+="</div>";html+='<div style="min-width:100%; min-height:100%;" class="modal-footer modal-lg">';if(b.closeButton===true){html+='<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'}html+="</div>";html+="</div>";html+="</div>";html+="</div>";a("body").prepend(html);a("#myModal").modal().on("hidden.bs.modal",function(){a(this).remove()})}})(jQuery);

  /*
  * Here is how you use it
  */
  $(function(){
      $('.view-pdf').on('click',function(){
          var pdf_link = $(this).attr('href');
          var iframe = '<div class="iframe-container"><iframe src="'+pdf_link+'"></iframe></div>'
          $.createModal({
          title:'Invoice PDF',
          message: iframe,
          closeButton:true,
          scrollable:false
          });
          return false;
      });
  })
</script>

<?php
include("assets/footer.php");
?>
