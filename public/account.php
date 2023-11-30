<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function cards\deck_card;
use function cards\tag_card;

$account_id = filter_input(
    INPUT_GET,
    "account_id",
    FILTER_VALIDATE_REGEXP,
    [
        "options" => [
            'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
        ]
    ]
);

// If deck_id is invalid or not set send user to error page
if ($account_id === null || $account_id === false) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// Establish Db connection
$db = new DB();

$user_query = $db->getUser($account_id, $_SESSION["user_id"] ?? null);

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

            <?php if ($user["is_current_user"]) : ?>
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
            <?php endif ?>
        </header>

        <main>
            <section>
                <ul class="statistic-grid-large">
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            add
                        </span>
                        <span>
                            <h3><?= htmlspecialchars($user["deck_no"]) ?></h3>
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
                            <h3><?= htmlspecialchars($user["play_no"]) ?></h3>
                            <figcaption>Decks Played</figcaption>
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
                <h2>Following</h2>
                <ul class="tag-list">
                    <?php
                    $likes = $db->getFollows($account_id, $_SESSION["account_id"] ?? null);
                    if (!$likes->isOk()) : ?>
                        <p>There was an error loading the users follows please try again</p>
                    <?php elseif ($likes->isEmpty()) : ?>
                        <p>This user currently has no follows, check back later</p>
                    <?php else : ?>
                        <?php foreach ($likes->array() as $tag) {
                            echo tag_card($tag);
                        } ?>
                    <?php endif; ?>
                </ul>
            </section>

            <section>
                <h2>Creations</h2>
                <ul class="deck-grid">
                    <?php
                    $creations = $db->getCreations($account_id, $_SESSION["account_id"] ?? null);
                    if (!$creations->isOk()) : ?>
                        <p>There was an error loading the users creations please try again</p>
                    <?php elseif ($creations->isEmpty()) : ?>
                        <p>This user currently has no creations, check back later </p>
                    <?php else : ?>
                        <?php foreach ($creations->array() as $deck) {
                            echo deck_card($deck, $db->getDeckTopics($deck["deck_id"]));
                        } ?>
                    <?php endif; ?>
                </ul>
            </section>
        </main>
    </div>
</body>