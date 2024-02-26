<?php

use function helpers\random_from_array;

$question = random_from_array($all_questions, 1);

$question_part = random_from_array(["question", "answer"]);
$answer_part = $question_part == "question" ? "answer" : "question";
?>

<section class="self-question play-question" data-question-text="<?= htmlspecialchars($question["question"]) ?>" data-index="<?= $i + 1 ?>" data-original="true" style="display:<?= $i == 0 ? "grid" : "none" ?>">
    <header class="question-display">
        <p class="<?= $question_part ?>-card">
            <?= htmlspecialchars($question[$question_part]) ?>
        </p>

        <p class="<?= $answer_part ?>-card self-answer">
            <?= htmlspecialchars($question[$answer_part]) ?>
        </p>
    </header>

    <main>
        <button class="secondary-button reveal-button button" onclick="displaySelfAnswer(this);" keyboard-shortcut="r">
            reveal
        </button>
    </main>

    <main class="self-answer-buttons">
        <button class="success-button button" onclick="selfAnswer(this, 'correct')" keyboard-shortcut="c">
            correct
        </button>

        <button class="danger-button button" onclick="selfAnswer(this, 'wrong')" keyboard-shortcut="w">
            wrong
        </button>
    </main>

    <footer class="question-correct">
        <h2>
            <span class="material-symbols-outlined">
                check
            </span>
            Correct
        </h2>

        <button onclick="nextQuestion(this)" class="success-button button" keyboard-shortcut="n">
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

        <button onclick="nextQuestion(this)" class="danger-button button" keyboard-shortcut="n">
            Next
        </button>
    </footer>
</section>