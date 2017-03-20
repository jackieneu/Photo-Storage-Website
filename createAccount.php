<?php session_start();
?>

<!DOCTYPE html>
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="jquery-2.1.0.min.js"></script>
	<script src="jquery.validate.min"></script>
	<title>Create an Account</title>
	
	<style>
		body {
			font-family: sans-serif;
		}
		
		#heading {
			font-size: 16pt;
			font-weight: bold;
			margin-bottom: 20px;
		}
		
		#header{
			background:#074266;
		}
		#center_header{
			background:#053350;
			height:100px;
			margin:auto;
			width:980px;
		}
		#left_header{
		   float:left;
		   font-size:40px;
		   line-height: 100px;
		   text-align: center;
		   width:370px;
		   color: #EEEEEE;
		}
		
		#frm{
			margin-top: 20px;
		}
		
		#displayHere{
			margin-top: 20px;
		}
	</style>
	
<script>
	$(document).ready(function(){
	
		$("#login").hide();
		
		//Validate Form
		$("#frm").validate({
			rules:{
				username : {
					required : true,
					minlength: 4
				},
				password : {
					required : true,
					minlength : 8
				},
				password2 : {
					required : true,
					equalTo : '#password'
				}
			}
		});
		
		$("#submit").click(function(){

			var user = ($("#username").val());
			var pass = ($("#password").val());
			
			//Verify fields are not blank
			if(user == "" || pass == ""){
				e.preventDefault();
			}
			
			$.ajax({
				url: "createLogin.php",
				type: "POST",
				data:{
					'password':pass, 
					'username':user
				},
				dataType:"json",
				success: fnsuccess,
				error: fnerror
			});
			
			function fnsuccess(serverReply){
				if (serverReply && serverReply.status=='success'){
					window.location.href = 'login.php';
				}else if (serverReply && serverReply.status){
					$("#login").show();
					$("#displayHere").text(serverReply.status);
				}else{
					fnerror();
				}
			}
			function fnerror() {
				$("#displayHere").text("An error occurred.");
				$("#login").show();
			}	
		});

	});
</script>

</head><body>

	<div id="header">
		<div id="center_header">
			<div id="left_header">
				Account Creation
			</div> <!--#left_header-->
		</div> <!--#center_header-->
	</div> <!--#header-->
	
	<form method="post" id="frm">
		<fieldset>
			<legend>Create an Account</legend>
				<p>Username: <input type="text" name="username" id="username"/></p>
				<p>Password: <input type="password" name="password" id="password" /></p>
				<p>Re-Enter Password: <input type="password" name="password2" id="password2" /></p>
		</fieldset>
		<input type="button" value="Login" id="submit"/>
	</form>
	
	<div id="displayHere"></div>
	
	<div id="login">Already have an account? <a href="login.php">Login</a></div>
	
</body></html>