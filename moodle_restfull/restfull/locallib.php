<?php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/webservice/lib.php");
require_once("classes/php-curl-class/vendor/autoload.php");
require_once 'config.php';

use Curl\Curl;

/**
 * REST service server implementation.
 */

class webservice_restful_server
{
    private $pdo;
    private $curl;
    private $prestashop_url;
    private $resp;
    private $data_for_query;

    public function __construct($authmethod)
    {
        global $dbh;
        global $presta_url;
        $this->pdo = $dbh;
        $this->prestashop_url = $presta_url;
        $this->resp = false;
        $this->curl = new Curl();
        $this->data_for_query = array();
    }
    public function api_service()
    {
        $is_good_key = $this->check_key();
        if ($is_good_key == false) {
            header("HTTP/1.1 401 Unauthorized");
            echo 'auth key incorrect';
            exit;
        }
        //TODO: validator
        //TODO: change hardcode data curl header
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $json_data = file_get_contents('php://input');
                //$this->parse_json($json_data);
                $is_presta = $this->check_which_system_make_request();
                if ($is_presta) {
                    $this->resp = $this->sql_query_executer('insert', $this->data_for_query);
                    if ($this->resp == false) {
                        $this->throw_err();
                    }
                } else {

                    $this->resp = $this->sql_query_executer('insert', $this->data_for_query);
                    if ($this->resp) {
                        $this->curl->setHeader('moodle', 'test');
                        $this->resp = $this->curl->post($this->prestashop_url, $json_data);
                    } else {
                        $this->throw_err();
                    }
                }
                return $this->resp;
            case 'PATCH':
                $is_presta = $this->check_which_system_make_request();
                if ($is_presta) {
                    $this->resp = $this->sql_query_executer('update', $this->data_for_query);
                    if ($this->resp == false) {
                        $this->throw_err();
                    }
                } else {
                    $this->curl->setHeader('moodle', 'test');
                    $this->resp = $this->sql_query_executer('update', $this->data_for_query);
                    if ($this->resp == false) {
                        $this->throw_err();
                    }
                    $this->resp = $this->curl->patch($this->prestashop_url);
                    if ($this->resp == false) {
                        $this->throw_err();
                    }
                }
                return $this->resp;
            case 'GET':
                $this->resp = $this->sql_query_executer('show');
                if ($this->resp == false) {
                    $this->throw_err();
                }
                header('Content-Type: application/json');
                echo $this->resp;
            case 'DELETE':
                $is_presta = $this->check_which_system_make_request();
                if ($is_presta) {
                    $this->resp = $this->sql_query_executer('delete', $this->data_for_query);
                    if ($this->resp == false) {
                        $this->throw_err();
                    }
                } else {
                    $this->curl->setHeader('moodle', 'test');
                    $this->resp = $this->sql_query_executer('delete', $this->data_for_query);
                    if ($this->resp == false) {
                        $this->throw_err();
                    }
                    $this->resp = $this->curl->delete($this->data_for_query);
                }
                return $this->resp;
            default:
                return false;
        }
    }
    public function parse_json($json_data)
    {
        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($json_data, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($jsonIterator as $key => $val) {
            if (is_array($val)) {
                //echo "$key:\n".'<br>';
            } else {
                //echo "$key => $val\n";
                array_push($this->data_for_query, $key);
            }
        }
    }
    public function sql_query_executer($action, $data = null)
    {
        switch ($action) {
            case 'show':
                try {
                    $show_course = $this->pdo->prepare('SELECT * FROM `moodle`.`mdl_course`');
                    $show_course->execute();
                    $courses = $show_course->fetchAll(PDO::FETCH_ASSOC);
                    $this->resp = json_encode($courses, JSON_UNESCAPED_UNICODE);
                    //echo 'course was selected' . '<br>';
                } catch (\Throwable $th) {
                    echo 'error occure' . $th->getMessage() . '<br>';
                    $this->resp = false;
                }
                return $this->resp;
            
            //TODO: data parsing
            case 'insert':
                try {
                    $insert_course = $this->pdo->prepare("INSERT INTO `moodle`.`mdl_course_categories` (`name`, `sortorder`, `timemodified`, `depth`, `path`) 
                                                                VALUES ('test_category', '10000', '1600849859', '1', '/1');");
                    //foreach ($data as $category) {
                    //   /  $insert_course->bindParam(':category', $category, PDO::PARAM_STR);
                        $this->resp = $insert_course->execute();
                    //}
                    echo 'course was added' . '<br>';
                } catch (\Throwable $th) {
                    echo 'error occure' . $th->getMessage() . '<br>';
                    $this->resp = false;
                }
                return $this->resp;

            case 'update':
                try {
                    $update_course = $this->pdo->prepare("UPDATE `moodle`.`mdl_course` SET `fullname`='test' WHERE  `id`=28");
                    $this->resp = $update_course->execute();
                    //echo 'course was updated' . '<br>';
                } catch (\Throwable $th) {
                    echo 'error occure' . $th->getMessage() . '<br>';
                    $this->resp = false;
                }
                return $this->resp;
            case 'delete':
                try {
                    $delete_course = $this->pdo->prepare('DELETE FROM `moodle`.`mdl_course` WHERE  id >1 ORDER BY `id` DESC LIMIT 1 ');
                    $this->resp = $delete_course->execute();
                    //echo 'last course by id was deleted' . '<br>';
                } catch (\Throwable $th) {
                    echo 'error occure' . $th->getMessage() . '<br>';
                    $this->resp = false;
                }
                return $this->resp;
            default:
                return false;
        }
    }

    public function check_key()
    {
        foreach (getallheaders() as $name => $value) {
            if ($name == 'Authorization' && $value == MOODLE_KEY) {
                $this->resp = true;
            }
        }
        return $this->resp;
    }

    public function check_which_system_make_request()
    {
        foreach (getallheaders() as $name => $value) {
            if ($name == 'prestashop' && $value == 'test') {
                $this->resp = true;
                break;
            } else {
                $this->resp = false;
            }
        }
        return $this->resp;
    }
    //TODO: details response
    public function throw_err()
    {
        header('HTTP/1.1 500 Internal Server Error');
        die();
    }

    public function run()
    {
        global $CFG, $SESSION;
        $this->api_service($this->curl);
        die;
    }
}
