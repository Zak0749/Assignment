<?php
// Begins the session for authenticating the user


// If user is already logged in send them to an error page
if (isset($_SESSION["account_id"])) {
    http_response_code(403);
    require("errors/403.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <main class="center-main">
        <header >
            <h1>Account</h1>
        </header>

        <p >You are not logged in, if you have an account?</p>

        <a  href="login" class=" primary-button button" keyboard-shortcut="l">
            <span class="material-symbols-outlined">login</span> Login
        </a>

        <p >Otherwise</p>

        <a href="create-account" class=" primary-button button" keyboard-shortcut="c">
            <span class="material-symbols-outlined">person_add</span> Create Account
        </a>
</body>