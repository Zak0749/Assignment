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

// Establish Db connection
$db = new Db();

$body = filter_input_array(INPUT_POST, [
    "title" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^.{3,32}$/"
        ]
    ],
    "description" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => [
            'regexp' => "/^.{3,256}$/"
        ]
    ],
    "topics" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        'flags' => FILTER_FORCE_ARRAY,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ],
    "cards" => [
        'flags' => FILTER_FORCE_ARRAY,
    ]
]);

if (
    in_array(false, $body, true) ||
    $body["title"] === null ||
    $body["description"] === null ||
    $body["cards"] === null ||
    count($body["cards"]) < 8
) {
    http_response_code(400);
    return;
}

$body["cards"] = array_map(function ($card) {
    $card = filter_var_array($card, [
        "question" => [
            "filter" => FILTER_VALIDATE_REGEXP,
            "options" => [
                'regexp' => "/^.{0,32}$/"
            ]
        ],
        "answer" => [
            "filter" => FILTER_VALIDATE_REGEXP,
            "options" => [
                'regexp' => "/^.{0,256}$/"
            ]
        ],
    ]);

    if (in_array(false, $card, true) || in_array(null, $card, true)) {
        return false;
    } else {
        return $card;
    }
}, $body["cards"]);

if (
    in_array(false, $body["cards"], true)
) {
    http_response_code(400);
    return;
}


$result = $db->createDeck(
    $_SESSION["account_id"],
    $body["title"],
    $body["description"],
    $body["topics"],
    $body["cards"]
);

if ($result->isOk()) {
    // Give response of `Created`
    http_response_code(201);

    // Give the new id of the deck
    echo $result->getValue();
} else {
    // Response of `Internal Server Error`
    http_response_code(500);
}
