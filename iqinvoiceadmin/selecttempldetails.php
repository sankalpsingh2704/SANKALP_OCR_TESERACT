<?php
include("assets/header.php");
/*--------------------------------------------------------------------------------------------
|    @desc:        selecttempldetails.php
|    @author:      Vivek P Nair
|    @url:         http://www.mynameisvivek.in
|    @date:        24 May 2017
|    @email        deadbrainviv@gmail.com
|    @license:     NA
---------------------------------------------------------------------------------------------*/
include('config.php');    //include of db config file
if(!isset($_GET['id']))
{
  header("Location: index.php");
}
$vofs = "<option value='0'>Select one Item</option>";
$filename = returnfilename($_GET['id']);
$connect = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
$sqlqry = "SELECT * FROM templatedata WHERE templatefilename ='" . $filename . "'";
echo $sqlqry;
$result = mysqli_query($connect, $sqlqry);
if (mysqli_num_rows($result) > 0){
echo "test";
  while($row = mysqli_fetch_assoc($result))
  {
    $vofs .= "<option value='" . $row['templatedataid'] . "'>" . $row['datafield7'] . "</option>";
  }
}
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
						Template Fieldset
					</h2>
					<p>
						To manage mapping of areas in a template
					</p>
          <h3>
            Manage files for a new template
          </h3>
          <p>
            <?php

            $conn = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
            $sql = "SELECT * FROM  `fieldstofetch`";
            $fieldsres = mysqli_query($conn, $sql);
            if (mysqli_num_rows($fieldsres) > 0) {
            // output data of each row
              while($row = mysqli_fetch_assoc($fieldsres)) {
                  $fields[] = $row['parameterstring'];
            }
          }
            else {
              echo "wrong results obtained.";
            }


            ?>

            <form id="generate-form" type="POST" method="post" action="assets/processmappings.php">
                <?php foreach($fields as $field) {?>
                    <label>
                        <?php echo "$field: "; ?>
                        <input type="hidden" value="<?php echo $_GET['id']; ?>" id="idoftemplate" name="idoftemplate">
                        <select name="vof  <?php echo "$field: "; ?>" id="vof  <?php echo "$field: "; ?>">
                            <?php echo $vofs; ?>
                        </select>
                        <!-- <input type="text" name="<?php echo $field; ?>" /> -->
                    </label><br/>
                <?php } ?>
                <input type="submit" name="submit" />
            </form>
          </p>
				</div>
				<div class="col-md-6">
          <div style="width:100%; height:100%;"><iframe src="uploads/<?php echo $filename; ?>" style="width:100%; height:750px;"></iframe></div>
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
