<?php
// Begins the session for authenticating the user


// If the user is not logged in send them to an error page
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    require("errors/401.php");
    exit;
}

use database\DB;
use function cards\deck_card;
use function cards\tag_card;

// Establish Db connection
$db = new DB();

$user_query = $db->getUser($_SESSION["user_id"]);

if (!$user_query->isOk()) {
    http_response_code(500);
    require("errors/500.php");
    exit;
}

if ($user_query->isEmpty()) {
    http_response_code(404);
    require("errors/404.php");
    exit;
}

$user = $user_query->single();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require "components/head.php" ?>
</head>

<body>
    <?php require "components/navbar.php" ?>

    <div class="page">

        <header class="spaced-apart">
            <div class="beside">
                <img class="large-avatar" src="https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= htmlspecialchars($user["avatar"]) ?>">

                <div>
                    <h1>
                        <?= htmlspecialchars($user["username"]) ?>
                    </h1>

                    <h2 class="subtitle">Joined <?= date("s/m/y", strtotime($user["timestamp"])) ?></h2>
                </div>
            </div>

            <ul class="icon-bar">
                <li>
                    <a href="edit-account" class="header-icon" keyboard-shortcut="e">
                        <span class=" material-symbols-outlined">
                            edit
                        </span>
                    </a>
                </li>
                <li>
                    <button type="button" onclick="logout()" class="header-icon" keyboard-shortcut="l">
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
                            <h3><?= htmlspecialchars($user["decks"]) ?></h3>
                            <figcaption>Deck's Made</figcaption>
                        </span>
                    </figure>
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            local_fire_department
                        </span>
                        <span>
                            <h3><?= htmlspecialchars($user["streak"]) ?></h3>
                            <figcaption>Day Streak</figcaption>
                        </span>
                    </figure>
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            playing_cards
                        </span>
                        <span>
                            <h3><?= htmlspecialchars($user["total_plays"]) ?></h3>
                            <figcaption>Total Plays</figcaption>
                        </span>
                    </figure>
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            target
                        </span>
                        <span>
                            <h3><?= round($user["average_score"], 2) ?></h3>
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
    </div>
</body>