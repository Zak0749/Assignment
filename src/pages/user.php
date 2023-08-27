<?php include "../src/components/navbar.php"; ?>
<div class="page">

    <?php

    use database\DB;

    use function cards\deck_card;
    use function cards\tag_card;

    $db = new DB();

    $query = $db->prepare(<<<SQL
    SELECT User.user_id, username, 
        timestamp,
        streak_start,
        streak_last,
        COALESCE(decks, 0) as decks,
        COALESCE(total_plays, 0) as total_plays,
        COALESCE(average_score, 0) as average_score

    FROM User 
        LEFT JOIN (
            SELECT COUNT(Deck.user_id) as decks, 
                SUM(Deck.plays) as total_plays,
                Deck.user_id 
            FROM Deck 
            GROUP BY user_id
        )
        AS Decks ON Decks.user_id=User.user_id
        LEFT JOIN (SELECT AVG(User_Play.score) as average_score, user_id FROM User_Play GROUP BY user_id) AS Previous ON Previous.user_id=:user_id

    WHERE User.user_id=:user_id
SQL);
    $query->bindValue(":user_id", $_GET["user_id"] ?? null);
    $result = $query->execute();

    $user = $result->fetchArray();

    $streak_start = strtotime($user["streak_start"]);
    $streak_last =  strtotime($user["streak_last"]);
    $date_difference = $streak_last - $streak_start;

    if ($user == false) { ?>

        <header>
            <h1>User not found</h1>
        </header>
        <main>
            <p>The user requested does not exist</p>
        </main>


    <?php
    } else { ?>
        <header>
            <h1><?= $user["username"] ?></h1>

            <h2 class="subtitle">Joined <?= date("s/m/y", strtotime($user["timestamp"])) ?></h2>

        </header>

        <main>
            <section class="figure-grid">
                <figure class="statistic">
                    <span class="material-symbols-outlined">
                        add
                    </span>
                    <span>
                        <h3><?= $user["decks"] ?></h3>
                        <figcaption>Deck's Made</figcaption>
                    </span>
                </figure>
                <figure class="statistic">
                    <span class="material-symbols-outlined">
                        local_fire_department
                    </span>
                    <span>
                        <h3><?= floor($date_difference / 86400) ?></h3>
                        <figcaption>Saves</figcaption>
                    </span>
                </figure>
                <figure class="statistic">
                    <span class="material-symbols-outlined">
                        playing_cards
                    </span>
                    <span>
                        <h3><?= $user["total_plays"] ?></h3>
                        <figcaption>Total Plays</figcaption>
                    </span>
                </figure>
                <figure class="statistic">
                    <span class="material-symbols-outlined">
                        target
                    </span>
                    <span>
                        <h3><?= $user["average_score"] ?></h3>
                        <figcaption>Avg Score</figcaption>
                    </span>
                </figure>
            </section>

            <section>
                <h2>Likes</h2>
                <ul class="horizontal-list">
                    <?php $query = $db->prepare(<<<SQL
                    SELECT title, 
                        User_Likes.tag_id 
                    FROM User_Likes 
                        INNER JOIN Tag ON Tag.tag_id = User_Likes.tag_id
                    WHERE user_id=:user_id
                SQL);
                    $query->bindValue(":user_id", $_GET["user_id"] ?? null);
                    $result = $query->execute();
                    while ($tag = $result->fetchArray()) {
                        echo tag_card($tag);
                    } ?>
                </ul>
            </section>

            <section>
                <h2>Decks</h2>
                <ul class="grid-list">
                    <?php
                    $query = $db->prepare(<<<SQL
                    SELECT Deck.deck_id, 
                        Deck.title, 
                        Deck.plays, 
                        User.username,
                        CASE WHEN :user_id IS NULL THEN 0
                        WHEN EXISTS (
                        SELECT 1
                        FROM User_Save
                        WHERE User_Save.user_id = :user_id
                        AND User_Save.deck_id = Deck.deck_id
                        ) THEN 1 ELSE 0 END AS saved

                FROM Deck
                    INNER JOIN User ON Deck.user_id = User.user_id
                WHERE Deck.user_id = :user_id
                LIMIT 10
                SQL);

                    $query->bindValue(":user_id", $_GET["user_id"] ?? null);
                    $featured = $query->execute();

                    while ($deck = $featured->fetchArray()) {
                        echo deck_card($deck);
                    }
                    ?>
                </ul>
            </section>
        </main>
    <?php } ?>
</div>