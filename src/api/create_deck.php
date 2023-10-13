<?php

// Imports
use database\Db;

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// Establish Db connection
$db = new Db();

$result = $db->createDeck(
    $body["title"],
    $body["description"],
    $body["topics"],
    $body["questions"]
);

if ($result->isOk()) {
    // Give the new id of the deck
    echo $result->value;

    // Give response of `Created`
    http_response_code(201);
} else {
    // Response of `Internal Server Error`
    http_response_code(500);
}
