<?php
// Imports
use database\Db;

use function helpers\get_body;
use function helpers\validate_string;

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
	http_response_code(401);
	return;
}

$body = get_body();

if (
	!validate_string($body, "username", required: false, pattern: "/^[\w]{3,16}$/") &&
	!validate_string($body, "password", required: false, pattern: "/^[\S]{8,24}$/") &&
	!validate_string($body, "avatar", required: false, pattern: "/^[0-9, a-f]{8}$/")
) {
	http_response_code(400);
	return;
}

// Establishes a connection 
$db = new Db();

$result = $db->updateAccount(
	$_REQUEST["username"],
	// If the password is set hash it otherwise leave it as null
	isset($_REQUEST["password"]) ?
		password_hash($_REQUEST["password"], PASSWORD_DEFAULT) : NULL,
	$_REQUEST["avatar"],
	$_REQUEST["added_likes"],
	$_REQUEST["removed_likes"]
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
