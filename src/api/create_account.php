<?php
// Imports
use database\DB;

// If logged in give error code and stop request
if (isset($_SESSION["user_id"])) {
	http_response_code(403);
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
	// Gives error message
	echo json_encode(
		[
			"input-name" => "username",
			"message" => "The username is taken",
		]
	);

	// Response of `Bad Request`
	http_response_code(400);
} else {
	// Response of `Internal Server Error`
	http_response_code(500);
}
