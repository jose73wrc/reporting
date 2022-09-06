<!doctype html>
<html>
<head>
<title>Business Reporting</title>
<meta name="description" content="Our first page">
<meta name="keywords" content="html tutorial template">
<meta charset="utf-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
<script src="view/view.js"></script>
<link rel="stylesheet" href="view/view.css">
</head>
<body>
    <h1>Business Report 2022</h1>
    <div class="container report">
        <div class="controls">
            <form name="controls" method="POST">
                <div class="btn-group" role="group"> 
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            load
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div>
                    
                    <input type="submit" name="control" class="btn btn-outline-secondary" value="save" />
                    <input type="submit" name="control" class="btn btn-outline-secondary" value="export" />           
                </div>
            </form>
        </div>       
        <div class="main">
            <div class="text"><?php include 'remarks.php';?></div>
            <div class=" row charts"></div>
            <div class="text"><?php include 'analysis.php';?></div>
            <div class=" row charts">
                <div class="col-6"><?php echo $b->get_balancesheet();?></div>
                <div class="col-6"><?php echo $r->get_ratios(); ?></div>
            </div>
            <div class="text"></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $c->get_cashflow();?></div>                
            </div>
            <div class="text"><?php include 'sales.php';?></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $i->get_incomestmt();?></div> 
                <div class="col-12"><?php echo $obj->current_projects('sales');?></div> 
                <div class="col-12"><?php echo $obj->get_prospects();?></div>               
            </div>
            <div class="text"><?php include 'marketing.php';?></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $obj->current_projects('mrkt');?></div>                
            </div>
            <div class="text"><?php include 'forward.php';?></div>
            <div class=" row charts">
                <div class="col-12"><?php echo $obj->get_meetings();?></div>                
            </div>
        </div>       
    </div>    
</body>
</html>
<?php
//
//
//
//