<?php

// Imports
use database\DB;
use function cards\tag_card;

$deck_id = filter_input(INPUT_GET, "deck_id", FILTER_VALIDATE_REGEXP, [
    "options" => [
        'regexp' =>  '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i'
    ]
]);

// If deck_id is invalid or not set send user to error page
if ($deck_id === null || $deck_id === false) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// Establish Db connection
$db = new DB();

$deck_query = $db->getDeck($deck_id, $_SESSION["account_id"] ?? null);

// If error occurred send the user to an error page
if (!$deck_query->isOk()) {
    http_response_code(500);
    require("errors/500.php");
    exit;
}

// If the deck could not be found send the user to an error page
if ($deck_query->isEmpty()) {
    http_response_code(404);
    require("errors/404.php");
    exit;
}

$deck = $deck_query->single();
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
                    <?= htmlspecialchars($deck["title"]) ?>
                </h1>

                <div class="icon-bar">
                    <?php if (isset($_SESSION["account_id"]) && $_SESSION["account_id"] == $deck["account_id"]) : ?>
                        <a href="edit-deck?deck_id=<?= htmlspecialchars($deck_id) ?>" class="header-icon" keyboard-shortcut="e">
                            <span class=" material-symbols-outlined">
                                edit
                            </span>
                        </a>
                    <?php endif ?>

                    <?php if (isset($_SESSION["account_id"])) : ?>
                        <button data-deck-id="<?= htmlspecialchars($deck_id) ?>" data-save="<?= boolval($deck["saved"]) ?>" id="save_toggle" class="header-icon" onclick="toggleSave(this)" keyboard-shortcut="s">
                            <?php if ($deck["saved"]) : ?>
                                <span class="material-symbols-outlined">
                                    bookmark_added
                                </span>
                            <?php else : ?>
                                <span class=" material-symbols-outlined">
                                    bookmark_add
                                </span>
                            <?php endif ?>
                        </button>
                    <?php endif ?>
                </div>
            </div>

            <div class="spaced-apart">
                <h2>
                    <a href="account?account_id=<?= htmlspecialchars($deck["account_id"]) ?>">
                        <?= htmlspecialchars($deck["username"]) ?>
                    </a>
                </h2>

                <h2 class="subtitle">
                    <?= date("s/m/y", strtotime($deck["timestamp"])) ?>
                </h2>
            </div>

            <?php
            $topics = $db->getDeckTopics($deck_id);
            if (!$topics->isOk()) :
            ?>
                <p>An error occurred when loading the tags please try again</p>
            <?php elseif (!$topics->isEmpty()) : ?>
                <ul class="tag-list">
                    <?php foreach ($topics->array() as $tag) {
                        echo tag_card($tag);
                    } ?>
                </ul>
            <?php endif; ?>
        </header>

        <main class="split-main">
            <section>
                <p><?= htmlspecialchars($deck["description"]) ?></p>

                <ul class="statistic-grid">
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            playing_cards
                        </span>
                        <span>
                            <h3><?= htmlspecialchars($deck["deck_play_no"]) ?></h3>
                            <figcaption>Plays</figcaption>
                        </span>
                    </figure>
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            bookmark
                        </span>
                        <span>
                            <h3><?= htmlspecialchars($deck["save_no"]) ?></h3>
                            <figcaption>Saves</figcaption>
                        </span>
                    </figure>
                    <?php if ($deck["user_play_no"] != 0) : ?>
                        <figure class="statistic">
                            <span class="material-symbols-outlined">
                                history
                            </span>
                            <span>
                                <h3><?= $deck["user_play_no"] ?></h3>
                                <figcaption>Previous Plays</figcaption>
                            </span>
                        </figure>
                        <figure class="statistic">
                            <span class="material-symbols-outlined">
                                target
                            </span>
                            <span>
                                <h3><?= round($deck["average_score"], 2) ?></h3>
                                <figcaption>Avg Score</figcaption>
                            </span>
                        </figure>
                    <?php endif ?>
                </ul>

                <a class="primary-button" href="play?deck_id=<?= htmlspecialchars($deck_id) ?>" keyboard-shortcut="enter">Play Round!</a>
            </section>

            <section>
                <?php
                $questions = $db->getDeckQuestions($deck_id);
                if (!$questions->isOk() || $questions->isEmpty()) : ?>
                    <p>There was an error loading the questions please try again </p>
                <?php else : ?>
                    <h2>Questions: <?= htmlspecialchars($questions->rowCount()) ?></h2>
                    <ul class="question-list">
                        <?php foreach ($questions->array() as $question) : ?>
                            <li class="question-card">
                                <h3><?= htmlspecialchars($question["question"]) ?></h3>
                                <p><?= htmlspecialchars($question["answer"]) ?></p>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>