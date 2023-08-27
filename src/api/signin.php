<?php

use database\DB;
use session\Session;

// Gets the request body
$json = file_get_contents('php://input');
$body = json_decode($json, true);

// Sets the response type
header("Content-type:application/json");


// Connect to the database
$db = new DB();

// Get the user associated with the username entered
$query = $db->prepare(<<<SQL
    SELECT user_id,
        username,
        password
    FROM User
    WHERE username = :username
SQL);
$query->bindValue(":username", $body["username"]);
$result = $query->execute();
$user = $result->fetchArray();

if (!$user) {
    echo "Username entered is incorrect user not found";
    http_response_code(401);
} else {
    if ($body["password"] != $user["password"]) {
        echo "Password entered is incorrect";
        http_response_code(401);
    } else {
        $_SESSION["user_id"] = $user["user_id"];
        var_dump($_SESSION);
        http_response_code(200);
    }
}
