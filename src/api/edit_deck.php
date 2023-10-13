<?php

// If not logged in give error code and stop request

use database\Db;

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// If deck is not specified give error code and stop request
if (!isset($body["deck_id"])) {
    http_response_code(400);
    return;
}

$db = new Db();

$deck = $db->getDeck($body["deck_id"]);

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
if ($_SESSION["user_id"] != $deck->single()["user_id"]) {
    http_response_code(403);
    return;
}

$result = $db->updateDeck(
    $body["deck_id"],
    $body["title"],
    $body["description"],
    $body["added_topics"],
    $body["removed_topics"],
    $body["new_questions"],
    $body["edited_questions"],
    $body["removed_questions"]
);

if ($result->isOk()) {
    http_response_code(204);
} else {
    http_response_code(500);
}
