<?php

use function helpers\random_from_array;

$r = new \Random\Randomizer();


$questions = random_from_array($all_questions, 4);

?>

<section class="play-question match-question" data-question-text="match" data-index="<?= $i + 1 ?>">
    <main class="match-grid">
        <section>
            <?php foreach ($r->shuffleArray($questions) as $question) : ?>
                <button class="play-key" onclick="matchAnswer(this)" data-question-id=<?= $question["question_id"] ?>>
                    <?= htmlspecialchars($question["key"]) ?>
                </button>
            <?php endforeach; ?>
        </section>
        <section>
            <?php foreach ($r->shuffleArray($questions) as $question) : ?>
                <button class="play-value" onclick="matchAnswer(this)" data-question-id=<?= $question["question_id"] ?>>
                    <?= htmlspecialchars($question["value"]) ?>
                </button>
            <?php endforeach; ?>
        </section>
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

        <button onclick="nextQuestion(this)" class="danger-