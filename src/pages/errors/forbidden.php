<?php
// Send a response code meaning `Forbidden`
http_response_code(403);
?>

<header>
    <h1>Forbidden</h1>
</header>
<main class="center-main">
    <section>
        <h2>401</h2>
        <p>Your account is not able to preform this action, sign out <a href="/my_account">here</a> ad sign in to the correct account and try again</p>
    </section>
</main>