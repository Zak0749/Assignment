<?php
// Imports
use database\Db;

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
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
        "filter" => FILTER_VALIDATE_INT,
        'flags' => FILTER_FORCE_ARRAY
    ],
    "questions" => [
        'flags' => FILTER_FORCE_ARRAY,
    ]
]);

if (
    in_array(false, $body, true) ||
    $body["title"] === null ||
    $body["description"] === null ||
    $body["questions"] === null ||
    count($body["questions"]) < 8
) {
    http_response_code(400);
    return;
}

$body["questions"] = array_map(function ($question) {
    $question = filter_var_array($question, [
        "key" => [
            "filter" => FILTER_VALIDATE_REGEXP,
            "options" => [
                'regexp' => "/^.{0,32}$/"
            ]
        ],
        "value" => [
            "filter" => FILTER_VALIDATE_REGEXP,
            "options" => [
                'regexp' => "/^.{0,256}$/"
            ]
        ],
    ]);

    if (in_array(false, $question, true) || in_array(null, $question, true)) {
        return false;
    } else {
        return $question;
    }
}, $body["questions"]);

if (
    $title === null || $title === false ||
    $description === null || $description === false ||
    ($topics != null && in_array(false, $topics, true)) ||
    in_array(false, $questions, true)
) {
    http_response_code(400);
    return;
}


$result = $db->createDeck(
    $title,
    $description,
    $topics,
    $questions
);

if ($result->isOk()) {
    // Give response of `Created`
    http_response_code(201);

    // Give the new id of the deck
    echo $result->value;
} else {
    // Response of `Internal Server Error`
    http_response_code(500);
}
