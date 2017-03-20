<?php session_start();
?>

<!DOCTYPE html>
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="jquery-2.1.0.min.js"></script>
	<title>Login</title>
	
	<style>
		body {
			font-family: sans-serif;
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
	
		
		$("#submit").click(function(){
		
			var user = ($("#username").val());
			var pass = ($("#password").val());
			
			$.ajax({
				url: "checklogin.php",
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
					window.location.href = 'databaseMain.php';
				}else if (serverReply && serverReply.status){
					$("#displayHere").text(serverReply.status);
				}else{
					fnerror();
				}
			}
			function fnerror() {
				$("#displayHere").text("An error occurred.");
			}	
		});
		
	});
</script>

</head><body>
	
		<div id="header">
			<div id="center_header">
				<div id="left_header">
					Please Login
				</div> <!--#left_header-->
			</div> <!--#center_header-->
		</div> <!--#header-->
	
	<form method="post" id="frm">
		<fieldset>
			<legend>Login</legend>
				<p>Username: <input type="text" name="username" id="username" /></p>
				<p>Password: <input type="password" name="password" id="password" /></p>
		</fieldset>
		<input type="button" value="Login" id="submit"/>
	</form>

	<div id="displayHere"></div>
	
	<div id="create">Don't have an account? <a href="createAccount.php">Create an Account</a></div>
	
</body></html>