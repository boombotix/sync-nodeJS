<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Boombotix</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
 
<!-- StyleSheet -->
<link rel="stylesheet" href="css/bootstrap.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.css" />
<script src="js/jquery.min.js" language="javascript"></script>
<script>
$(document).ready(function()
{
	$('#submit').click(function()
	{
            submitForm();
		
});
});
$(document).keypress(function(e) {
  if(e.which == 13) {
    // enter pressed
      submitForm();
  }
});

function submitForm()
{
    //var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
		var email = $('#email').val();
		var password = $('#password').val();
		if(email.length == 0 && password.length == 0)
		{
			$('#errorfielddiv').show();
			$('#errorfielddiv').html("Please fill all the fields.");
			return false;
		}
		if(email.length == 0 && password.length != 0)
		{
			$('#errorfielddiv').show();
			$('#errorfielddiv').html("Please fill in an email.");
			return false;
		}
		if(password.length == 0)
		{
			
			$('#errorfielddiv').show();
			
				$('#errorfielddiv').html("Please fill in a password to login.");
			

			return false;
		}

		$('#errorfielddiv').hide();
		$('#submit').hide();
		$('#loader').show();
			$.ajax({
							type: "POST",
							url: 'api-files/index.php?action=login',
							dataType: "json",
							data: { email : email, password : password },
							async:false,
							success: function(logindata) {
                                                            console.log(logindata);
							   if(logindata['log'] == 0)
							   {
								   
								   $('#errordiv').show('slide');
								   setTimeout(function() {
									$('#errordiv').hide();
								}, 3000);
								   $('#submit').show();
								   $('#loader').hide();
								
							   }
							   if(logindata['log'] == 1)
							   {
								
								  $('#successdiv').show();
								  setTimeout(function() {
									$('#successdiv').hide();
								}, 5000);
								//$.cookie('postmygreetings_username',logindata['data'][0]['user_name']);
//								$.cookie('Schlumberger_user_name',logindata['data'][0]['username']);
								location.href='user_data.php';
							   }
							},
							error: function(status) {
							}
				});
	
}


</script>
</head>
 
<body background="img/bg.png">
<!-- Main Container -->
<div style="margin-top:100px">
<center><span style="font-size:28px; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; color:#960">Boombotix</span></center><br />
        <div class="center span4 well" style="margin:0px auto">
            <legend align="center">Please Sign In</legend>
            <div class="alert alert-error" style="display:none" id="errordiv">
                Incorrect Username or Password!
            </div>
            <div class="alert alert-error" style="display:none" id="errorfielddiv">
             
            </div>
            <div class="alert alert-success" style="display:none" id="successdiv">
                You are Successfully Logged In! Redirecting to Dashboard...
            </div>
            
            <input type="text" id="email" class="span4" name="email" placeholder="Username" required />
            <input type="password" id="password" class="span4" name="password" placeholder="Password" required />
            <button type="button" name="submit" class="btn btn-primary btn-block" id="submit">Sign in</button>
            <center><img src="img/loader.gif" style="display:none" id="loader" /></center>
            
        </div><br />
<p align="center">&copy; 20<?php echo date('y'); ?> Boombotix. All rights reserved.</p>
</div>
<!--JavaScript  -->
<!--<script src="js/jquery-latest.js"></script>
<script src="js/bootstrap.js"></script>-->
<!--<script src="js/jquery.cookie.js"></script>-->
</body>
</html>