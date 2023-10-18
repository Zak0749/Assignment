<?php
// Sets the response type
header("Content-type:application/json");

// Imports
use database\Db;

use function helpers\get_body;
use function helpers\validate_number;

$body = get_body();

// If deck not specified 
if (!isset($body["deck_id"])) {
    http_response_code(400);
    return;
}

// If score not specified 
if (!validate_number($body, "score", max: 12, min: 0)) {
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
