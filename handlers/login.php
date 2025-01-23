<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['message'])) {
        if ($_SESSION['message'] == 123) {
            $message = $_SESSION['token'];

            $response = [
            "status" => true,
            "type" => 0,
            "message" => $message
            ];

            echo json_encode($response);

            unset($_SESSION['message']);


            die();
        }
    }



    $email = filter_input(
        INPUT_POST,
        'email',
        FILTER_VALIDATE_EMAIL
    );


    $password = htmlspecialchars($_POST['password']);

    $error_fields = [];

    if ($email == false) {
        $error_fields[] = 'email';
    }


    if ($password === '') {
          $error_fields[] = 'password';
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


 //Authorization
    try {
        $limit = 2;
        $sql = "SELECT `id`, `email`, `password`, `verified` FROM `users` WHERE `email` = :email LIMIT :limit";
         $sqlSelect = $dbcon->prepare($sql);
         $sqlSelect->bindParam(':email', $email);
         $sqlSelect->bindParam(':limit', $limit);
         $sqlSelect->execute();
         $result = $sqlSelect->fetchAll();
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }

    if (isset($result)) {
        if (count($result) === 0) {
            $response = [
            "status" => false,
            "type" => 2,
            "message" => "Register please",
            //"result" => $result,
            ];
            echo json_encode($response);
            die();
        } elseif (count($result) > 1) {
            try {
                $message = 'More than 1 similar Emails DB';
                $created_on = 'NOW()';
                $sql = "INSERT INTO `error_logs` (`message`, `created_on`) VALUES (:message, " . "$created_on" . ")";
                $sqlInsert = $dbcon->prepare($sql);
                $sqlInsert ->bindParam(':message', $message);

                $sqlInsert->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }


            $response = [
              "status" => false,
              "type" => 2,
              "message" => "Something went wrong. Please try again.4",
            ];
             echo json_encode($response);
             die();
        } elseif (count($result) === 1) {
            $verifiedDB = (isset($result[0]['verified'])) ? $result[0]['verified'] : 0;
            $passwordDB = (isset($result[0]['password'])) ? $result[0]['password'] : 0;




            if (password_verify($password, $passwordDB)) {
                $response = [
                "status" => true,
                   ];
                   echo json_encode($response);
                   die();
            } else {
                $response = [
                   "status" => false,
                   "type" => 2,
                   "message" => "Password is incorrect.",
                ];
                echo json_encode($response);
                die();
            }
        }
    } else {
        $response = [
        "status" => false,
        "type" => 2,
        "message" => "Something went wrong. Please try again.2",

        ];
        echo json_encode($response);
        die();
    }
}
