<?php
// Imports
use database\DB;

use function helpers\get_body;
use function helpers\validate_string;
use function helpers\validate_text;

// Sets the response type
header("Content-type:application/json");

// If logged in give error code and stop request
if (isset($_SESSION["user_id"])) {
	http_response_code(403);
	return;
}

$body = get_body();

if (
	!validate_string($body, "username", required: true, pattern: "/^[\w]{3,16}$/") &&
	!validate_string($body, "password", required: true, pattern: "/^[\S]{8,24}$/") &&
	!validate_string($body, "avatar", required: true, pattern: "/^[0-9, a-f]{8}$/")
) {
	http_response_code(400);
	return;
}


// Establish Db connection
$db = new DB();


$result = $db->createAccount(
	$body["username"],
	// Hash password for security 
	password_hash($body["username"], PASSWORD_DEFAULT),
	$body["avatar"],
	$body["likes"]
);

// If the insert was successful
if ($result->isOk()) {
	// Store id of user
	$_SESSION["user_id"] = $result->value;

	// Response of `Created`
	http_response_code(201);

	// If username is not unique
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
	// Response of `Internal Server Error`
	http_response_code(500);
}
