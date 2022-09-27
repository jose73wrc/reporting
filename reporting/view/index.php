<!doctype html>
<html>
<head>
<title>Business Reporting</title>
<meta name="description" content="Reporting">
<meta name="keywords" content="html tutorial template">
<meta charset="utf-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

<!-- jQuery library -->
<!--<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>-->

<!-- Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.ckeditor.com/4.19.1/standard/ckeditor.js"></script>

<script src="view/view.js"></script>
<link rel="stylesheet" href="view/view.css">
</head>
<body>
    <h1><?php echo $obj->get_title();?></h1>
    <div class="container report">
        <div class="controls">
            <form name="controls" method="POST">
                <div class="btn-group" role="group">
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        load
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php echo $obj->load_options();?>
                    </div>
                  </div>
                  <div>
                      <!--<button type="button" data-toggle="modal" data-target="#exampleModal" class="btn btn-outline-secondary">load</button>-->
                      <input type="submit" name="control" class="btn btn-outline-secondary" value="save" />
                      <input type="submit" name="control" class="btn btn-outline-secondary" value="export" />
                      <!--<a type="button" name="control" class="btn btn-outline-secondary" value="download">download</a>-->           
                  </div>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Data
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                      <a class="dropdown-item"  name="control" value="qftf">QuickFixTrips</a>
                      <a class="dropdown-item" href="?source=udi" name="control" value="udi">UDI Group LLC</a>
                    </div>
                  </div>
                </div>
            </form>
        </div>       
        <div class="main">
            <?php $thedb = "i3797837_db2.db2"  ?>
            <div class="text"><?php include 'remarks.php';?></div>
            <div class=" row charts"></div>
            <div class="text"><?php include 'analysis.php';?></div>
            <div class=" row charts">
            <?php $h = $b->get_balancesheet($thedb);?>
                <div class="col-6"><?php echo $h[0];?></div>
                <div class="col-6"><?php echo $r->get_ratios($thedb); ?></div>
            </div>
            <div class="text"></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $c->get_cashflow($thedb);?></div>                
            </div>
            <div class="text"><?php include 'sales.php';?></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $i->get_incomestmt($thedb);?></div> 
                <div class="col-12"><?php echo $obj->current_projects('sales', 'i3797837_db8.db');?></div> 
                <div class="col-12"><?php echo $obj->get_prospects($thedb);?></div>               
            </div>
            <div class="text"><?php include 'marketing.php';?></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $obj->current_projects('mrkt', 'i3797837_db8.db');?></div>                
            </div>
            <div class="text"><?php include 'forward.php';?></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $obj->get_meetings($thedb);?></div>                
            </div>
        </div>       
    </div>    
   <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>  
</body>
</html>
<?php