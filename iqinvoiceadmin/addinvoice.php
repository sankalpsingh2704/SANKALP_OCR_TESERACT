<?php
/*--------------------------------------------------------------------------------------------
|    @desc:        addinvoice.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
include("assets/header.php");
include('config.php');    //include of db config file
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
								<button type="submit" class="btn btn-primary btn btn-primary-primary btn btn-primary btn btn-primary-primary-default">
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
						<div class="col-md-12">
							<p>
		            <a class="btn btn-primary btn btn-primary-primary" href="index.php">Go to admin landing page »</a>
		          </p>
							<?php
								$conn = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
								$sql = "SELECT * FROM templatemanager WHERE `templid` = " . $_GET['id'];
								$templresult = mysqli_query($conn, $sql);

								if (mysqli_num_rows($templresult) > 0) {
								// output data of each row
								$i=1;
								while($row = mysqli_fetch_assoc($templresult)) {
									$query1 = "SELECT `vendorname` FROM `vendorsmaster` WHERE `vendorid` = " . $row['templvendrid'] ;
									$row1 = mysqli_query($conn,$query1);
									$result1 = mysqli_fetch_array($row1);
							?>
							<div align="center">
								<h3>Upload PDF File : <?php echo "Template Name: " . $row['templname'] . " for Company Name: " . $result1[0] ?></h3>
								<form enctype="multipart/form-data" action="assets/processupload.php" method="POST">
								<p><input type="hidden" name="templid" value="<?php echo $_GET['id']; ?>" />
									<input type="file" id="fileToUpload" name="fileToUpload" /><br />
								<br />
  								<input type="submit" value="upload!" /></p>
								</form>
							</div>
							<?php
						} }
							 ?>

</div>
</div>
</div>
</div>
</div>

<?php
//Creating a random file name

include("assets/footer.php");
?>
