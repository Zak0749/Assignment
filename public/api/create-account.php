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
	"likes" => [
		"filter" => FILTER_VALIDATE_INT,
		'flags' => FILTER_FORCE_ARRAY
	],
]);

if (
	in_array(false, $body, true) || // If any fields are invalid
	($body["likes"] !== null && in_array(false, $body["likes"], true)) || // If likes exists and any of them are invalid
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
	$body["likes"]
);

// If the insert was successful
if ($result->isOk()) {
	// Store id of user
	$_SESSION["account_id"] = $result->value;

	// Response of `Created`
	http_response_code(201);

	// If username is not unique
} else if ($result->error == 23505) {
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
