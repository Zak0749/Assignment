<?php

// If not logged in give error code and stop request
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    return;
}

// Removes the user_id on the session
unset($_SESSION["user_id"]);

// Response of `No Content`
http_response_code(204);
