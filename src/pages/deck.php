<?php include "../src/components/navbar.php"; ?>
<div class="page">

    <?php

    use database\DB;

    use function cards\tag_card;
    use function cards\ur_mum;

    $db = new DB();

    $query = $db->prepare(<<<SQL
    SELECT title, 
        User.user_id,
        User.username, 
        Deck.timestamp, 
        Deck.description, 
        Deck.plays,
        COALESCE(Saves.saves, 0) as saves,
        COALESCE(Questions.questions, 0) as questions,
        Previous.user_plays,
        Previous.average_score,
        CASE WHEN :user_id IS NULL THEN 0
                WHEN EXISTS (
                    SELECT 1
                    FROM User_Save
                    WHERE User_Save.user_id = :user_id
                    AND User_Save.deck_id = Deck.deck_id
                ) THEN 1 ELSE 0 END AS saved
    FROM Deck 
        INNER JOIN User ON Deck.user_id = User.user_id
        LEFT JOIN (SELECT COUNT(User_Save.deck_id) as saves, deck_id FROM User_Save GROUP BY deck_id) AS Saves ON Saves.deck_id=Deck.deck_id
        LEFT JOIN (SELECT COUNT(Question.deck_id) as questions, deck_id FROM Question GROUP BY deck_id) AS Questions ON Questions.deck_id=Deck.deck_id
        LEFT JOIN (SELECT COUNT(User_Play.deck_id) as user_plays, AVG(User_Play.score) as average_score, deck_id, user_id FROM User_Play GROUP BY deck_id) AS Previous ON Previous.deck_id=Deck.deck_id AND Previous.user_id=:user_id
    WHERE Deck.deck_id=:deck_id
    SQL);



    $query->bindValue(":deck_id", $_GET["deck_id"]);
    $query->bindValue(":user_id", $_SESSION["user_id"]);


    $result = $query->execute();

    $deck = $result->fetchArray();

    if ($deck == false) { ?>

        <header>
            <h1>Deck not found</h1>
        </header>
        <main>
            <p>The deck requested does not exist</p>
        </main>


    <?php
    } else {
        $query = $db->prepare("SELECT 
    Tag.tag_id, title FROM Deck_Topic
        INNER JOIN Tag ON Deck_Topic.tag_id = Tag.tag_id 
        WHERE deck_id=:deck_id
");

        $query->bindValue(":deck_id", $_GET["deck_id"]);

        $tags = $query->execute();

        $query = $db->prepare("SELECT key, value FROM Question WHERE deck_id=:deck_id LIMIT 15");

        $query->bindValue(":deck_id", $_GET["deck_id"]);

        $questions = $query->execute();
    ?>

        <header>
            <div style="display: flex; justify-content: space-between;">
                <h1 class="page_margin"><?= $deck["title"] ?></h1>


                <div style="display: flex;">
                    <?php if ($_SESSION["user_id"] ?? null) : ?>
                        <button id="save_toggle" class="header-icon">
                            <?php if ($deck["saved"]) : ?>
                                <span class=" material-symbols-outlined">
                                    bookmark_add
                                </span>
                            <?php else : ?>
                                <span class=" material-symbols-outlined">
                                    bookmark
                                </span>
                            <?php endif ?>
                        </button>
                    <?php endif ?>
                    <?php if (($_SESSION["user_id"] ?? null) == $deck["user_id"]) : ?>
                        <a href="edit_deck?deck_id=<?= $_GET["deck_id"] ?>" class="header-icon">
                            <span class=" material-symbols-outlined">
                                edit
                            </span>
                        </a>
                    <?php endif ?>
                </div>

            </div>
            <div style="display: flex; justify-content: space-between;">
                <h2 class="subtitle"><a href="user?user_id=<?= $deck["user_id"] ?>"><?= $deck["username"] ?></a></h2>
                <h2 class="subtitle"> <?= date("s/m/y", strtotime($deck["timestamp"])) ?></h2>
            </div>
        </header>

        <main class="split-main">
            <section>
                <ul class="horizontal-list">
                    <?php while ($tag = $tags->fetchArray()) {
                        echo tag_card($tag);
                    } ?>
                </ul>


                <p><?= $deck["description"] ?></p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px">
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
                </div>

                <a style="margin-top:10px" class="form-button" href=" /play?deck_id=<?= $_GET["deck_id"] ?>">Play Round!</a>
            </section>

            <section>
                <h2>Questions: <?= $deck["questions"] ?></h2>
                <ul class="key-value-list">
                    <?php while ($question = $questions->fetchArray()) : ?>
                        <li class="key-value">
                            <h3><?= $question["key"] ?></h3>
                            <p><?= $question["value"] ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>
        </main>
    <?php } ?>
</div>