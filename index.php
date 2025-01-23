<?php

ob_start();

session_start();


require_once "connect.php";


if (isset($_GET['confirmation_token']) && !empty($confirmation_token = $_GET['confirmation_token'])) {
    include "handlers/confirmation_token.php";
}

//Password recovery
if (isset($_GET['recovery_token'])) {
    include "handlers/recovery_token.php";
}


if (isset($_GET['recovery_two_set_new_pass'])) {
    include "handlers/recovery_two_set_new_pass.php";
}


//Session exit
if (isset($_GET['logout'])) {
    include "handlers/logout.php";
}

//To show personal account if $_SESSION['username'] exists

elseif (!isset($_GET['recovery_one']) && !isset($_GET['signup']) && !isset($_GET['recovery_two_set_new_pass'])) {
  //login
    include "handlers/login.php";
    include "views/login.php";
} elseif (isset($_GET['recovery_one'])) {
  //password recovery
    include "handlers/recovery_one.php";
    include "views/recovery_one.php";
} elseif (isset($_GET['signup'])) {
  //registration form
    include "handlers/signup.php";
    include "views/signup.php";
}
?>

<!DOCTYPE html>
<html lang = "en">
<head>
<meta charset = "UTF-8">
<title>Authorization and Registration</title>
<link rel ="stylesheet" href="assets/css/main.css">

</head>
</html>



