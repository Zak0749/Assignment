<?php

use function helpers\random_from_array;

$r = new \Random\Randomizer();


$questions = random_from_array($all_questions, 4);

?>

<section class="play-question match-question" data-question-text="match" data-index="<?= $i + 1 ?>" data-original="true"  style="display:<?= $i == 0 ? 'grid' : 'none' ?>">
    <main class="match-grid">
        <section>
            <?php foreach ($r->shuffleArray($questions) as $index => $question) : ?>
                <button class="question-card" onclick="matchAnswer(this)" data-question-id=<?= htmlspecialchars($question["card_id"]) ?> keyboard-shortcut="<?= $index + 1 ?>">
                    <?= htmlspecialchars($question["question"]) ?>
                </button>
            <?php endforeach; ?>
        </section>
        <section>
            <?php foreach ($r->shuffleArray($questions) as $index => $question) : ?>
                <button class="answer-card" onclick="matchAnswer(this)" data-question-id=<?= htmlspecialchars($question["card_id"]) ?> keyboard-shortcut="<?= $index + 5 ?>">
                    <?= htmlspecialchars($question["answer"]) ?>
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