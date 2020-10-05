<?php
require_once 'config/config.php';
require_once 'vendor/autoload.php';

use Curl\Curl;

global $dbh;

//TODO: function parse_data/format_data_for_presta/encode_format
$xmlstring = file_get_contents('category.xml');

function api_service($dbh, $xmlstring, $data = null)
{
    $curl = new Curl();
    $resp = false;
    $is_moodle_request = check_which_system_make_request();
    //TODO: function format_data_for_moodle
    $data_request = file_get_contents('php://input');

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            try {
                if ($is_moodle_request) {
                    $data = parse_json($data_request);
                    //TODO: function parse_data/formata_data_for_presta/encode_format
                    $resp = $curl->post(PRESTA_URl.'categories&ws_key='.PRESTA_KEY.'?output_format=JSON', $xmlstring);
                } else {
                    $resp = $curl->post(PRESTA_URl.'categories&ws_key='.PRESTA_KEY.'?output_format=JSON', $xmlstring);
                    $curl->setHeader('prestashop', 'test');
                    $curl->setHeader('Authorization', MOODLE_KEY);
                    $resp = $curl->post(MOODLE_URL, $data_request);
                }
            } catch (\Throwable $th) {
                echo 'error occure' . $th->getMessage() . '<br>';
                $resp = false;
            }
            return $resp;
            //TODO: как обрабатываем get запрос?
        case 'GET':
            try {
                if ($is_moodle_request) {
                    $resp = $curl->get(PRESTA_URl.'categories&ws_key='.PRESTA_KEY.'?output_format=JSON');
                } else {
                    $resp = $curl->get(PRESTA_URl.'categories&ws_key='.PRESTA_KEY.'?output_format=JSON');
                    $resp = json_encode($resp);
                    echo $resp;
                    //$curl->setHeader('prestashop', 'test');
                    //$curl->get(MOODLE_URL, $data_request);
                }
            } catch (\Throwable $th) {
                echo 'error occure' . $th->getMessage() . '<br>';
                $resp = false;
            }
            return $resp;
        case 'PATCH':
            try {
                if ($is_moodle_request) {
                    //TODO: patch by id url 
                    $resp = $curl->patch(PRESTA_URl.'categories&ws_key='.PRESTA_KEY);
                } else {
                    $resp = $curl->patch(PRESTA_URl.'categories&ws_key='.PRESTA_KEY);
                    $curl->setHeader('prestashop', 'test');
                    $curl->patch(MOODLE_URL, $data_request);
                }
            } catch (\Throwable $th) {
                echo 'error occure' . $th->getMessage() . '<br>';
                $resp = false;
            }
            return $resp;
        case 'DELETE':
            try {
                if ($is_moodle_request) {
                    //TODO: patch by id url 
                    $resp = $curl->delete(PRESTA_URl.'categories&ws_key='.PRESTA_KEY);
                } else {
                    $curl->delete(PRESTA_URl.'categories&ws_key='.PRESTA_KEY);
                    $curl->setHeader('prestashop', 'test');
                    $curl->delete(MOODLE_URL, $data_request);
                }
            } catch (\Throwable $th) {
                echo 'error occure' . $th->getMessage() . '<br>';
                $resp = false;
            }
            return $resp;
        default:
            return false;
    }
}
//TODO: parse data/format data
function parse_json($json_data)
{
    $data_for_query = array();
    $jsonIterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator(json_decode($json_data, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($jsonIterator as $key => $val) {
        if (is_array($val)) {
            //echo "$key:\n".'<br>';
        } else {
            //echo "$key => $val\n";
            array_push($data_for_query, $key);
        }
    }
    return $data_for_query;
}

function check_which_system_make_request()
{
    $resp = false;
    foreach (getallheaders() as $name => $value) {
        if ($name == 'moodle' && $value == 'test') {
            $resp = true;
        }
    }
    return $resp;
}
api_service($dbh, $xmlstring);
