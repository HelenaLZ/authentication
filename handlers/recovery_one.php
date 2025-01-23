<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(
        INPUT_POST,
        'email',
        FILTER_VALIDATE_EMAIL
    );

    $error_fields = [];

    if ($email == false) {
        $error_fields[] = 'email';
    }


    if (!empty($error_fields)) {
        $response = [
             "status" => false,
             "type" => 1,
             "message" => "Check email field",
             "fields" => $error_fields
        ];

        echo json_encode($response);

        die();
    }

    $limit = 2;
    try {
        $sql = "SELECT `id`, `verified` FROM `users` WHERE `email` = :email LIMIT :limit";
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
        if (count($result) === 0) {
            $response = [
            "status" => false,
            "type" => 2,
            "message" => "You're trying to recovery a non-existent account",
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
            if ($result[0]['verified'] == 'N') {
                $response = [
                "status" => false,
                "type" => 2,
                "message" => "Before entering the account, activate it. Folow link at your email.",
                ];
                echo json_encode($response);
                die();
            } else {
                $recovery_token = bin2hex(random_bytes(40));
                try {
                    $sql = "INSERT INTO `recovery_tokens` (`user_id`, `recovery_token`) VALUES (:user_id, :recovery_token)";
                    $sqlInsert = $dbcon->prepare($sql);
                    $sqlInsert->bindParam(':user_id', $result[0]['id']);
                    $sqlInsert->bindParam(':recovery_token', $recovery_token);
                    $sqlInsert->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                    return false;
                }


                $_SESSION['recovery_token'] = $recovery_token;
                $_SESSION['email'] = $email;
                $response = [
                "status" => false,
                "message" => "Press link <a href=\"./?recovery_token=" . $recovery_token . "\">ссылку</a> to set new password",
                            ];
                echo json_encode($response);
                die();
            }


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
}
