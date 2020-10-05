<?php

/**
 * RESTful web service entry point. The authentication is done via header tokens.
**/

define('NO_DEBUG_DISPLAY', true);
define('WS_SERVER', true);

require('../../config.php');
require_once("$CFG->dirroot/webservice/restful/locallib.php");

if (!webservice_protocol_is_enabled('restful')) {
    header("HTTP/1.0 403 Forbidden");
    debugging('The server died because the web services or the REST protocol are not enable',
        DEBUG_DEVELOPER);
    die;
}

$server = new webservice_restful_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN);
$server->run();
die;

