<?php

if ($_SESSION['recovery_token'] == $_GET['recovery_token']) {
    include "views/recovery_two_set_new_pass.php";


    exit;
} else {

}
