<?php
    global $dbh;
    global $presta_url; 
    $shop_key = 'R8V1QDRBLD4QCXEYIWI31LEEIJLZFGCG';
    //$presta_url ='http://localhost/presta/api/&ws_key='.$shop_key;
    $presta_url ='http://localhost/presta_service_sync/WebServiceSync.php';

    const MOODLE_KEY ='4a0dd9bcea1bd457f173421d5f7f9420';
    
    // TODO: изменить порт на 3306 - стандарт
    $dsn = 'mysql:dbname=' . $CFG->dbname . ';host='.$CFG->dbhost .';. port=3307';
    $user = $CFG->dbuser;
    $password = $CFG->dbpass;
    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'error occure: ' . $e->getMessage();
    }
