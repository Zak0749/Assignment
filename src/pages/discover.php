<?php

// Imports
use database\DB;
use function cards\deck_card;
use function helpers\calculate_streak;

// Establish Db connection
$db = new DB();
?>

<header>
    <div class="spaced-apart">
        <h1>
            Discover
        </h1>

        <?php
        if (isset($_SESSION["user_id"])) :
            $user_query = $db->getUser($_SESSION["user_id"]);
            if ($user_query->isOk() && !$user_query->isEmpty()) : ?>
                <span class="streak-display">
                    <span class="material-symbols-outlined">
                        local_fire_department
                    </span>

                    <h1>
                        <?= calculate_streak($user_query->single()) ?>
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
        <h2>Featured</h2>

        <?php
        $featured_query = $db->getFeatured();
        if (!$featured_query->isOk()) :
        ?>
            <p>An error occurred, please try again</p>
        <?php elseif ($featured_query->isEmpty()) : ?>
            <p>There is currently no popular decks check back later and there might be</p>
        <?php else : ?>
            <ul class="deck-grid">
                <?php // Display the card for each featured deck
                foreach ($featured_query->iterate() as $deck) {
                    echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                } ?>
            </ul>
        <?php endif; ?>
    </section>

    <?php
    // Show the user if logged in
    if (isset($_SESSION["user_id"])) :
    ?>
        <section>
            <h2>For You</h2>

            <?php
            $for_you_query = $db->getForYou();
            if (!$for_you_query->isOk()) :
            ?>
                <p>An error occurred, please try again</p>
            <?php elseif ($for_you_query->isEmpty()) : ?>
                <p>There is currently no recommended decks, check back later and there might be</p>
            <?php else : ?>
                <ul class="deck-grid">
                    <?php // Display the card for each featured deck
                    foreach ($for_you_query->iterate() as $deck) {
                        echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                    } ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endif ?>

    <section>
        <h2>Popular</h2>

        <?php
        $popular_query = $db->getPopular();
        if (!$popular_query->isOk()) :
        ?>
            <p>An error occurred, please try again</p>
        <?php elseif ($popular_query->isEmpty()) : ?>
            <p>There is currently no popular decks check back later and there might be</p>
        <?php else : ?>
            <ul class="deck-grid">
                <?php foreach ($popular_query->iterate() as $deck) { // Display the card for each featured deck
                    echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                } ?>
            </ul>
        <?php endif; ?>
    </section>
</main>