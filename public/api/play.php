<?php
// Sets the response type
header("Content-type:application/json");

// Imports
use database\Db;

$body = filter_input_array(INPUT_POST, [
    "deck_id" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ],
    "score" => [
        "filter" => FILTER_VALIDATE_INT,
        "options" => [
            "min_range" => 0,
            "max_range" => 12
        ]
    ]
]);

// If deck not specified 
if (in_array(null, $body, true) || in_array(false, $body, true)) {
    http_response_code(400);
    return;
}

// Establish database connection
$db = new Db();

$result = $db->savePlay(
    $body["deck_id"],
    $body["score"],
    $_SESSION["account_id"] ?? null
);

if ($result->isOk()) {
    http_response_code(204);
} else {
    http_response_code(500);
}
