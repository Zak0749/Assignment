<?php
// Imports
use database\Db;

use function helpers\get_body;
use function helpers\validate_array_key;
use function helpers\validate_string;

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// Establish Db connection
$db = new Db();

$body = get_body();

if (
    !validate_string($body, "title", required: true, pattern: "/^.{3,32}$/") ||
    !validate_string($body, "description", required: true, pattern: "/^.{3,256}$/") ||
    
) {
    http_response_code(400);
    return;
}


$result = $db->createDeck(
    $body["title"],
    