<?php
// If user is already logged in send them to an error page
if (isset($_SESSION["user_id"])) {
    require "pages/errors/forbidden.php";
    return;
}
?>
<header>
    <h1>Account</h1>
</header>

<main class="center-main">
    <section>
        <h3>Have an account?</h3>

        <a href="/login" class="primary-button">
            <span class="material-symbols-outlined">login</span> Login
        </a>
    </section>

    <section>
        <h3>Otherwise</h3>

        <a href="/create_account" class="primary-button">
            <span class="material-symbols-outlined">person_add</span> Create Account
        </a>
    </section>
</main>