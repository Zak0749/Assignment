<?php
// Send a response code meaning `Unauthorized`
http_response_code(401);
?>

<header>
    <h1>Unauthorized</h1>
</header>
<main class="center-main">
    <section>
        <h2>401</h2>
        <p>You are not authorised to do this, login <a href="/not_logged_in">here</a></p>
    </section>
</main>