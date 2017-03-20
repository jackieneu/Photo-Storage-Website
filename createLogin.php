<?php 
ini_set('display_errors', 'On');
session_start();
	
if(isset($_POST['password']) && isset($_POST['username'])){
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//Connects to the database
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu","lambja-db","5tGW34Y2vYr1Gy5T","lambja-db");
	if(!$mysqli || $mysqli->connect_errno){
		$response_array['status'] = "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
	
	//Check if username is taken
	if(!($stmt = $mysqli->prepare("SELECT account_id FROM account WHERE username = '$username' "))){
		$response_array['status'] = "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
	}
	if(!$stmt->execute()){
		$response_array['status'] = "Execute failed: "  . $stmt->errno . " " . $stmt->errno;
	}
	$stmt->store_result();
	
	if($stmt->num_rows){
		//Username taken
		$response_array['status'] = "Username already taken, try a different username.";
		$stmt->close();
	}else{
		//Username available, add to database
		if(!($stmt = $mysqli->prepare("INSERT INTO account(username, password) VALUES (?,?)"))){
			$response_array['status'] = "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
		}
		if(!($stmt->bind_param("ss",$username,$password))){
			$response_array['status'] = "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			$response_array['status'] = "Execute failed: "  . $stmt->errno . " " . $stmt->error;
		} else {
			$response_array['status'] = 'success';
		}
		$stmt->close();
		}	
		
	echo json_encode($response_array);
}
?>