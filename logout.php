<?php
session_start();

unset($_SESSION['valid_user']);
session_destroy();

//Take user back to login page
header("Location:login.php");

?>