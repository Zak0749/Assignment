<?php
// If not logged in give error code and stop request

use database\Db;

use function helpers\get_body;

// Sets the response type
header("Content-type:application/json");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// If deck is not specified give error code and stop request
if (!isset($_REQUEST["deck_id"])) {
    http_response_code(400);
    return;
}

$body = get_body();

$db = new Db();

$deck = $db->getDeck($_REQUEST["deck_id"]);

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
    $_REQUEST["deck_id"],
    $_REQUEST["title"],
    $_REQUEST["description"],
    $_REQUEST["added_topics"],
    $_REQUEST["removed_topics"],
    $_REQUEST["new_questions"],
    $_REQUEST["edited_questions"],
    $_REQUEST["removed_questions"]
);

if ($result->isOk()) {
    http_response_code(204);
} else {
    http_response_code(500);
}
