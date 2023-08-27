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
        </h1>
    </header>

    <main>


        <section>
            <h2>Saved</h2>

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
                
                FROM User_Save  
                    INNER JOIN Deck ON User_Save.deck_id = Deck.deck_id
                    INNER JOIN User ON Deck.user_id = User.user_id 
                WHERE User_Save.user_id = :user_id
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
            <h2>Creations</h2>

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
                
                FROM Deck  
                    INNER JOIN User ON Deck.user_id = User.user_id 
                WHERE Deck.user_id = :user_id
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
            <h2>Recents</h2>

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
                
                FROM User_Play  
                    INNER JOIN Deck ON User_Play .deck_id = Deck.deck_id
                    INNER JOIN User ON Deck.user_id = User.user_id 
                WHERE User_Play.user_id = :user_id
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