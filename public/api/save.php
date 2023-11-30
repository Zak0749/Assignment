<?php
// Imports
use database\Db;

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["account_id"])) {
    http_response_code(401);
    return;
}

// Gets the search string
// If not set it will be null
// If not a number it will be false
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

// Establish Db connection
$db = new Db();

// Saves the deck_id
$result = $db->save($deck_id, $_SESSION["account_id"]);

// If save was successful
if ($result->isOk()) {
    // Response of `No Content`
    http_response_code(204);
} else {
    // Response of `Internal Server Error§`
    http_response_code(500);
}
