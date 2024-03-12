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

    <!-- Styles for form elements -->
    <link href="styles/forms.css" rel="stylesheet">
</head>

<body>
    <?php require "components/navbar.php" ?>

    <main class="center-main">
        <header >
            <h1>Login</h1>
        </header>
        <form class="form" onsubmit="loginUser(this); return false;">
            <div class="form-field" id="username">
                <label for="username">Username</label>
                <input name="username" type="text" required minlength="3" maxlength="16" pattern="[\w]+" title="Letters, Numbers and underscores only" oninput="this.setCustomValidity('')" />
            </div>

            <div class="form-field" id="password">
                <label for="password">Password</label>
                <input name="password" type="password" required minlength="8" maxlength="24" pattern="[\S]+" title="Whitespace characters are not allowed" oninput="this.setCustomValidity('')" />
            </div>

            <button type="submit" class="primary-button button" id="submit">
                <span class="material-symbols-outlined">
                    check
                </span>
                Login
            </button>

            <p>
                Don't have an account?
                <a href="create-account" keyboard-shortcut="c">
                    Create an account
                </a>
            </p>
        </form>
    </main>
</body>