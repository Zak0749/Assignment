<?php
// Begins the session for authenticating the user


// Imports
use database\DB;
use function cards\tag_card;

// If deck_id is not set send user to error page
if (!isset($_GET["deck_id"])) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// Establish Db connection
$db = new DB();

$deck_query = $db->getDeck($_GET["deck_id"]);

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
                    <?php if (isset($_SESSION["user_id"])) : ?>
                        <button data-deck-id="<?= htmlspecialchars($_GET['deck_id']) ?>" data-save="<?= boolval($deck["saved"]) ?>" id="save_toggle" class="header-icon" onclick="toggle_save(this)">
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

                    <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $deck["user_id"]) : ?>
                        <a href="edit-deck?deck_id=<?= htmlspecialchars($_GET["deck_id"]) ?>" class="header-icon">
                            <span class=" material-symbols-outlined">
                                edit
                            </span>
                        </a>
                    <?php endif ?>
                </div>

            </div>
            <div class="spaced-apart">
                <h2>
                    <a href="user?user_id=<?= $deck["user_id"] ?>">
                        <?= htmlspecialchars($deck["username"]) ?>
                    </a>
                </h2>
                <h2 class="subtitle">
                    <?= date("s/m/y", strtotime($deck["timestamp"])) ?>
                </h2>
            </div>

            <?php
            $topics = $db->getTopics($_GET["deck_id"]);
            if (!$topics->isOk()) :
            ?>
                <p>An error occurred when loading the tags please try again</p>
            <?php elseif (!$topics->isEmpty()) : ?>
                <ul class="tag-list">
                    <?php foreach ($topics->iterate() as $tag) {
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
                            <h3><?= $deck["plays"] ?></h3>
                            <figcaption>Plays</figcaption>
                        </span>
                    </figure>
                    <figure class="statistic">
                        <span class="material-symbols-outlined">
                            bookmark
                        </span>
                        <span>
                            <h3><?= $deck["saves"] ?></h3>
                            <figcaption>Saves</figcaption>
                        </span>
                    </figure>
                    <?php if ($deck["user_plays"] != 0) : ?>
                        <figure class="statistic">
                            <span class="material-symbols-outlined">
                                history
                            </span>
                            <span>
                                <h3><?= $deck["user_plays"] ?></h3>
                                <figcaption>Previous Plays</figcaption>
                            </span>
                        </figure>
                        <figure class="statistic">
                            <span class="material-symbols-outlined">
                                target
                            </span>
                            <span>
                                <h3><?= $deck["average_score"] ?></h3>
                                <figcaption>Avg Score</figcaption>
                            </span>
                        </figure>
                    <?php endif ?>
                </ul>

                <a class="primary-button" href="play?deck_id=<?= htmlspecialchars($_GET["deck_id"]) ?>">Play Round!</a>
            </section>

            <section>
                <h2>Questions: <?= $deck["questions"] ?></h2>
                <?php
                $questions = $db->getDeckQuestions($_GET["deck_id"]);
                if (!$questions->isOk() || $questions->isEmpty()) : ?>
                    <p>There was an error loading the questions please try again </p>
                <?php else : ?>
                    <ul class="question-list">
                        <?php foreach ($questions->iterate() as $question) : ?>
                            <li class="question-card">
                                <h3><?= htmlspecialchars($question["key"]) ?></h3>
                                <p><?= htmlspecialchars($question["value"]) ?></p>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>