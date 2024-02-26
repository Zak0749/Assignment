<?php
// If not logged in give error code and stop request

use database\Db;

// Sets the response type
header("Content-type:application/json");

if (!isset($_SESSION["account_id"])) {
    http_response_code(401);
    return;
}

$body = filter_input_array(INPUT_POST, [
    "deck_id" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ],
    "title" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => [
            'regexp' => "/^.{3,32}$/"
        ]
    ],
    "description" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "options" => [
            'regexp' => "/^.{3,256}$/"
        ]
    ],
    "added_topics" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "flags" => FILTER_FORCE_ARRAY,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ],
    "removed_topics" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "flags" => FILTER_FORCE_ARRAY,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ],
    "new_cards" => [
        "flags" => FILTER_FORCE_ARRAY
    ],
    "edited_cards" => [
        "flags" => FILTER_FORCE_ARRAY,
    ],
    "removed_cards" => [
        "filter" => FILTER_VALIDATE_REGEXP,
        "flags" => FILTER_FORCE_ARRAY,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ]
]);

// If not null or valid
if ($body["new_cards"]) {
    $body["new_cards"] = array_map(function ($card) {
        $card = filter_var_array($card, [
            "question" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => [
                    'regexp' => "/^.{0,256}$/"
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
    }, $body["new_cards"]);
}

if ($body["edited_cards"]) {
    $body["edited_cards"] = array_map(function ($card) {
        $card = filter_var_array($card, [
            "card_id" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => [
                    'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
                ]
            ],
            "question" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => [
                    'regexp' => "/^.{0,256}$/"
                ]
            ],
            "answer" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => [
                    'regexp' => "/^.{0,256}$/"
                ]
            ],
        ]);

        // If any fields are invalid or the id is not set
        if (in_array(false, $card, true) || $card["card_id"] === null) {
            return false;
        } else {
            return $card;
        }
    }, $body["edited_cards"]);
}

// If deck is not specified give error code and stop request
if (
    in_array(false, $body, true) || // Ensure all answers are not false as false means invalid
    $body["deck_id"] === null || // Ensure the id of the deck is set
    ($body["added_topics"] !== null && in_array(false, $body["added_topics"], true)) || // Ensure added each topic is either null or is valid
    ($body["removed_topics"] !== null && in_array(false, $body["removed_topics"], true)) || //  Ensure added each removed topic is either null or is valid
    ($body["edited_cards"] !== null && in_array(false, $body["edited_cards"], true)) || // Ensure each new question is either null or is valid
    ($body["edited_cards"] !== null && in_array(false, $body["edited_cards"], true)) || // Ensure each edited question is either null or is valid
    ($body["removed_cards"] !== null && in_array(false, $body["removed_cards"], true)) // Ensure each removed question is either null or is valid
) {
    http_response_code(400);
    var_dump($body);
    return;
}

$db = new Db();

$deck_query = $db->getDeck($body["deck_id"], $_SESSION["account_id"]);

// If getting deck had an error give error code then stop request
if (!$deck_query->isOk()) {
    http_response_code(500);
    return;
}

//If deck doesn't exist give error code and stop request
if ($deck_query->isEmpty()) {
    http_response_code(404);
    return;
}

$d = $deck_query->single();

// If not owner in give error code and stop request
if (!$d["is_owned"]) {
    var_dump($d);
    http_response_code(403);
    return;
}

$result = $db->updateDeck(
    $body["deck_id"],
    $body["title"],
    $body["description"],
    $body["added_topics"],
    $body["removed_topics"],
    $body["new_cards"],
    $body["edited_cards"],
    $body["removed_cards"]
);

if ($result->isOk()) {
    http_response_code(204);
} else {
    http_response_code(500);
}
