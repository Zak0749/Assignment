<?php

use function helpers\random_from_array;

$question = random_from_array($all_questions, 1);

$question_part = random_from_array(["key", "value"]);
$answer_part = $question_part == "key" ? "value" : "key";
?>

<section class="play-question self-question" data-question-text="<?= $question["key"] ?>" data-index="<?= $i + 1?>">
    <header class="question-display">
        <p class="play-<?= $question_part ?>">
            <?= htmlspecialchars($question[$question_part]) ?>
        </p>

        <p class="play-<?= $answer_part ?> self-answer">
            <?= htmlspecialchars($question[$answer_part]) ?>
        </p>
    </header>

    <footer>
        <button class="secondary-button reveal-button" onclick="displaySelfAnswer(this);">
            reveal
        </button>
    </footer>

    <footer class="self-answer-buttons">
        <button class="success-button" onclick="selfAnswer(this, 'correct')">
            correct
        </button>

        <button class="danger-button" onclick="selfAnswer(this, 'wrong')">
            wrong
        </button>
    </footer>

    <footer class="question-correct">
        <h2>
            <span class="material-symbols-outlined">
                check
            </span>
            Correct
        </h2>

        <button onclick="nextQuestion(this)" class="success-button">
            Next
        </button>
    </footer>
    <footer class="question-wrong">
        <h2>
            <span class="material-symbols-outlined">
                close
            </span>
            Wrong
        </h2>

        <button onclick="nextQuestion(this)" class="danger-button">
            Next
        </button>
    </footer>
</section>