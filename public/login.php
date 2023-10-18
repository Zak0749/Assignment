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
            <h1>Login</h1>
        </header>

        <main class="center-main">
            <form class="form" onsubmit="loginUser(this); return false;">
                <div class="form-field" id="username">
                    <label for="username">Username</label>
                    <input name="username" type="username" required minlength="3" maxlength="16" pattern="[\w]+" oninput="this.setCustomValidity('')" />
                </div>

                <div class="form-field" id="password">
                    <label for="password">Password</label>
                    <input name="password" type="password" required minlength="8" maxlength="24" pattern="[\S]+" oninput="this.setCustomValidity('')" />
                </div>

                <button type="submit" class="primary-button" id="submit">
                    <span class="material-symbols-outlined">
                        check
                    </span>
                    Login
                </button>

                <p>
                    Don't have an account?
                    <a href="create-account">
                        Create an account
                    </a>
                </p>

                <p id="general-error"></p>
            </form>
        </main>
    </div>
</body>