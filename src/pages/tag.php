<?php
include "../src/components/navbar.php"; ?>

<div class="page">
    <?php

    use database\DB;

    use function cards\deck_card;

    $db = new DB();
    $query = $db->prepare(<<<SQL
    SELECT title FROM Tag WHERE tag_id = :tag_id
SQL);

    $query->bindValue(":tag_id", $_GET["tag_id"]);

    $result = $query->execute();

    $tag = $result->fetchArray();

    if ($tag) :
    ?>
        <header>
            <h1><?= $tag["title"] ?></h1>
        </header>

        <main>

            <section>
                <h2>Popular</h2>

                <ul class="grid-list">
                    <?php
                    $query = $db->prepare("SELECT Deck.deck_id, Deck.title, Deck.plays, User.username, 
                CASE WHEN :user_id IS NULL THEN 0
                WHEN EXISTS (
                    SELECT 1
                    FROM User_Save
                    WHERE User_Save.user_id = :user_id
                    AND User_Save.deck_id = Deck.deck_id
                ) THEN 1 ELSE 0 END AS saved 

                FROM Deck_Topic   
                    INNER JOIN Deck ON Deck_Topic.deck_id = Deck.deck_id   
                    INNER JOIN User ON Deck.user_id = User.user_id 
                ORDER BY plays DESC 
                LIMIT 10");

                    $query->bindValue(":user_id", $_SESSION["user_id"] ?? null);
                    $query->bindValue(":user_id", $_GET["tag_id"]);
                    $featured = $query->execute();

                    while ($deck = $featured->fetchArray()) {
                        echo deck_card($deck);
                    }
                    ?>
                </ul>
            </section>

            <section>
                <h2>New</h2>

                <ul class="grid-list">
                    <?php
                    $query = $db->prepare("SELECT Deck.deck_id, Deck.title, Deck.plays, User.username, 
                CASE WHEN :user_id IS NULL THEN 0
                WHEN EXISTS (
                    SELECT 1
                    FROM User_Save
                    WHERE User_Save.user_id = :user_id
                    AND User_Save.deck_id = Deck.deck_id
                ) THEN 1 ELSE 0 END AS saved 

                FROM Deck_Topic   
                    INNER JOIN Deck ON Deck_Topic.deck_id = Deck.deck_id   
                    INNER JOIN User ON Deck.user_id = User.user_id 
                ORDER BY Deck.timestamp ASC 
                LIMIT 10");

                    $query->bindValue(":user_id", $_SESSION["user_id"] ?? null);
                    $query->bindValue(":user_id", $_GET["tag_id"]);
                    $featured = $query->execute();

                    while ($deck = $featured->fetchArray()) {
                        echo deck_card($deck);
                    }
                    ?>
                </ul>
            </section>
        </main>

    <?php else : ?>
        <header>
            <h1>Tag not found</h1>
        </header>
        <main>
            <p>The tag requested does not exist</p>
        </main>
    <?php endif ?>

</div>