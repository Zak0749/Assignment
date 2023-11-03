<?php

use function helpers\random_from_array;

$r = new \Random\Randomizer();


$questions = random_from_array($all_questions, 4);

?>

<section class="play-question match-question" data-question-text="match" data-index="<?= $i + 1 ?>" data-original="true">
    <main class="match-grid">
        <section>
            <?php foreach ($r->shuffleArray($questions) as $index => $question) : ?>
                <button class="play-key" onclick="matchAnswer(this)" data-question-id=<?= htmlspecialchars($question["question_id"]) ?> keyboard-shortcut="<?= $index + 1 ?>">
                    <?= htmlspecialchars($question["key"]) ?>
                </button>
            <?php endforeach; ?>
        </section>
        <section>
            <?php foreach ($r->shuffleArray($questions) as $index => $question) : ?>
                <button class="play-value" onclick="matchAnswer(this)" data-question-id=<?= htmlspecialchars($question["question_id"]) ?> keyboard-shortcut="<?= $index + 5 ?>">
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

        <button onclick="nextQuestion(this)" class="success-button" keyboard-shortcut="n">
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

        <button onclick="nextQuestion(this)" class="danger-button" keyboard-shortcut="n">
            Next
        </button>
    </footer>
</section>