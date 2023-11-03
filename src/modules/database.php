<?php

namespace database;

use Generator;
use SQLite3;
use SQLite3Result;

/**
 * The database class
 * Used to interact with the database
 * All methods will return a database result
 */
class Db
{
    public SQLite3 $db;
    // Creates the database with preset location
    public function __construct()
    {
        // Connects to the database
        $this->db = new SQLite3($_ENV["PWD"] . "/database/db.sqlite");
        $this->db->enableExtendedResultCodes(true);
    }

    // Gets the title and id of all tags and returns a generator yielding each tag
    public function getAllTags(): DbResult
    {
        // Sets the sql
        $result = $this->db->query(
            <<<SQL
            SELECT tag_id,
                title
            FROM Tag
            SQL
        );

        // If query has failed return the error code
        if ($result == false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        // returns
        return DbResult::result($result);
    }

    // Gets the title and id of all tags and returns a generator yielding each deck
    function getPopular(): DbResult
    {
        // Sets up the query
        $query = $this->db->prepare(
            <<<SQL
            SELECT deck_id,
                title,
                plays,
                username,
                description,
                CASE
                    WHEN :user_id IS NULL THEN 0
                    WHEN EXISTS (
                        SELECT 1
                        FROM User_Save
                        WHERE User_Save.user_id = :user_id
                            AND User_Save.deck_id = Deck.deck_id
                    ) THEN 1
                    ELSE 0
                END AS saved
            FROM Deck
                INNER JOIN User ON Deck.user_id = User.user_id
            ORDER BY plays DESC
            LIMIT 16
            SQL
        );

        // Inserts the user_id into the statement if logged in otherwise null
        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        // Executes the query and returns it as a generator 
        return DbResult::result($result);
    }

    function getForYou(): DbResult
    {
        $query = $this->db->prepare(
            <<<SQL
            SELECT 
                Deck.deck_id, 
                title, 
                plays, 
                username, 
                description,
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
                LIMIT 16 
            SQL
        );

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getFeatured(): DbResult
    {
        $query = $this->db->prepare(
            <<<SQL
            SELECT 
                deck_id, 
                title, 
                plays, 
                username, 
                description,
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
                LIMIT 16
            SQL
        );

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getTopics(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT Tag.title, Tag.tag_id
            FROM Deck_Topic 
                INNER JOIN Tag ON Deck_Topic.tag_id = Tag.tag_id 
            WHERE Deck_Topic.deck_id=:deck_id
            SQL
        );

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getLogin(
        string $username
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT user_id,
                password
            FROM User
            WHERE username = :username
            SQL
        );

        $query->bindValue(":username", $username, SQLITE3_TEXT);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function searchUsers(
        string $search_string
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT User.user_id,
                username,
                avatar,
                COUNT(Deck.user_id) as deck_num
            FROM User
                LEFT JOIN Deck ON User.user_id = Deck.user_id
            WHERE username LIKE :search_string
                GROUP BY User.user_id;
            LIMIT 16
            SQL
        );
        $query->bindValue(":search_string", '%' . $search_string . '%', SQLITE3_TEXT);
        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function searchDecks(
        string $search_string
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT 
                deck_id,
                title,
                plays,
                username,
                description,
                CASE
                    WHEN :user_id IS NULL THEN 0
                    WHEN EXISTS (
                        SELECT 1
                        FROM User_Save
                        WHERE User_Save.user_id = :user_id
                            AND User_Save.deck_id = Deck.deck_id
                    ) THEN 1
                    ELSE 0
                END AS saved
            FROM Deck
                INNER JOIN User ON Deck.user_id = User.user_id
            WHERE title LIKE :search_string
                LIMIT 16
            SQL
        );
        $query->bindValue(":search_string", '%' . $search_string . '%', SQLITE3_TEXT);
        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function searchTags(
        string $search_string
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT tag_id,
                title
            FROM Tag
            WHERE title LIKE :search_string
            LIMIT 16
            SQL
        );

        $query->bindValue(":search_string", '%' . $search_string . '%', SQLITE3_TEXT);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function createAccount(
        string $username,
        string $password,
        string $avatar,
        array | null $likes
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            INSERT INTO User (username, password, avatar)
            VALUES (:username, :password, :avatar)
            SQL
        );

        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $query->bindValue(":avatar", $avatar, SQLITE3_TEXT);
        $query->bindValue(":password", $password, SQLITE3_TEXT);

        if (!$query->execute()) {
            return DbResult::error($this->db->lastErrorCode());
        }

        $user_id = $this->db->lastInsertRowID();

        if ($likes !== null) {
            $query = $this->db->prepare(
                <<<SQL
            INSERT INTO User_Likes (user_id, tag_id) VALUES (:user_id, :tag_id)
            SQL
            );

            $query->bindValue(":user_id", $user_id, SQLITE3_INTEGER);
            $query->bindParam("tag_id", $tag, SQLITE3_TEXT);


            foreach ($likes as $tag) {
                $result = $query->execute();
                if ($result === false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        return DbResult::value($user_id);
    }

    function getUser(
        int $user_id
    ): DbResult {
        // PS Actaully look at code as middle bit not sure on
        // I GOT IT WORKING YAAAAAAS
        $query = $this->db->prepare(
            <<<SQL
            SELECT User.user_id, username, 
                timestamp,
                COALESCE((WITH EventsWithGaps AS (
                    SELECT timestamp,
                        julianday(
                            date(
                                LAG (timestamp, -1, julianday("now") + 1) OVER (
                                    ORDER BY timestamp ASC
                                )
                            )
                        ) - julianday(date(timestamp)) as untilNext
                    FROM User_Play
                    WHERE User_Play.user_id = User.user_id
                    ORDER BY timestamp
                    )
                    SELECT SUM(consecutive_ones) AS consecutive_ones_count
                    FROM (
                            SELECT *,
                                ROW_NUMBER() OVER (
                                    ORDER BY timestamp
                                ) - ROW_NUMBER() OVER (
                                    PARTITION BY untilNext
                                    ORDER BY timestamp
                                ) AS grp,
                                CASE
                                    WHEN untilNext = 1 THEN 1
                                    ELSE 0
                                END AS consecutive_ones
                            FROM EventsWithGaps
                        ) AS subquery
                    WHERE grp = 0
                        AND untilNext >= 1
                    LIMIT 1
            ), 0) as streak,

                avatar,
                COALESCE(decks, 0) as decks,
                COALESCE(total_plays, 0) as total_plays,
                COALESCE(average_score, 2) as average_score

            FROM User 
                LEFT JOIN (
                    SELECT COUNT(Deck.user_id) as decks, 
                        SUM(Deck.plays) as total_plays,
                        Deck.user_id 
                    FROM Deck 
                    GROUP BY user_id
                )
                AS Decks ON Decks.user_id=User.user_id
                LEFT JOIN 
                    (
                        SELECT AVG(User_Play.score) as average_score, 
                            user_id 
                        FROM User_Play 
                        GROUP BY user_id
                    ) AS Previous 
                ON Previous.user_id=:user_id
            WHERE User.user_id=:user_id
            SQL
        );

        $query->bindValue(":user_id", $user_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }



        return DbResult::result($result);
    }

    function getLikes(
        int $user_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
                    SELECT title, 
                        User_Likes.tag_id 
                    FROM User_Likes 
                        INNER JOIN Tag ON Tag.tag_id = User_Likes.tag_id
                    WHERE user_id=:user_id
                    SQL
        );
        $query->bindValue(":user_id", $user_id, SQLITE3_INTEGER);
        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getCreations(
        int $user_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
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
            LIMIT 16
            SQL
        );

        $query->bindValue(":user_id", $user_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getDeck(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
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
                LEFT JOIN (
                    SELECT COUNT(User_Save.deck_id) as saves, 
                        deck_id 
                    FROM User_Save 
                    GROUP BY deck_id
                ) AS Saves 
                ON Saves.deck_id=Deck.deck_id
                LEFT JOIN (
                    SELECT COUNT(Question.deck_id) as questions, 
                        deck_id
                     FROM Question 
                     GROUP BY deck_id
                ) AS Questions 
                ON Questions.deck_id=Deck.deck_id
                LEFT JOIN (
                    SELECT COUNT(User_Play.deck_id) as user_plays, 
                        AVG(User_Play.score) as average_score, 
                        deck_id, 
                        user_id 
                    FROM User_Play 
                    GROUP BY deck_id
                ) AS Previous 
                ON Previous.deck_id=Deck.deck_id AND Previous.user_id=:user_id
            WHERE Deck.deck_id=:deck_id
            SQL
        );

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id",  $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getDeckQuestions(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
                SELECT question_id, 
                    key, 
                    value 
                FROM Question 
                WHERE deck_id=:deck_id 
                SQL
        );

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getPlayQuestions(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
                SELECT question_id,
                    key,
                    value 
                FROM Question 
                WHERE deck_id=:deck_id 
                ORDER BY RANDOM() 
                LIMIT 20 
                SQL
        );

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function popularByTag(
        int $tag_id
    ): DbResult {
        $query = $this->db->prepare(
            "SELECT Deck.deck_id, Deck.title, Deck.plays, User.username, 
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
                WHERE Deck_Topic.tag_id = :tag_id
                ORDER BY plays DESC 
                LIMIT 16"
        );

        $query->bindValue(":tag_id", $tag_id, SQLITE3_INTEGER);

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function newByTag(
        int $tag_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT Deck.deck_id, Deck.title, Deck.plays, User.username, 
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
                WHERE Deck_Topic.tag_id = :tag_id
                ORDER BY Deck.timestamp ASC 
                LIMIT 16 
            SQL
        );

        $query->bindValue(":tag_id", $tag_id);

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getTag(
        int $tag_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT title FROM Tag WHERE tag_id = :tag_id
            SQL
        );

        $query->bindValue(":tag_id", $tag_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getSaved(): DbResult
    {
        $query = $this->db->prepare(
            <<<SQL
            SELECT Deck.deck_id, Deck.title, Deck.plays, User.username, 
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
                LIMIT 16
            SQL
        );

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function getRecent(): DbResult
    {
        $query = $this->db->prepare(
            <<<SQL
            SELECT Deck.deck_id, Deck.title, Deck.plays, User.username, 
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
                GROUP BY User_Play.deck_id
                LIMIT 16
            SQL
        );

        if (isset($_SESSION["user_id"])) {
            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        }

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function save(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
                INSERT INTO User_Save 
                    (deck_id, user_id) 
                VALUES (:deck_id, :user_id)
            SQL
        );

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
        $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::success();
    }

    function deleteSave(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
                DELETE FROM User_Save 
                WHERE deck_id=:deck_id AND user_id=:user_id 
                SQL
        );

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
        $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::success();
    }

    function getTagsWithLikes(): DbResult
    {
        $query = $this->db->prepare(
            <<<SQL
            SELECT tag_id, 
            title,
            CASE WHEN EXISTS 
                (
                    SELECT 1 
                    FROM User_Likes 
                    WHERE 
                        User_Likes.user_id=:user_id AND 
                        Tag.tag_id = User_Likes.tag_id
                )
            THEN 1 
            ELSE 0 
            END as checked
            FROM Tag 
            SQL
        );

        $query->bindValue(":user_id", $_SESSION["user_id"]);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function updateAccount(
        string | null $username,
        string | null $password,
        string| null $avatar,
        array | null $added_likes,
        array | null $removed_likes
    ): DbResult {
        // COALESCE if null don't change value otherwise update
        $query = $this->db->prepare(
            <<<SQL
            UPDATE 
                User 
            SET 
                username=COALESCE(:username, username), 
                password=COALESCE(:password, password),
                avatar=COALESCE(:avatar, avatar)
            WHERE
                user_id=:user_id
        SQL
        );

        $query->bindValue(":username", $username, SQLITE3_TEXT);
        $query->bindValue(":password", $password, SQLITE3_TEXT);
        $query->bindValue(":avatar", $avatar, SQLITE3_TEXT);
        $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);


        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        if ($added_likes !== null) {
            $query = $this->db->prepare(
                <<<SQL
            INSERT INTO User_Likes (user_id, tag_id) 
            VALUES (:user_id, :tag_id) 
            SQL
            );

            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
            $query->bindParam(":tag_id", $like, SQLITE3_INTEGER);

            foreach ($added_likes as $like) {
                $result = $query->execute();

                if ($result === false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        if ($removed_likes !== null) {
            $query = $this->db->prepare(
                <<<SQL
            DELETE FROM User_Likes 
            WHERE user_id=:user_id AND tag_id=:tag_id 
            SQL
            );

            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
            $query->bindParam(":tag_id", $like, SQLITE3_INTEGER);

            foreach ($removed_likes as $like) {
                $result = $query->execute();

                if ($result === false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        return DbResult::success();
    }

    function deleteAccount($user_id): DbResult
    {
        $query = $this->db->prepare("DELETE FROM User WHERE user_id=:user_id");

        $query->bindValue(":user_id", $user_id);

        $result = $query->execute();

        if ($result == false) {
            return DbResult::error($this->db->lastErrorCode());
        } else {
            return DbResult::success();
        }
    }

    function createDeck(
        string $title,
        string $description,
        array | null $topics,
        array $questions
    ): DbResult {
        $query = $this->db->prepare(<<<SQL
             INSERT INTO Deck (
                user_id,
                title,
                description
            )
            VALUES (
                :user_id,
                :title,
                :description
            )
        SQL);

        $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
        $query->bindValue(":title", $title, SQLITE3_TEXT);
        $query->bindValue(":description", $description, SQLITE3_TEXT);

        $result = $query->execute();

        if ($result == false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        $deck_id = $this->db->lastInsertRowID();

        $query = $this->db->prepare(<<<SQL
            INSERT INTO Question (
                deck_id, key, value
            )
            VALUES (
                :deck_id,
                :key,
                :value
            )
        SQL);

        $key = "";
        $value = "";

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
        $query->bindParam(":key", $key, SQLITE3_TEXT);
        $query->bindParam(":value", $value, SQLITE3_TEXT);

        foreach ($questions as $question) {
            ["key" => $key, "value" => $value] = $question;
            $result = $query->execute();

            if ($result == false) {
                return DbResult::error($this->db->lastErrorCode());
            }
        }


        if ($topics !== null) {
            $query = $this->db->prepare(
                <<<SQL
                INSERT INTO Deck_Topic (
                    deck_id, tag_id
                )
                VALUES (
                    :deck_id,
                    :tag_id
                )
        SQL
            );

            $topic = "";

            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
            $query->bindParam(":tag_id", $topic, SQLITE3_INTEGER);

            foreach ($topics as $topic) {
                $result = $query->execute();

                if ($result == false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        return DbResult::value($deck_id);
    }

    function getAnnotatedTopics(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare(
            <<<SQL
            SELECT tag_id, 
            title,
            CASE WHEN EXISTS 
                (
                    SELECT 1 
                    FROM Deck_Topic
                    WHERE 
                        Deck_Topic.deck_id = :deck_id AND
                        Tag.tag_id = Deck_Topic.tag_id
                )
            THEN 1 
            ELSE 0 
            END as checked
            FROM Tag 
            SQL
        );

        $query->bindValue(":deck_id", $deck_id);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::result($result);
    }

    function updateDeck(
        int $deck_id,
        string | null $title,
        string | null $description,
        array | null $added_topics,
        array | null $removed_topics,
        array | null $new_questions,
        array | null $edited_questions,
        array | null $deleted_questions
    ): DbResult {
        // Title and description

        $query = $this->db->prepare("UPDATE Deck SET title=COALESCE(:title, title), description=COALESCE(:description, description) WHERE deck_id=:deck_id");

        $query->bindValue(":title", $title, SQLITE3_TEXT);
        $query->bindValue(":description", $description, SQLITE3_TEXT);
        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result == false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        // Now update topics

        if ($added_topics !== null) {
            $query = $this->db->prepare(
                <<<SQL
            INSERT INTO Deck_Topic (deck_id, tag_id) 
            VALUES (:deck_id, :tag_id) 
            SQL
            );

            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
            $query->bindParam(":tag_id", $topic, SQLITE3_INTEGER);

            foreach ($added_topics as $topic) {
                $result = $query->execute();

                if ($result === false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        if ($removed_topics !== null) {
            $query = $this->db->prepare(
                <<<SQL
                    DELETE FROM Deck_Topic 
                    WHERE deck_id=:deck_id AND tag_id=:tag_id 
                SQL
            );

            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
            $query->bindParam(":tag_id", $like, SQLITE3_INTEGER);

            foreach ($removed_topics as $like) {
                $result = $query->execute();

                if ($result === false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        // Question edits
        if ($deleted_questions !== null) {
            $query = $this->db->prepare(<<<SQL
            DELETE FROM Question WHERE question_id=:question_id AND deck_id = :deck_id
        SQL);

            $question_id = null;
            $query->bindParam(":question_id", $question_id, SQLITE3_INTEGER);
            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

            foreach ($deleted_questions as $question_id) {
                $result = $query->execute();

                if ($result == false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        if ($edited_questions !== null) {
            $query = $this->db->prepare(<<<SQL
                UPDATE Question SET
                    key = COALESCE(:key, key),
                    value = COALESCE(:value, value)
                WHERE question_id = :question_id AND deck_id = :deck_id
            SQL);

            $key = null;
            $value = null;
            $question_id = null;

            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
            $query->bindParam(":key", $key, SQLITE3_TEXT);
            $query->bindParam(":value", $value, SQLITE3_TEXT);
            $query->bindParam(":question_id", $question_id, SQLITE3_TEXT);

            foreach ($edited_questions as $question) {
                ["key" => $key, "value" => $value, "id" => $question_id] = $question;

                $result = $query->execute();

                if ($result == false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        if ($new_questions !== null) {
            $query = $this->db->prepare(<<<SQL
            INSERT INTO Question (
                deck_id, key, value
            )
            VALUES (
                :deck_id,
                :key,
                :value
            )
        SQL);

            $key = "";
            $value = "";

            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
            $query->bindParam(":key", $key, SQLITE3_TEXT);
            $query->bindParam(":value", $value, SQLITE3_TEXT);

            foreach ($new_questions as $question) {
                ["key" => $key, "value" => $value] = $question;
                $result = $query->execute();

                if ($result == false) {
                    return DbResult::error($this->db->lastErrorCode());
                }
            }
        }

        return DbResult::success();
    }

    function deleteDeck(
        int $deck_id
    ): DbResult {
        $query = $this->db->prepare("DELETE FROM Deck WHERE deck_id = :deck_id");

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result === false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        return DbResult::success();
    }

    function savePlay(
        int $deck_id,
        int $score
    ): DbResult {
        $query = $this->db->prepare(<<<SQL
            UPDATE Deck SET 
                plays = plays + 1
            WHERE deck_id=:deck_id
        SQL);

        $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);

        $result = $query->execute();

        if ($result == false) {
            return DbResult::error($this->db->lastErrorCode());
        }

        if (isset($_SESSION["user_id"])) {
            $query = $this->db->prepare(<<<SQL
            INSERT INTO 
                User_Play (
                user_id,
                deck_id,
                score
            )
            VALUES (
                :user_id,
                :deck_id,
                :score
            )
        SQL);

            $query->bindValue(":user_id", $_SESSION["user_id"], SQLITE3_INTEGER);
            $query->bindValue(":deck_id", $deck_id, SQLITE3_INTEGER);
            $query->bindValue(":score", $score, SQLITE3_INTEGER);

            $result = $query->execute();

            if ($result == false) {
                return DbResult::error($this->db->lastErrorCode());
            }
        }

        return DbResult::success();
    }
}

/**
 * Returned from all db queries
 * States:
 *     Result of query
 *     Error
 *     Success with value
 *     Success with no value
 */
class DbResult
{
    public $error;
    public $result;
    public $value;

    function isOk(): bool
    {
        return $this->error === null;
    }

    function isEmpty(): bool
    {
        $first_row = $this->result->fetchArray();
        $this->result->reset();
        return $first_row === false;
    }

    function iterate(): Generator
    {
        while ($row = $this->result->fetchArray(SQLITE3_ASSOC)) {
            yield $row;
        }
    }

    function array(): array
    {
        return iterator_to_array($this->iterate());
    }

    function single(): array | bool
    {
        return $this->result->fetchArray(SQLITE3_ASSOC);
    }

    // Used when success
    private function __construct(
        SQLite3Result | null $result,
        int | null $error,
        mixed $value
    ) {
        $this->result = $result;
        $this->error = $error;
        $this->value = $value;
    }


    // Returns the result of the db query
    static function result(SQLite3Result $result)
    {
        return new DbResult($result, null, null);
    }

    // Constructor when wrong
    static function error(int $error)
    {
        return new DbResult(null, $error, null);
    }

    // Constructor when value is not from a query e.g. the id of an inserted row
    static function value($value)
    {
        return new DbResult(null, null, $value);
    }

    static function success()
    {
        return new DbResult(null, null, null);
    }
}
