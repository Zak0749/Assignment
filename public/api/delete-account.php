<?php
// Imports
use database\Db;

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// Establish Db connection
$db = new Db();

$result = $db->deleteAccount($_SESSION["user_id"]);

// If the deletion was successful
if ($result->isOk()) {
    // log the end user out
    unset($_SESSION["user_id"]);

    // Response of `No Content`
    http_response_code(204);
} else {
    // Response of `Internal Server Error`
    http_response_code(500);
}
