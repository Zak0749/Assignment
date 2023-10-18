<?php
// Import database class
use database\DB;

use function helpers\get_body;
use function helpers\validate_string;

header('Content-Type: application/json');

// If logged in give error code and stop request
if (isset($_SESSION["user_id"])) {
	http_response_code(403);
	return;
}

$body = get_body();

// If username or password is invalid
// No need to send error message as same as client validation 
if (
	!validate_string($body, "username", required: true, pattern: "/^[\w]{3,16}$/") ||
	!validate_string($body, "password", required: true, pattern: "/^[\S]{8,24}$/")
) {
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
	// Stores the user_id of the logged in user
	$_SESSION["user_id"] = $user["user_id"];

	// Response of `No Content`
	http_response_code(204);
}
