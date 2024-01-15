<?php

use function helpers\random_from_array;

$questions = random_from_array($all_questions, 4);

$correct_question = random_from_array($questions);

$question_part = random_from_array(["question", "answer"]);
$answer_part = $question_part == "question" ? "answer" : "question";
?>

<section class="select-question play-question" data-correct-id="<?= htmlspecialchars($correct_question["card_id"]) ?>" data-question-text="<?= htmlspecialchars($correct_question["question"]) ?>" data-index="<?= $i + 1 ?>" data-original="true">

        <header class="question-display">
                <p class="<?= $question_part ?>-card">
                        <?= htmlspecialchars($correct_question[$question_part]) ?>
                </p>
        </header>

        <main class="question-answers-grid">
                <?php foreach ($questions as $index => $question) : ?>
                        <button class="<?= $answer_part ?>-card" data-answer-id="<?= htmlspecialchars($question["card_id"]) ?>" onclick="selectAnswer(this);" keyboard-shortcut="<?= $index + 1 ?>">
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

                <p><?= htmlspecialchars($correct_question[$answer_part]) ?></p>

                <button onclick="nextQuestion(this)" class="danger-button button" keyboard-shortcut="n">
                        Next
                </button>
        </footer>
</section>