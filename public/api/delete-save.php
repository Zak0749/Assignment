<?php
// Imports
use database\Db;

use function helpers\get_body;

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

$body = get_body();


// Establish Db connection
$db = new Db();

// Removes the deck from the saved list
$result = $db->deleteSave($_REQUEST["deck_id"]);

// If removal was successful
if ($result->isOk()) {
    // Response of `No Content`
    http_response_code(204);
} else {
    // Response of `Internal Server Error`
    http_response_code(500);
}
