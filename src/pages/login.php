<?php

// If user is already logged in send them to an error page
if (isset($_SESSION["user_id"])) {
    require "pages/errors/forbidden.php";
    return;
}
?>

<header>
    <h1>Login</h1>
</header>

<main class="center-main">
    <form class="form" onsubmit="submit_login(this); return false;">
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
            <a href="/create_account">
                Create an account
            </a>
        </p>
    </form>
</main>