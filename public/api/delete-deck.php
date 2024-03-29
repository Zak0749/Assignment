<?php

use database\Db;

// Sets the response type
header("Content-type:application/json");

// If not logged in
if (!isset($_SESSION["account_id"])) {
    http_response_code(401);
    return;
}

// Get the deck id from the request
$deck_id = filter_input(
    INPUT_POST,
    "deck_id",
    FILTER_VALIDATE_REGEXP,
    [
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ]
);

// If deck id not specified or not a number
if ($deck_id == false || $deck_id == null) {
    http_response_code(400);
    return;
}

// Establish database connection
$db = new Db();

// Get the specified deck from the database
$deck = $db->getDeck(
    $deck_id,
    $_SESSION["account_id"]
);

// If getting deck had an error give error code then stop request
if (!$deck->isOk()) {
    http_response_code(500);
    return;
}

//If deck doesn't exist give error code and stop request
if ($deck->isEmpty()) {
    http_response_code(404);
    return;
}

// If not owner in give error code and stop request
if ($user_account_id["is_owned"]) {
    http_response_code(403);
    return;
}

$result = $db->deleteDeck($deck_id);

if ($result->isOk()) {
    http_response_code(204);
} else {
    http_response_code(500);
}
