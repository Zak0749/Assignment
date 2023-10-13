<?php

// Imports
use database\Db;

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// If deck not specified 
if (!isset($_REQUEST["deck_id"])) {
    http_response_code(400);
    return;
}


// Establish Db connection
$db = new Db();

// Saves the deck_id
$result = $db->save($_REQUEST["deck_id"]);

// If save was successful
if ($result->isOk()) {
    // Response of `No Content`
    http_response_code(204);
} else {
    // Response of `Internal Server Error§`
    http_response_code(500);
}
