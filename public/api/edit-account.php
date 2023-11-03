<?php
// Imports
use database\Db;

// Sets the response type
// header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
	http_response_code(401);
	return;
}

$body = filter_input_array(INPUT_POST, [
	"username" => [
		"filter" => FILTER_VALIDATE_REGEXP,
		"options" => [
			'regexp' => "/^[\w]{3,16}$/"
		]
	],
	"password" => [
		"filter" => FILTER_VALIDATE_REGEXP,
		"options" => [
			'regexp' => "/^[\S]{8,24}$/"
		]
	],
	"avatar" => [
		"filter" => FILTER_VALIDATE_REGEXP,
		'options' => [
			'regexp' => "/^[0-9, a-f]{8}$/"
		],
	],
	"added_likes" => [
		"filter" => FILTER_VALIDATE_INT,
		'flags' => FILTER_FORCE_ARRAY
	],
	"removed_likes" => [
		"filter" => FILTER_VALIDATE_INT,
		'flags' => FILTER_FORCE_ARRAY
	]
]);

// if any invalid
if (
	in_array(false, $body, true) ||
	($body["added_likes"] !== null && in_array(false, $body["added_likes"], true)) ||
	($body["added_likes"] !== null && in_array(false, $body["added_likes"], true))
) {
	http_response_code(400);
	return;
}
// Establishes a connection 
$db = new Db();

// If a new password is given hash it 
if ($body["password"] !== null) {
	$body["password"] = password_hash($body["password"], PASSWORD_DEFAULT);
}

$result = $db->updateAccount(
	$body["username"],
	$body["password"],
	$body["avatar"],
	$body["added_likes"],
	$body["removed_likes"]
);

// If the edit was successful
if ($result->isOk()) {
	// Response of `No Content`
	http_response_code(204);
} else if ($result->error == 2067) {
	// Response of `Bad Request`
	http_response_code(400);

	// Gives error message
	echo json_encode(
		[
			"input" => "username",
			"message" => "The username is taken",
		]
	);
} else {
	// Response of Internal Server Error`
	http_response_code(500);
}
