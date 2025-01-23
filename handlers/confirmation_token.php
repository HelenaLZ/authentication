<?php


try {

    $sql = "SELECT `user_id` FROM `tokens` WHERE `confirmation_token`=:confirmation_token";
    $sqlSelect = $dbcon->prepare($sql);
    $sqlSelect->bindParam(':confirmation_token', $confirmation_token);
    $sqlSelect->execute();
    $result = $sqlSelect->fetchAll();

} catch (PDOException $e) {
    echo 'Ошибка: ' . $e->getMessage();
    return false;
}


if (isset($result)) {
    if (count($result) === 1) {
        try {
            $verified = "Y";
            $user_id = $result[0]['user_id'];


            $sql = "UPDATE `users` SET `verified`=:verified WHERE `id`= :user_id";
            $sqlUpdate = $dbcon->prepare($sql);
            $sqlUpdate->bindParam(':verified', $verified);
            $sqlUpdate->bindParam(':user_id', $user_id);
            $sqlUpdate->execute();
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }

        try {
            $verified = "Y";
            $iduser = $result[0]['user_id'];
            $sql = "DELETE from `tokens` WHERE `confirmation_token`= :confirmation_token";
            $sqlDelete = $dbcon->prepare($sql);
            $sqlDelete->bindParam(':confirmation_token', $confirmation_token);

            $sqlDelete->execute();
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }



    if (count($result) > 1) {
        try {
            $message = 'More than 1 similar Tokens in DB ' . $confirmation_token;
            $created_on = 'NOW()';
            $sql = "INSERT INTO `error_logs` (`message`, `created_on`) VALUES (:message, " . "$created_on" . ")";
            $sqlInsert = $dbcon->prepare($sql);
            $sqlInsert ->bindParam(':message', $message);
            $sqlInsert->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        $_SESSION['message'][] = 'Something went wrong;';
        header('Location: ./');
    }


    if (count($result) === 0) {
        $_SESSION['message'][] = 'Incorrect link;';
        header('Location:. /');
    }
}
