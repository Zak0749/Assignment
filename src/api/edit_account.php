<?php

// Imports
use database\Db;

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
	http_response_code(401);
	return;
}

// Establishes a connection 
$db = new Db();

$result = $db->updateAccount(
	$body["username"],
	// If the password is set hash it otherwise leave it as null
	isset($body["password"]) ?
		password_hash($body["password"], PASSWORD_DEFAULT) : NULL,
	$body["avatar"],
	$body["added_likes"],
	$body["removed_likes"]
);

// If the edit was successful
if ($result->isOk()) {
	// Response of `No Content`
	http_response_code(204);
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
	// Response of Internal Server Error`
	http_response_code(500);
}
