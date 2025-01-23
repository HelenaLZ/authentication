<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass1 = htmlspecialchars($_POST['pass1']);
    $pass2 = htmlspecialchars($_POST['pass2']);
    $email = $_SESSION['email'];
    $error_fields = [];

    if ($pass1 === '') {
        $error_fields[] = 'pass1';
    }

    if ($pass2 === '') {
        $error_fields[] = 'pass2';
    }


    if (!empty($error_fields)) {
        $response = [
             "status" => false,
             "type" => 1,
             "message" => "Check fields",
             "fields" => $error_fields
        ];

        echo json_encode($response);
        die();
    }

    if ($pass1 !== $pass2) {
        $response = [
         "status" => false,
         "type" => 1,
         "message" => "Passwords dont match",
         "fields" => $error_fields
        ];

        echo json_encode($response);
        die();
    }


  //Password checking
    if (preg_match("/(^.{1,5}$)|([А-я]+)/", $pass2)) {
        $response = [
        "status" => false,
        "type" => 2,
        "message" => "Password consists of less than 6 symbols or russian letters are used. Please, create strong password.",
        ];
        echo json_encode($response);
        die();
    }

 // $email = $_SESSION['email'];

    $pass = password_hash($pass2, PASSWORD_BCRYPT, ['cost' => 12,]);

 //$pass = $pass2;

    try {
        $limit = 2;
        $sql = "UPDATE `users` SET `password` = :pass WHERE `email` = :email";
         $sqlUpdate = $dbcon->prepare($sql);
         $sqlUpdate->bindParam(':pass', $pass);
         $sqlUpdate->bindParam(':email', $email);
         $sqlUpdate->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }

      $response = [
        "status" => true,
      ];
       echo json_encode($response);
       die();
}
