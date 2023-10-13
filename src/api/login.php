<?php

// Import database class
use database\DB;

// Establish Db connection
$db = new DB();

// If logged in give error code and stop request
if (isset($_SESSION["user_id"])) {
	http_response_code(403);
	return;
}

// Find user from username
$user_query = $db->getLogin($body["username"]);

// If the user doesn't exist 
if ($user_query->isEmpty()) {
	// Gives error message
	echo json_encode(
		[
			"input-name" => "username",
			"message" => "The user entered does not exist",
		]
	);

	// Response of `Bad Request`
	http_response_code(400);

	// Ends the request
	return;
}

$user = $user_query->single();

// If given password is incorrect
if (!password_verify($body["password"], $user["password"])) {
	// Gives error message
	echo json_encode(
		[
			"input-name" => "password",
			"message" => "The password entered is incorrect"
		]
	);

	// Response of `Unauthorised`
	http_response_code(401);
} else {
	// Stores the user_id of the logged in user
	$_SESSION["user_id"] = $user["user_id"];

	// Response of `No Content`
	http_response_code(204);
}
