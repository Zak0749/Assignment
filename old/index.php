<?php

// Gets the URL from the request and filters out the location and parameters
$path = parse_url($_SERVER['REQUEST_URI'])["path"];

// Sets where all my includes and requires will be based from
set_include_path("../src");

// Begins the session for authenticating the user
session_start();


// Adds all the functions/classes to be available to all routes 
require "modules/database.php";
require "modules/helpers.php";
require "components/cards.php";

// Determines if the request is to get a page or preform an action and routes accordingly
if (str_contains($path, "api")) {
    include "api.php";
} else {
    include "page.php";
}
