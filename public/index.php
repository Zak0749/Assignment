<?php

// Get the url
$request = $_SERVER['REQUEST_URI'];

// Take out the parameters
$url = parse_url($request)["path"];

// Get function components
include "../src/components/cards.php";

// Make the DB class availible to everyone
require "../src/modules/database.php";

// Start session for auth
session_start();

// ADD CHECKING FOR NON AUTH ROUTES
if (!str_contains($url, "api")) {
    echo '<!DOCTYPE html><html lang="en">';

    include "../src/components/head.html";

    echo '<body>';
}


switch ($url) {
    case '/api/search_results':
        require '../src/api/search_results.php';
        break;

    case '/api/signin':
        require '../src/api/signin.php';
        break;

    case '/api/signout':
        require '../src/api/signout.php';
        break;

    case '':
    case '/':
        require '../src/pages/discover.php';
        break;

    case '/library':
        require '../src/pages/library.php';
        break;

    case '/search':
        require '../src/pages/search.php';
        break;

    case '/account':
        if ($_SESSION["user_id"] ?? null ?? false) {
            require '../src/pages/account.php';
        } else {
            require '../src/pages/not_logged_in.php';
        }
        break;

    case '/signin':
        if ($_SESSION["user_id"] ?? null) {
            require '../src/pages/account.php';
        } else {
            require '../src/pages/signin.php';
        }
        break;

    case '/tag':
        if ($_GET["tag_id"] ?? null) {
            require '../src/pages/tag.php';
        } else {
            require '../src/pages/not_found.php';
            http_response_code(404);
        }
        break;

    case '/deck':
        if ($_GET["deck_id"] ?? null) {
            require '../src/pages/deck.php';
        } else {
            require '../src/pages/not_found.php';
            http_response_code(404);
        }
        break;

    case '/user':
        if ($_GET["user_id"] ?? null) {
            if ($_GET["user_id"] == $_SESSION["user_id"]) {
                require '../src/pages/account.php';
            } else {
                require '../src/pages/user.php';
            }
        } else {
            require '../src/pages/not_found.php';
            http_response_code(404);
        }
        break;

    default:
        http_response_code(404);
        require '../src/pages/not_found.php';
        break;
}


if (!str_contains($url, "api")) {
    echo '</body></html>';
}
