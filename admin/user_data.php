<?php
session_start();
//include('connection.php');
if (!isset($_SESSION['login_access'])) {
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>users | Boombotix</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
    
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/bootstrap-responsive.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/DT_bootstrap.css">

        <script src="js/jquery.min.js" language="javascript"></script>
        <script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.js"></script>
        <script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf-8" language="javascript" src="js/DT_bootstrap.js"></script>
        <script src="js/jquery.dataTables.columnFilter.js" type="text/javascript"></script>

        <script src="js/datatablesusers.js" language="javascript"></script>


        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .table th,.table td{padding:5px;}


            #navbar1 #navbar-inner1 {
                padding: 0;
            }
            #navbar1 #nav1 {
                margin: 0;
                display: table;
                width: 100%;
            }
            #navbar1 #nav1 li {
                display: table-cell;
                width: 1%;
                float: none;
            }
            #navbar1 #nav1 li a {
                font-weight: bold;
                text-align: center;
                /*        border-left: 1px solid rgba(255,255,255,.75);*/
                border-right: 1px solid rgba(0,0,0,.1);
            }
            #navbar1 #nav1 li:first-child a {
                border-left: 0;
                border-radius: 3px 0 0 3px;
            }
            #navbar1 #nav1 li:last-child a {
                border-right: 0;
                border-radius: 0 3px 3px 0;
            }
        </style>
    </head>

    <body>

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <div class="nav-collapse collapse">
                        <ul class="nav">
<!--                           <li class="active"><a href="products#2">Products</a></li>
                              <li><a href="coupons#2">Coupons</a></li>
                            <li><a href="category#3">Category</a></li>
                           <li><a href="filter#1">Filters</a></li>
                            <li><a href="tags#1">Tags</a></li>-->
                            
                        </ul>

                        <button type="button" class="btn" style="float:right;" onClick="location.href = 'logout'">Logout</button>
                        <button type="button" class="btn" style="float:right; cursor:crosshair; margin-right:10px">Welcome <span id="name"><?php
                                $name = ($_SESSION['login_access']) ? $_SESSION['login_access'] : "";

                                echo $name;
                                ?></span></button>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>

    <center><img src="img/loader.gif" id="loader-main" style="display:none; margin-top:100px; margin-bottom:100px" /></center>
    <div class="container" style="width: 1105px;; min-height: 540px;">
        <div style=" margin-top: 10px;margin-bottom: 20px;">
            <a href="add_user.php" ><button type="button" style=" outline: none;" class="btn-small btn-inverse" id="add_the_user">Create new user</button></a>
        </div>  

<!--        <div class="masthead">            
            <div class="navbar" id="navbar1">
                <div class="navbar-inner" id="navbar-inner1">
                    <div class="container">
                        <ul class="nav" id="nav1">
                            <li id="current" class="active" ><a class="links" href="#2" id="fs2">Products</a></li>
                            <li><a href="products_upload.php">Upload Products</a></li>                            
                        </ul>
                    </div>
                </div>
            </div> /.navbar 
        </div>-->

        <center><img src="img/loader.gif" id="loader-maindata" style="margin-top:100px; margin-bottom:100px" /></center>
        <center><strong><span style="font-size:16px; margin-top:50px; display:none" id="noapps">No users in this section.</span></strong></center>

        <div id="maindata">  

        </div> <!-- Main Data -->

    </div>
    <hr>

    <footer>
        <p align="center">&copy; 2013. Boombotix. All rights reserved.</p>
    </footer>

    <!-- Le javascript
    ================================================== -->
<!--    <script src="js/jquery.cookie.js"></script>-->
    <script src="js/bootstrap-alert.js"></script>

</body>
</html>