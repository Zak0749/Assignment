<?php
// Import database class
use database\DB;

header('Content-Type: application/json');

// If logged in give error code and stop request
if (isset($_SESSION["account_id"])) {
	http_response_code(403);
	return;
}

// Get the body of the request and validate it
$body = filter_input_array(INPUT_POST, [
	"username" => [
		"filter" => FILTER_VALIDATE_REGEXP,
		"options" => [
			'regexp' => "/^[\w]{3,16}$/"
		]
	],
	"password" =>
	[
		"filter" => FILTER_VALIDATE_REGEXP,
		'options' => [
			'regexp' => "/^[\S]{8,24}$/"
		]
	],
]);

// If any of the inputs are invalid or not set send a `bad request` code
if (in_array(false, $body, true) || in_array(null, $body, true)) {
	http_response_code(400);
	return;
}

// Establish Db connection
$db = new DB();

// Find user from username
$user_query = $db->getLogin($body["username"]);

// If the user doesn't exist 
if ($user_query->isEmpty()) {
	// Response of `Bad Request`
	http_response_code(400);

	// Gives error message
	echo json_encode(
		[
			"input" => "username",
			"message" => "The user entered does not exist",
		]
	);

	// Ends the request
	return;
}

$user = $user_query->single();

// If given password is incorrect
if (!password_verify($body["password"], $user["password"])) {
	// Response of `Unauthorised`
	http_response_code(401);

	// Gives error message
	echo json_encode(
		[
			"input" => "password",
			"message" => "The password entered is incorrect"
		]
	);
} else {
	// Stores the account_id of the logged in user
	$_SESSION["account_id"] = $user["account_id"];

	// Response of `No Content`
	http_response_code(204);
}
