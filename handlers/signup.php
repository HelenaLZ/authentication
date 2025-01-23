<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = htmlspecialchars($_POST['login']);

    $email = filter_input(
        INPUT_POST,
        'email',
        FILTER_VALIDATE_EMAIL
    );

    $password = htmlspecialchars($_POST['password']);

    $password_confirm = htmlspecialchars($_POST['password_confirm']);

    if ($password !== $password_confirm) {
        $response = [
            "status" => false,
            "type" => 2,
            "message" => "Passwords dont match",
           ];
           echo json_encode($response);
           die();
    } else {
        // Validation of fields
        $error_fields = [];


        if ($login === '') {
              $error_fields[] = 'login';
        }

        if ($password === '') {
              $error_fields[] = 'password';
        }

        if ($email === '' || $email == false) {
            $error_fields[] = 'email';
        }

        if ($password_confirm === '') {
            $error_fields[] = 'password_confirm';
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

  //Password checking
        if (preg_match("/(^.{1,5}$)|([А-я]+)/", $password)) {
            $response = [
            "status" => false,
            "type" => 2,
            "message" => "Password consists of less than 6 symbols or russian letters are used. Please, create strong password.",
            ];
            echo json_encode($response);
            die();
        }

  // Сhecking if a login exists
        try {
            $limit = 2;
            $sql = "SELECT `id` FROM `users` WHERE `login` = :login LIMIT :limit";
             $sqlSelect = $dbcon->prepare($sql);
            $sqlSelect ->bindParam(':login', $login);
            $sqlSelect ->bindParam(':limit', $limit);
             $sqlSelect->execute();
             $result = $sqlSelect->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }


        if (isset($result)) {
            if (count($result) === 1) {
                $response = [
                "status" => false,
                "type" => 2,
                "message" => "Login is already used",
          //"result" => $result,
                ];
                echo json_encode($response);
                die();
            } elseif (count($result) > 1) {
                try {
                     $message = 'More than 1 similar Logins DB';
                     $created_on = 'NOW()';
                   // $sql = "INSERT INTO `error_logs` (`message`, `created_on`) VALUES ('More than 1 similar Logins DB', NOW())";
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
                "message" => "Something went wrong. Please try again.2",
       //"result" => $result,
                ];
                echo json_encode($response);
                die();
            }
        } else {
            $response = [
            "status" => false,
            "type" => 2,
            "message" => "Something went wrong. Please try again.3",

            ];
            echo json_encode($response);
            die();
        }


  // Сhecking if an email exists
        try {
            $sql = "SELECT `id` FROM `users` WHERE `email` = :email LIMIT :limit";
             $sqlSelect = $dbcon->prepare($sql);
            $sqlSelect ->bindParam(':email', $email);
            $sqlSelect ->bindParam(':limit', $limit);
             $sqlSelect->execute();
             $result = $sqlSelect->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }

        if (isset($result)) {
            if (count($result) === 1) {
                $response = [
                "status" => false,
                "type" => 2,
                "message" => "Email is already used",
                ];
                echo json_encode($response);
                die();
            } elseif (count($result) > 1) {
                try {
                    $message = 'More than 1 similar Emails DB';
                    $created_on = 'NOW()';
                        // $sql = "INSERT INTO `error_logs` (`message`, `created_on`) VALUES ('More than 1 similar Logins DB', NOW())";
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
           // "result" => $result,
                ];
                echo json_encode($response);
                die();
            }
        } else {
            $response = [
            "status" => false,
            "type" => 2,
            "message" => "Something went wrong. Please try again.5",

            ];
            echo json_encode($response);
            die();
        }



    //Registration


        $pass = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12,]);
        $confirmation_token = bin2hex(random_bytes(40));

        $user_data = array(
          'login' => $login,
          'email' => $email,
          'password' => $pass,
          'created_on' => 'NOW()',
          'updated_on' => 'NOW()'
        );

        try {
            $dbcon->beginTransaction();

            $sql = "INSERT INTO `users` (`login`, `email`, `password`, `created_on`, `updated_on`) VALUES (:login, 
        :email, :password, " . $user_data['created_on'] . ", " . $user_data['updated_on'] . ")";
            $sqlInsert = $dbcon->prepare($sql);
            $sqlInsert->execute(
                array(
                ':login' => $user_data['login'],
                ':email' => $user_data['email'],
                ':password' => $user_data['password'],

                )
            );

            $result = $dbcon->lastInsertId();
            $user_id = (isset($result)) ? $result : 0;



            $sql = "INSERT INTO `tokens` (`user_id`, `confirmation_token`) VALUES (:user_id, :confirmation_token)";
            $sqlInsert = $dbcon->prepare($sql);
            $sqlInsert->bindParam(':user_id', $user_id);
            $sqlInsert->bindParam(':confirmation_token', $confirmation_token);
            $sqlInsert->execute();

            $dbcon->commit();
        }




     /* try {


          $sql = "INSERT INTO `users` (`login`, `email`, `password`, `created_on`, `updated_on`) VALUES (:login,
          :email, :password, ".$user_data['created_on'].", ".$user_data['updated_on'].")";
          $sqlInsert = $dbcon->prepare($sql);
          $sqlInsert->execute(
            array(
              ':login' => $user_data['login'],
              ':email' => $user_data['email'],
              ':password' => $user_data['password'],

            )
          );

         $sql = "SELECT id from `users` where `email` = :email and `password` = :password";
          $sqlSelect = $dbcon->prepare($sql);
          $sqlSelect ->bindParam(':email', $email);
          $sqlSelect ->bindParam(':password', $pass);
          $sqlSelect->execute();
          $result = $sqlSelect->fetchAll();
          $user_id = (isset($result[0]['id']))?$result[0]['id']:0;



          $sql = "INSERT INTO `tokens` (`user_id`, `confirmation_token`) VALUES (:user_id, :confirmation_token)";
          $sqlInsert = $dbcon->prepare($sql);
          $sqlInsert ->bindParam(':user_id', $user_id);
          $sqlInsert ->bindParam(':confirmation_token', $confirmation_token);
          $sqlInsert->execute();


          $_SESSION['message'][] = "Check your email and confirm registration.
         Нажмите на <a href=\"./?confirmation_token=" . $confirmation_token . "\">ссылку</a> для подтверждения email";

         /* $subject = "Подтверждение регистрации {$_SERVER['SERVER_NAME']}";
    $msg = "Нажмите на <a href=\"http://{$_SERVER['SERVER_NAME']}?confirmation_token=" . $confirmation_token . "\">ссылку</a> для подтверждения email";
    $headers = "From: no-reply@{$_SERVER['SERVER_NAME']}";
    mail($to, $subject, $msg, $headers);

         echo "<br/><pre>";
            $sqlInsert->debugDumpParams();
          echo "</pre><br/><br/>";


         }*/


        catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        $_SESSION['token'] = "Check your email and confirm registration. 
        Нажмите на <a href=\"./?confirmation_token=" . $confirmation_token . "\">ссылку</a> для подтверждения email";
        $_SESSION['message'] = 123;
         $response = [
          "status" => true,
          "message" => "success",
             // "result" => $result,
         ];
         echo json_encode($response);
         die();
    }
}
