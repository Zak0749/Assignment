<?php

use function helpers\random_from_array;

$questions = random_from_array($all_questions, 4);

$correct_question = random_from_array($questions);

$question_part = random_from_array(["key", "value"]);
$answer_part = $question_part == "key" ? "value" : "key";
?>

<section class="play-question select-question" data-correct-id="<?= $correct_question["question_id"] ?>" data-question-text="<?= $correct_question["key"] ?>" data-index="<?= $i + 1 ?>">

        <header class="question-display">
                <p class="play-<?= $question_part ?>">
                        <?= htmlspecialchars($correct_question[$question_part]) ?>
                </p>
        </header>

        <main class="question-answers-grid">
                <?php foreach ($questions as $question) : ?>
                        <button class="play-<?= $answer_part ?>" data-answer-id="<?= $question["question_id"] ?>" onclick="selectAnswer(this);">
                                <?= htmlspecialchars($question[$answer_part]) ?>
                        </button>
                <?php endforeach; ?>
        </main>

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

                <p><?= htmlspecialchars($correct_question[$answer_part]) ?></p>

                <button onclick="nextQuestion(this)" class="danger-button">
                        Next
                </button>
        </footer>
</section>