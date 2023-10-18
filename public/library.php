<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function cards\deck_card;

// If the user is not logged in send them to an error page
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    require("errors/401.php");
    exit;
}

// Establish Db connection
$db = new DB();
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
            <h1>
                Library
            </h1>
        </header>

        <main>
            <section>
                <h2>Saved</h2>

                <?php
                $saved = $db->getSaved();
                if (!$saved->isOk()) :
                ?>
                    <p>An error occurred, please try again</p>
                <?php elseif ($saved->isEmpty()) : ?>
                    <p>You have no saved decks when you save some they will appear here</p>
                <?php else : ?>
                    <ul class="deck-grid">
                        <?php foreach ($saved->iterate() as $deck) {
                            echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                        } ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section>
                <h2>Creations</h2>

                <?php
                $creations = $db->getCreations($_SESSION["user_id"]);
                if (!$creations->isOk()) :
                ?>
                    <p>An error occurred, please try again</p>
                <?php elseif ($creations->isEmpty()) : ?>
                    <p>You have not created any decks yet, when you do they will appear here</p>
                <?php else : ?>
                    <ul class="deck-grid">
                        <?php foreach ($creations->iterate() as $deck) {
                            echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                        } ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section>
                <h2>Recent</h2>

                <?php
                $recent = $db->getRecent($_SESSION["user_id"]);
                if (!$recent->isOk()) :
                ?>
                    <p>An error occurred, please try again</p>
                <?php elseif ($recent->isEmpty()) : ?>
                    <p>You have not played any decks yet, when you do they will appear here</p>
                <?php else : ?>
                    <ul class="deck-grid">
                        <?php foreach ($recent->iterate() as $deck) {
                            echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                        } ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>