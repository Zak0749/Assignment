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

// If the deck id is invalid or not set
if ($deck_id === false || $deck_id === null) {
    http_response_code(400);
    return;
}

// Establish Db connection
$db = new Db();

// Removes the deck from the saved list
$result = $db->deleteSave(
    $deck_id,
    $_SESSION["account_id"]
);

// If removal was successful
if ($result->isOk()) {
    // Response of `No Content`
    http_response_code(204);
} else {
    // Response of `Internal Server Error`
    http_response_code(500);
}
