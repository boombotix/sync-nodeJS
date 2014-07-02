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
        <script>
            $(document).ready(function()
            {
                var popup_delete_left = (screen.width / 2) - 200;

                $("#useractionmodal").css("margin-left", popup_delete_left);
                $("#useractionmodal").css("left", "0");

                $("#no_delete").click(function() {
                    $("#useractionmodal").modal('hide');
                });

                $("#user_submit").click(function() {
                    $("#useractionmodal").modal('hide');

                    $("#bodypart").attr("style", "opacity:0.3;filter:alpha(opacity=100);");
                    $("#loading").css("display", "block");
                    $(".modalback").css('display', 'block');


                    if (check == 1)
                    {
                        $.ajax({
                            type: "POST",
                            url: 'api-files/index.php?action=stop_bot',
                            dataType: "json",
                            data: {dj_id: userID},
                            async: false,
                            success: function(data) {

                                console.log(data);

                                if (data['log'] == 1)
                                {
                                    window.location.href = 'user_data.php';
                                }
                                else
                                {
                                    $("#bodypart").removeAttr('style');
                                    $("#loading").css("display", "none");
                                    $(".modalback").css('display', 'none');
                                    alert(data['error']);
                                }

                            }
                        });
                    }
                    else if (check == 2)
                    {

                        $.ajax({
                            type: "POST",
                            url: 'api-files/index.php?action=play_bot_playlist',
                            dataType: "json",
                            data: {bot_id: userID},
                            async: false,
                            success: function(data) {

                                console.log(data);

                                if (data['log'] == 1)
                                {
                                    window.location.href = 'user_data.php';
                                }
                                else
                                {
                                    $("#bodypart").removeAttr('style');
                                    $("#loading").css("display", "none");
                                    $(".modalback").css('display', 'none');
                                    alert(data['error']);
                                }

                            }
                        });
                    }

                });

                $("#user_edit_name").keyup(function() {

                   $("#validate_name").css("display", "none");

                });


                $("#editformSub").click(function() {

                    var name = $("#user_edit_name").val();

                    if (name == "")
                    {
                        $("#validate_name").css("display", "block");
                      //  $("#user_edit_name").css("border", "1px solid #953b39");

                        return false;

                    }
                    else
                    {
                        $("#useractionmodal").modal('hide');
                        $("#bodypart").attr("style", "opacity:0.3;filter:alpha(opacity=100);");
                        $("#loading").css("display", "block");
                        $(".modalback").css('display', 'block');

                        $.ajax({
                            type: "POST",
                            url: 'api-files/index.php?action=edit_user',
                            dataType: "json",
                            data: {user_id: userID,user_name:name},
                            async: false,
                            success: function(data) {

                                console.log(data);

                                if (data['log'] == 1)
                                {
                                    window.location.href = 'user_data.php';
                                }
                                else
                                {
                                    $("#bodypart").removeAttr('style');
                                    $("#loading").css("display", "none");
                                    $(".modalback").css('display', 'none');
                                    alert("error, try again");
                                }

                            }
                        });



                    }




                });



            });


        </script>

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
            #loading{
                position:fixed;
                width:30em;
                height:18em;
                display:none;
                top:50%;
                left: 50%;
                margin: -100px 0px 0px -100px;

            }

            .modalback
            {
                opacity: 0.8;
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1051;
                /*background-color: #333333;*/
            }
        </style>
    </head>

    <body>
        <div id="loading"><img src="img/ajax-loader.gif"></div>
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
        <div id="bodypart">
            <center><img src="img/loader.gif" id="loader-main" style="display:none; margin-top:100px; margin-bottom:100px" /></center>

            <div class="modal fade" id="useractionmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style=" width: 400px; display: none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style=" background-color: rgba(128, 128, 128, 0.25);">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Confirm Box</h4>
                        </div>
                        <div class="modal-body" style=" font-size: 22px;margin: 0 auto;">


                            <div style=" margin: 25px auto;" id="user-action-data"></div>

                            <div style="width: 91px; margin: 0 auto;">

                                <button id="user_submit" type="button" style=" outline:none" class="btn btn-small btn-danger" >YES</button>
                                <button id="no_delete" type="button" style="outline:none" class="btn btn-small btn-success">NO</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button"  class="btn" data-dismiss="modal">Close</button>

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div class="modal fade" id="useredit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style=" width: 500px; display: none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style=" background-color: rgba(128, 128, 128, 0.25);">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">User Edit</h4>
                        </div>
                        <div class="modal-body" style=" font-size: 22px;width: 332px;margin: 0 auto;">    


                            <div style="margin-top: 20px;">
                                <span>Name:</span> 
                                <span><input type="text" id="user_edit_name" placeholder="Name" name="user_name" style=" margin-left: 40px; margin-bottom: 0px" /></span>
                                <span id="validate_name" style="display: none; color: red; font-size: 12px; position: absolute;font-weight: bold; margin-left: 118px;" >This field is required.</span>
                            </div>


                            <div style="margin-top:40px;margin-left:112px;"><button type="button" id="editformSub" class="btn btn-primary" >Submit</button></div>

                        </div>
                        <div class="modal-footer">
                            <button type="button"  class="btn" data-dismiss="modal">Close</button>                    
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->    



            <div class="container" style="width: 1105px;; min-height: 540px;">


                <center><img src="img/loader.gif" id="loader-maindata" style="margin-top:100px; margin-bottom:100px" /></center>
                <center><strong><span style="font-size:16px; margin-top:50px; display:none" id="noapps">No users in this section.</span></strong></center>



                <div id="maindata">

                </div> <!-- Main Data -->

            </div>
            <hr>

            <footer>
                <p align="center">&copy; 20<?php echo date('y'); ?> Boombotix. All rights reserved.</p>
            </footer>
        </div>
        <div class="modalback" style=" display: none;"></div>
        <!-- Le javascript
        ================================================== -->
    <!--    <script src="js/jquery.cookie.js"></script>-->
        <script src="js/bootstrap-alert.js"></script>
        <script type="text/javascript" charset="utf-8" language="javascript" src="js/bootstrap.min.js"></script>

    </body>
</html>