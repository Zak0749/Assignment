<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function cards\deck_card;
use function cards\tag_card;

$user_id = filter_input(INPUT_GET, "user_id", FILTER_VALIDATE_INT);

// If deck_id is invalid or not set send user to error page
if ($user_id === null || $user_id === false) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// Establish Db connection
$db = new DB();

$user_query = $db->getUser($user_id);

// If error occurred while getting the user send the user to an error page
if (!$user_query->isOk()) {
    http_response_code(500);
    require("errors/500.php");
    exit;
}

// If user doesn't exist send the user to an error page
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
        <header>
            <div class="beside">
                <img class="large-avatar" src="https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=<?= htmlspecialchars($user["avatar"]) ?>">

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
                    $likes = $db->getLikes($user_id);
                    if (!$likes->isOk()) : ?>
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
                    $creations = $db->getCreations($user_id);
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
    </div>
</body>