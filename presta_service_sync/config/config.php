<?php

    const MOODLE_URL = 'http://localhost/moodle/webservice/restful/server.php';
    const MOODLE_KEY = '4a0dd9bcea1bd457f173421d5f7f9420';
    
    const PRESTA_URl = 'http://localhost/presta/api/';
    const PRESTA_KEY = 'XRU7BHTDQEG19AQZUZN2ENJM26N6DYBM';

    global $dbh;
    // TODO: изменить порт на 3306 - стандарт
    $dsn = 'mysql:dbname=prestashop;host=127.0.0.1;port=3307';
    $user = 'root';
    $password = '';
    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Подключение не удалось: ' . $e->getMessage();
    }
