<?php

// используемое руководство: http://phpfaq.ru/pdo#multi


 $host = 'localhost';
    $db   = 'MyToDoList';
    $user='MyAdmin';
    $pass = '%Lena327207%';
    $charset = 'utf8';

/*$host = 'localhost';
    $db   = 'j91933ec_db';
    $user='j91933ec_db';
    $pass = 'qwe_2DB_wsx';
    $charset = 'utf8'; */


    /*$host = 'localhost';
    $db   = 'novastar_lena';
    $user='novastar_lena';
    $pass = '%Lena327207%';
    $charset = 'utf8';*/

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        $dbcon = new PDO($dsn, $user, $pass, $opt);
       
    } catch (PDOException $e) {
        die('Подключение не удалось: ' . $e->getMessage());
    }
    ?>