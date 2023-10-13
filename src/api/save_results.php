<?php 

// Imports
use database\Db;



// If deck not specified 
if (!isset($body["deck_id"])) {
    http_response_code(400);
    return;
}

// If score not specified 
if (!isset($body["score"])) {
    http_response_code(400);
    return;
}

// Establish database connection
$db = new Db();

$result = $db->savePlay($body["deck_id"], $body["score"]);



if ($result->isOk()) {
    http_response_code(204);
} else {
    http_response_code(500);
}