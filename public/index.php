<?php
// Imports
use database\DB;
use function cards\deck_card;

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
            <div class="spaced-apart">
                <h1>
                    Discover
                </h1>

                <?php
                if (isset($_SESSION["account_id"])) :
                    $streak = $db->getStreak($_SESSION["account_id"], $_SESSION["account_id"]);
                    if ($user_query->isOk()) : ?>
                        <span class="streak-display">
                            <span class="material-symbols-outlined">
                                local_fire_department
                            </span>

                            <h1>
                                <?= htmlspecialchars($streak->value) ?>
                            </h1>
                        </span>
                <?php
                    endif;
                endif;
                ?>
            </div>
        </header>

        <main>
            <section>
                <h2>Popular</h2>

                <?php
                $popular_query = $db->getPopular($_SESSION["account_id"] ?? null);
                if (!$popular_query->isOk()) :
                ?>
                    <p>An error occurred, please try again</p>
                <?php elseif ($popular_query->isEmpty()) : ?>
                    <p>There is currently no popular decks check back later and there might be</p>
                <?php else : ?>
                    <ul class="deck-grid">
                        <?php foreach ($popular_query->array() as $deck) { // Display the card for each popular deck
                            echo deck_card($deck, $db->getDeckTopics($deck["deck_id"]));
                        } ?>
                    </ul>
                <?php endif; ?>
            </section>

            <?php
            // Show the user if logged in
            if (isset($_SESSION["account_id"])) :
            ?>
                <section>
                    <h2>For You</h2>

                    <?php
                    $for_you_query = $db->getForYou($_SESSION["account_id"]);
                    if (!$for_you_query->isOk()) :
                    ?>
                        <p>An error occurred, please try again</p>
                    <?php elseif ($for_you_query->isEmpty()) : ?>
                        <p>There is currently no recommended decks, check back later and there might be</p>
                    <?php else : ?>
                        <ul class="deck-grid">
                            <?php // Display the card for each recommended deck
                            foreach ($for_you_query->array() as $deck) {
                                echo deck_card($deck, $db->getDeckTopics($deck["deck_id"]));
                            } ?>
                        </ul>
                    <?php endif; ?>
                </section>
            <?php endif ?>

            <section>
                <h2>New</h2>

                <?php
                $new_query = $db->getNew($_SESSION["account_id"] ?? null);
                if (!$new_query->isOk()) :
                ?>
                    <p>An error occurred, please try again</p>
                <?php elseif ($new_query->isEmpty()) : ?>
                    <p>There is currently no nwe decks check back later and there might be</p>
                <?php else : ?>
                    <ul class="deck-grid">
                        <?php // Display the card for each new deck
                        foreach ($new_query->array() as $deck) {
                            echo deck_card($deck, $db->getDeckTopics($deck["deck_id"]));
                        } ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>