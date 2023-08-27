<?php

use database\DB;
use function cards\deck_card;

include "../src/components/navbar.php";
$db = new DB();
?>

<div class="page">

    <header>
        <h1 class="page-title">
            Discover

            <?php if ($_SESSION["user_id"] ?? null ?? null) : ?>
                <span class="streak-display">
                    <span class="material-symbols-outlined">
                        local_fire_department
                    </span>
                    <?php
                    $query = $db->prepare("SELECT streak_start, streak_last FROM User WHERE user_id=:user_id");

                    $query->bindValue(":user_id", $_SESSION["user_id"] ?? null);
                    $dates = $query->execute();


                    while ($date = $dates->fetchArray()) {
                        $streak_start = strtotime($date["streak_start"]);
                        $streak_last =  strtotime($date["streak_last"]);
                        $date_difference = $streak_last - $streak_start;

                        echo floor($date_difference / (60 * 60 * 24));
                    }

                    ?>
                </span>
            <?php endif; ?>
        </h1>
    </header>

    <main>
        <section>
            <h2>Featured</h2>

            <ul class="grid-list">
                <?php
                $query = $db->prepare("SELECT deck_id, title, plays, username, 
                CASE WHEN :user_id IS NULL THEN 0
                WHEN EXISTS (
                    SELECT 1
                    FROM User_Save
                    WHERE User_Save.user_id = :user_id
                    AND User_Save.deck_id = Deck.deck_id
                ) THEN 1 ELSE 0 END AS saved 
                
                FROM Deck  
                INNER JOIN User ON Deck.user_id = User.user_id 
                WHERE featured=1
                ORDER BY plays DESC
                LIMIT 10");

                $query->bindValue(":user_id", $_SESSION["user_id"] ?? null);
                $featured = $query->execute();

                while ($deck = $featured->fetchArray()) {
                    echo deck_card($deck);
                }
                ?>
            </ul>
        </section>


        <section>
            <?php if ($_SESSION["user_id"] ?? null ?? null) : ?>
                <h2>For You</h2>

                <ul class="grid-list">
                    <?php
                    $query = $db->prepare(<<<SQL
                SELECT Deck.deck_id, title, plays, username, 
                CASE WHEN :user_id IS NULL THEN 0
                WHEN EXISTS (
                    SELECT 1
                    FROM User_Save
                    WHERE User_Save.user_id = :user_id
                    AND User_Save.deck_id = Deck.deck_id
                ) THEN 1 ELSE 0 END AS saved 
                
                FROM User_Likes 
                    INNER JOIN Deck_Topic ON User_Likes.tag_id == Deck_Topic.tag_id 
                    INNER JOIN Deck ON Deck_Topic.deck_id == Deck.deck_id 
                    INNER JOIN User ON Deck.user_id = User.user_id
                    
                   WHERE User_Likes.user_id=:user_id ORDER BY plays DESC 
                LIMIT 10 
                SQL);

                    $query->bindValue(
                        ":user_id",
                        $_SESSION["user_id"] ?? null ?? null
                    );
                    $featured = $query->execute();

                    while ($deck = $featured->fetchArray()) {
                        echo deck_card($deck);
                    }
                    ?>
                </ul>
            <?php endif ?>
        </section>

        <section>
            <h2>Popular</h2>

            <ul class="grid-list">
                <?php
                $query = $db->prepare("SELECT deck_id, title, plays, username, 
                CASE WHEN :user_id IS NULL THEN 0
                WHEN EXISTS (
                    SELECT 1
                    FROM User_Save
                    WHERE User_Save.user_id = :user_id
                    AND User_Save.deck_id = Deck.deck_id
                ) THEN 1 ELSE 0 END AS saved 

                
                FROM Deck                     
                INNER JOIN User ON Deck.user_id = User.user_id 
                   ORDER BY plays DESC 
                LIMIT 10");

                $query->bindValue(":user_id", $_SESSION["user_id"] ?? null);
                $featured = $query->execute();

                while ($deck = $featured->fetchArray()) {
                    echo deck_card($deck);
                }
                ?>
            </ul>
        </section>
    </main>
</div>