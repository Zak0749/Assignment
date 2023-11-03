<?php

// Sets the response type
header("Content-type:application/json");

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// Removes the user_id on the session
// Would have used session_destroy() but decided not to as if other data was stored on SESSION could cause unexpected behavior 
unset($_SESSION["user_id"]);

// Response of `No Content`
http_response_code(204);
