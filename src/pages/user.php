<?php

// Imports
use database\DB;
use function cards\deck_card;
use function cards\tag_card;
use function helpers\calculate_streak;

// If user_id is not set send user to error page
if (!isset($_GET["user_id"])) {
    require 'pages/errors/not_found.php';
    return;
}

// Establish Db connection
$db = new DB();

$user_query = $db->getUser($_GET["user_id"]);

// If error occurred while getting the user send the user to an error page
if (!$user_query->isOk()) {
    require 'pages/errors/internal_server_error.php';
    return;
}

// If user doesn't exist send the user to an error page
if ($user_query->isEmpty()) {
    require 'pages/errors/not_found.php';
    return;
}

$user = $user_query->single();
?>
<header>
    <div class="beside">
        <img class="large-avatar" src="https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= $user["avatar"] ?>">

        <div>
            <h1>
                <?= htmlspecialchars($user["username"]) ?>
            </h1>

            <h2 class="subtitle">Joined <?= date("s/m/y", strtotime($user["timestamp"])) ?></h2>
        </div>
    </div>
</header>

<main>
    <section>
        <ul class="statistic-grid-large">
            <figure class="statistic">
                <span class="material-symbols-outlined">
                    add
                </span>
                <span>
                    <h3><?= $user["decks"] ?></h3>
                    <figcaption>Deck's Made</figcaption>
                </span>
            </figure>
            <figure class="statistic">
                <span class="material-symbols-outlined">
                    local_fire_department
                </span>
                <span>
                    <h3><?= calculate_streak($user) ?></h3>
                    <figcaption>Day Streak</figcaption>
                </span>
            </figure>
            <figure class="statistic">
                <span class="material-symbols-outlined">
                    playing_cards
                </span>
                <span>
                    <h3><?= $user["total_plays"] ?></h3>
                    <figcaption>Total Plays</figcaption>
                </span>
            </figure>
            <figure class="statistic">
                <span class="material-symbols-outlined">
                    target
                </span>
                <span>
                    <h3><?= $user["average_score"] ?>/12</h3>
                    <figcaption>Avg Score</figcaption>
                </span>
            </figure>
        </ul>
    </section>

    <section>
        <h2>Likes</h2>
        <ul class="tag-list">
            <?php
            $likes = $db->getLikes($_GET["user_id"]);
            if (@$likes->isOk()) : ?>
                <p>There was an error loading the users tags please try again</p>
            <?php elseif ($likes->isEmpty()) : ?>
                <p>This user currently has no likes, check back later</p>
            <?php else : ?>
                <?php foreach ($likes->iterate() as $tag) {
                    echo tag_card($tag);
                } ?>
            <?php endif; ?>
        </ul>
    </section>

    <section>
        <h2>Creations</h2>
        <ul class="deck-grid">
            <?php
            $creations = $db->getCreations($_GET["user_id"]);
            if (!$creations->isOk()) : ?>
                <p>There was an error loading the users creations please try again</p>
            <?php elseif ($creations->isEmpty()) : ?>
                <p>This user currently has no creations, check back later </p>
            <?php else : ?>
                <?php foreach ($creations->iterate() as $deck) {
                    echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                } ?>
            <?php endif; ?>
        </ul>
    </section>
</main>