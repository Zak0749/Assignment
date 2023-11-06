<?php

// Imports
use database\Db;

use function helpers\random_from_array;

$deck_id = filter_input(INPUT_GET, "deck_id", FILTER_VALIDATE_INT);

// If deck_id is invalid or not set send user to error page
if ($deck_id === null || $deck_id === false) {
    http_response_code(400);
    require("errors/400.php");
    exit;
}

// Establish database connection
$db = new Db();

// Get the deck
$deck_query = $db->getDeck($deck_id);

// If error occurred when getting deck send user to error page
if (!$deck_query->isOk()) {
    http_response_code(500);
    require("errors/500.php");
    exit;
}

// If deck doesn't exist send user to error page
if ($deck_query->isEmpty()) {
    http_response_code(404);
    require("errors/404.php");
    exit;
}

$deck = $deck_query->single();

// Get the deck
$question_query = $db->getPlayQuestions($deck_id);

// If error occurred when getting the questions send user to error page
if (!$question_query->isOk()) {
    http_response_code(500);
    require("errors/500.php");
    exit;
}

// If questions no not exist send user to error page
if ($question_query->isEmpty()) {
    http_response_code(404);
    require("errors/404.php");
    exit;
}

$all_questions = $question_query->array();

$question_types = ["select", "match", "self"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php require "components/head.php" ?>
</head>

<body>
    <div class="page">
        <header class="row">
            <progress id="play-progress" value="0" max="12"></progress>

            <div class="icon-bar">
                <a class="header-icon" type="button" href="/deck?deck_id=<?= htmlspecialchars($deck_id) ?>" keyboard-shortcut="esc">
                    <span class="material-symbols-outlined">
                        close
                    </span>
                </a>
            </div>
        </header>
        <main class="full-screen">
            <ul id="play-question-list" data-deck-id="<?= htmlspecialchars($deck_id) ?>">
                <?php foreach (range(0, 11) as $i) :
                    // Chooses a random question type out of the array
                    $type = random_from_array($question_types);

                    require match ($type) {
                        'select' => 'components/select.php',
                        'define' => 'components/define.php',
                        'match' => 'components/match.php',
                        'self' => 'components/self.php'
                    };
                ?>
                <?php endforeach ?>

                <section class="retry-page">
                    <header>
                        <h2>Retry</h2>
                        <p>Now it's time to retry all the questions you got wrong and fix your mistakes</p>
                    </header>

                    <main>
                        <button onclick="nextQuestion(this)" class="secondary-button" keyboard-shortcut="n">
                            Next
                        </button>
                    </main>
                </section>
            </ul>

            <section id="play-results" style="display:none;">
                <h1>Results for <?= htmlspecialchars($deck["title"]) ?></h1>

                <div class="result-chart-section">
                    <canvas id="results-chart"></canvas>

                    <div class="spaced-apart result-chart-legend">
                        <legend>
                            <div class="correct-marker"></div>
                            <h3>Correct:
                                <div id="correct-number"></div>
                            </h3>
                        </legend>

                        <legend>
                            <div class="wrong-marker"></div>
                            <h3>Wrong:
                                <div id="wrong-number"></div>
                            </h3>
                        </legend>
                    </div>
                </div>

                <div>
                    <a class="primary-button" href="deck?deck_id=<?= htmlspecialchars($deck_id) ?>" keyboard-shortcut="esc">
                        Exit
                    </a>
                </div>

                <div>
                    <table id="results-table">
                        <thead>
                            <th>No.</th>
                            <th>Question</th>
                            <th>Result</th>
                        </thead>
                        <tbody id="results-table-body">

                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>