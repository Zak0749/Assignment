<?php
// Begins the session for authenticating the user


// If user is already logged in send them to an error page
if (isset($_SESSION["user_id"])) {
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

    <div class="page">
        <header>
            <h1>Account</h1>
        </header>

        <main class="center-main">
            <section>
                <h3>Have an account?</h3>

                <a href="login" class="primary-button">
                    <span class="material-symbols-outlined">login</span> Login
                </a>
            </section>

            <section>
                <h3>Otherwise</h3>

                <a href="create-account" class="primary-button">
                    <span class="material-symbols-outlined">person_add</span> Create Account
                </a>
            </section>
        </main>
    </div>
</body>