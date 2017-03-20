<?php 
//Turn on error reporting
ini_set('display_errors', 'On');

session_start();

//Check Session
if (!(isset($_SESSION['valid_user']))){
	//Not logged in, redirect to login page
		header("Location:login.php");
}

//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","lambja-db","5tGW34Y2vYr1Gy5T","lambja-db");
if($mysqli->connect_errno){
	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

?>

<!DOCTYPE html>
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="jquery-2.1.0.min.js"></script>
	<title>Photo Database</title>
	
	<style>
		body, div {
			font-family: sans-serif;

		}
		
		#wrapper{
			background:#FFFFFF;

		}  
		
		#miniwrapper{
			background:#FFFFFF;
			margin: 0px 20px 0px 20px;
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
		#menu{
			float:right;
			height: 30px;
			margin: 42px 0 0;
			width: 100px;
		}
		#menu a{
			color: #EEEEEE;
			font-size: 15px;
			font-weight: bold;
			padding: 8px 12px;
		}	
		#menu a:hover{
		   background: #1E90FF url(images/hover-menu-items.png) repeat-x;
		}
		
		
		#heading2 {
			font-size: 16pt;
			margin-top: 20px;
		}
		
		#welcome{
			font-size: 16pt;
		}
		#logout{
			font-size: 10pt;
		}
				.wrap {
			overflow: hidden;
			margin: 10px;
		}
		.box {
			float: left;
			position: relative;
			width: 20%;
			padding-bottom: 20%;
		}
		.boxInner {
			position: absolute;
			left: 10px;
			right: 10px;
			top: 10px;
			bottom: 10px;
			overflow: hidden;
		}		
		.boxInner img {
			height: 70%;
		}	
		.boxInner .titleBox {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;	
			margin-bottom: 10px;
			background: #000;
			background: rgba(0, 0, 0, 0.5);
			color: #FFF;
			padding: 10px;
			text-align: center;;
		}
		body.no-touch .boxInner:hover .titleBox, body.touch .boxInner.touchFocus .titleBox {
		   margin-bottom: 0;
		}	

	</style>
	
<script>
function _(el){
	return document.getElementById(el);
}
function uploadFile(){
	var file = _("file1").files[0];
	var formdata = new FormData();
	formdata.append("file1", file);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress",progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	//UPDATE FILE NAME
	ajax.open("POST", "filetest.php");
	ajax.send(formdata);
}

function progressHandler(event){
	_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
	var percent = (event.loaded / event.total) * 100;
	_("progressBar").value = Math.round(percent);
	_("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
}
function completeHandler(event){
	_("status").innerHTML = event.target.responseText;
	_("progressBar").value = 0;
}
function errorHandler(event){
	_("status").innerHTML = "Upload Failed";
}
function abortHandler(event){
	_("status").innerHTML = "Upload Aborted";
}
</script>

</head><body>
	
	<div id="wrapper">
	
	<div id="miniwrapper">
	
	<!-- HEADER -->
		<div id="header">
			<div id="center_header">
				<div id="left_header">
					Photo Database
				</div> <!--#left_header-->
				
				<div id="menu">
					<a href="logout.php">Logout</a>
				</div> <!--#menu-->
			</div> <!--#center_header-->
		</div> <!--#header-->
			
	<!-- SUB-HEADER -->
	<div id="preview">
		<div id="center_preview">
	
			<div id="welcome">
			<?php
			if (isset($_SESSION['valid_user'])){
				echo 'Welcome '.$_SESSION['valid_user'].' !';
			}
			?>
			</div><!--#welcome-->
			
			<div id="logout">
			<?php
			if (isset($_SESSION['valid_user'])){
				echo 'If you are not '.$_SESSION['valid_user'].'<a href="logout.php"> logout</a>';
			}
			?>
			</div> <!--#logout-->
		</div> <!--#center_preview-->
	</div> <!--#preview-->
			
	<!--File Uploader -->
	<div id="heading2">Upload a Photo</div>
	<form id="upload_form" enctype="multipart/form-data" method="post">
		<input type="file" name="file1" id="file1"><br>
		<input type="button" value="Upload File" onclick="uploadFile()">
		<progress id="progressBar" value="0" max="100" style="width:300px;"></progress>
		<h3 id="status"></h3>
		<p id="loaded_n_total"></p>
	</form>
	
	<!--Photo Display-->
	<div id="heading2">
		<?php
		echo $_SESSION['valid_user'] . "'s Photos";
		?>
	</div>
	
	<div id="display_photos">
		<?php
		if(!($stmt = $mysqli->prepare("
			SELECT photo.photo_id, photo.name FROM photo
			INNER JOIN account_photo ON photo.photo_id = account_photo.photo_id
			INNER JOIN account ON account_photo.account_id = account.account_id
			WHERE account.username = ? "))){
			echo "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
		}
		if(!($stmt->bind_param("s",$_SESSION['valid_user']))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $stmt->errno . " " . $stmt->errno;
		}
		if(!$stmt->bind_result($photo_id, $name)){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->errno;
		}
		echo "<div class='wrap'>";
		while($stmt->fetch()){
			echo "<div class='box'> <div class='boxInner'>" . "<a href='fileview.php?id=" . $photo_id . "'>" . "<img src='fileview.php?id=" . $photo_id . "'>" . "</a>" . "<div class='titleBox'>" . $name . "</div> </div> </div>";
		}
		echo "</div>";
		$stmt->close();
		?>
	</div> <!--#display_photos-->
	
	</div> <!--#miniwrapper -->
	
	</div> <!--#wrapper -->
	
</body></html>