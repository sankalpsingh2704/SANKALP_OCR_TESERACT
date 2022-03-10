<?php
/*--------------------------------------------------------------------------------------------
|    @desc:        templadd.php
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
					</button><a class="navbar-brand" href="#"><img width="60%" height="60%" src="assets/iqlogo.png" /> </a>
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
				<div class="col-md-12">
					<h2>
						Template Management Area
					</h2>
          <p>
            <a class="btn btn-primary" href="index.php">Go back »</a>
          </p>
          <p>
              <form id="templcreate" name="templcreate" method="POST" action="assets/processtemplinsrt.php">
              <table style="width: 100%; border:1px dotted; border-radius:10px;" >
                <thead style="background-color: grey; color: white;">
                  <td style="width: 40%; ">Template Name</td>
                  <td style="width: 40%; ">Vendor Name</td>
                  <td style="width: 10%; ">Action</td>
                </thead>
                <tbody style="background-color: #f0f0f0; color: black;">
                  <tr>
                    <td style="width: 40%; padding-right: 10px; padding-top: 10px; "><input type="text" style="" id="templname" name="templname" class="form-control" placeholder="Enter Template Name "></td>
                    <td style="width: 40%; padding-left: 10px; padding-top: 10px; "><select id="vendorsel" name="vendorsel">
                      <?php
                        $conn = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
                        $sql1 = "SELECT * FROM `vendorsmaster`";
                        $vendrresult = mysqli_query($conn, $sql1);

                        if (mysqli_num_rows($vendrresult) > 0) {
                        // output data of each row
                        while($ro1 = mysqli_fetch_assoc($vendrresult)) { ?>
                          <option value="<?php echo $ro1['vendorid']; ?>"><?php echo $ro1['vendorname']; ?></option>
                    <?php
                        } }
                     ?>
                     </select></td></form>
                    <td style="width: 10%; ">
                    <p>
                      <button type="submit" form="templcreate" class="btn btn-primary btn btn-primary-default">Create Vendor</button>
          					</p>
                    </td>
                  </tr>
                </tbody>
              </table>

          </p>

				</div>
			</div>
		</div>
	</div>
</div>
<?php
include("assets/footer.php");
?>
