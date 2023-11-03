<?php

require "modules/database.php";
require "modules/helpers.php";
require "components/cards.php";

header('Cache-Control: no-cache,must-revalidate');

session_start();

error_reporting(0);

set_exception_handler(function ($e) {
    http_response_code(500);
    var_dump($e);
});
