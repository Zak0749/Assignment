<?php
// Imports
use database\DB;

// Sets the response type
header("Content-type:application/json");

// If logged in give error code and stop request
if (isset($_SESSION["account_id"])) {
	http_response_code(403);
	return;
}

// Gets the input body and filters it
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
	"follows" => [
		"filter" => FILTER_VALIDATE_REGEXP,
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
		],
		'flags' => FILTER_FORCE_ARRAY
	],
]);

if (
	in_array(false, $body, true) || // If any fields are invalid
	($body["follows"] !== null && in_array(false, $body["follows"], true)) || // If follows exists and any of them are invalid
	$body["username"] === null || // If username is not set
	$body["password"] === null || // If password is not set
	$body["avatar"] === null // If avatar is not set
) {
	http_response_code(400);
	return;
}


// Establish Db connection
$db = new DB();

$result = $db->createAccount(
	$body["username"],
	password_hash($body["password"], PASSWORD_DEFAULT), // Hash password so even if passwords are gotten out of the database attackers cannot get plain text password
	$body["avatar"],
	$body["follows"]
);

// If the insert was successful
if ($result->isOk()) {
	// Store id of user
	$_SESSION["account_id"] = $result->getValue();

	// Response of `Created`
	http_response_code(201);

	// If username is not unique
} else if ($result->errorCode() == 23505) {
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
	// Response of `Internal Server Error`
	http_response_code(500);
}
