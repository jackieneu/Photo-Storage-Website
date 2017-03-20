<?php
ini_set('display_errors', 'On');

session_start();

//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","lambja-db","5tGW34Y2vYr1Gy5T","lambja-db");
if(!$mysqli || $mysqli->connect_errno){
	$response_array['status'] = "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

//Gather file data
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES["file1"])){
	$errorinfo = $_FILES["file1"]["error"];
	$filename = $_FILES["file1"]["name"];
	$tmpfile = $_FILES["file1"]["tmp_name"];
	$filesize = $_FILES["file1"]["size"]; 
	$filetype = $_FILES["file1"]["type"];

//Add photo

//Check that a file was chosen
if (!$tmpfile) { //if file not chosen
	echo "ERROR: Please browse for a file before clicking the upload button.";
	exit();
}	

	//Check that file is an image and the correct size
	if ($filetype == "image/jpeg" && $filesize < 4294967295) {
	
		//Check that photo name is unique
		if(!($stmt = $mysqli->prepare("SELECT count(photo_id) FROM photo WHERE name = ? "))){
			echo "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
		}
		if(!($stmt->bind_param("s",$filename))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->bind_result($count)){  //Count number of people in class
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->errno;
		}
		$stmt->fetch();
		$stmt->close();
		if($count > 0){
			echo "ERROR: Please choose a different file, another photo with this name already exists in the database";
			exit();
		}
		
		//Insert image into photo
		if(!($stmt = $mysqli->prepare("INSERT INTO photo(image) VALUES (?)"))){
			echo "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
		}
		$null = NULL;
		if(!($stmt->bind_param("b",$null))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!($stmt->send_long_data(0, file_get_contents($tmpfile)))){
			echo "Send Long Data failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
		}
		$photo_id = $stmt->insert_id;
		$stmt->close();
		
		//Insert name into photo
		if(!($stmt = $mysqli->prepare("UPDATE photo SET name = ? WHERE photo_id = ?"))){
			echo "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
		}
		if(!($stmt->bind_param("si",$filename,$photo_id))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
		} else {
			echo "Added " . $stmt->affected_rows . " rows to photo.";
		}
		$stmt->close();
		
		//Insert into account_photo
			
			//Get account_id
			if(!($stmt = $mysqli->prepare("SELECT account_id FROM account WHERE username = ? "))){
				echo "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
			}
			if(!($stmt->bind_param("s",$_SESSION['valid_user']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->bind_result($account_id)){  //Account Id of logged in user
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->errno;
			}
			$stmt->fetch();
			$stmt->close();
		
			if(!($stmt = $mysqli->prepare("INSERT INTO account_photo(account_id, photo_id) VALUES (?,?)"))){
				echo "Prepare failed: "  . $mysqli->errno . " " . $mysqli->error;
			}
			if(!($stmt->bind_param("ii",$account_id,$photo_id))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			}
			$stmt->close();
	} else {
		echo "Only jpegs under 4MB are invited to this party."; 
	}
}

?>