<?php

// If the user is not logged in send them to an error page
if (!isset($_SESSION["user_id"])) {
    include "pages/errors/unauthorized.php";
    return;
}

use database\DB;
use function cards\deck_card;
use function cards\tag_card;
use function helpers\calculate_streak;

// Establish Db connection
$db = new DB();

$user_query = $db->getUser($_SESSION["user_id"]);

if (!$user_query->isOk()) {
    include 'components/500.php';
    http_response_code(500);
    return;
}

if ($user_query->isEmpty()) {
    include 'components/404.php';
    http_response_code(404);
    return;
}

$user = $user_query->single();
?>

<header class="spaced-apart">
    <div class="beside">
        <img class="large-avatar" src="https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= $user["avatar"] ?>">

        <div>
            <h1>
                <?= htmlspecialchars($user["username"]) ?>
            </h1>

            <h2 class="subtitle">Joined <?= date("s/m/y", strtotime($user["timestamp"])) ?></h2>
        </div>
    </div>

    <ul class="icon-bar">
        <li>
            <a href="edit_account" class="header-icon">
                <span class=" material-symbols-outlined">
                    edit
                </span>
            </a>
        </li>
        <li>
            <button type="button" onclick="logout()" class="header-icon">
                <span class=" material-symbols-outlined">
                    logout
                </span>
            </button>
        </li>
    </ul>
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
            $likes = $db->getLikes($_SESSION["user_id"]);
            if (!$likes->isOk()) : ?>
                <p>There was an error loading the users tags please try again</p>
            <?php elseif ($likes->isEmpty()) : ?>
                <p>You currently have no likes selected, edit your account to add them</p>
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
            $creations = $db->getCreations($_SESSION["user_id"]);
            if (!$creations->isOk()) : ?>
                <p>There was an error loading the users creations please try again</p>
            <?php elseif ($creations->isEmpty()) : ?>
                <p>You currently have no creations, create a deck and you will see them here </p>
            <?php else : ?>
                <?php foreach ($creations->iterate() as $deck) {
                    echo deck_card($deck, $db->getTopics($deck["deck_id"]));
                } ?>
            <?php endif; ?>
        </ul>
    </section>
</main>