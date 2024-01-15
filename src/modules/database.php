<?php

namespace database;

use PDO;
use PDOException;
use PDOStatement;

/**
 * The database class
 * Used to interact with the database
 * All methods will return a database result
 */
class Db
{
    // Stores the database class
    private PDO $db;

    /** 
     * Initialises the database connection
     */
    public function __construct()
    {
        // Get's the parameters for connecting to the database from the project environment 
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');
        $dbName = getenv('DB_NAME');
        $instanceHost = getenv('INSTANCE_HOST');

        // Generates a data source name for the database
        $dsn = "pgsql:" . "host=" . $instanceHost . ";dbname=" . $dbName;

        // Connects to the database and stores the connection in the class
        $this->db = new PDO($dsn, $username, $password);
    }

    public function getAllTags(
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        tag.tag_id,
                        tag.title,
                        is_followed(tag.tag_id, :user_account_id) as is_followed
                    FROM tag
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success return the rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getPopular(
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id,
                        deck.title,
                        account.username,
                        deck.account_id = :user_account_id as is_owned,
                        COUNT (*) as deck_play_no,
                        is_saved(deck.deck_id, :user_account_id) as is_saved
                    FROM deck
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    GROUP BY  deck.deck_id, account.account_id
                    ORDER BY deck_play_no DESC
                    LIMIT 16
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getForYou(
        string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id, 
                        deck.title, 
                        account.username, 
                        deck.account_id = :user_account_id as is_owned,
                        COUNT (*) as deck_play_no,
                        is_saved(deck.deck_id, :user_account_id) as is_saved
                    FROM follow 
                        LEFT JOIN topic ON follow.tag_id = topic.tag_id
                        LEFT JOIN deck ON topic.deck_id = deck.deck_id
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    WHERE follow.account_id=:user_account_id
                    GROUP BY  deck.deck_id, account.account_id
                    ORDER BY deck_play_no DESC 
                    LIMIT 16 
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getNew(
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT
                        deck.deck_id,
                        deck.title,
                        account.username,
                        deck.account_id = :user_account_id as is_owned,
                        COUNT (*) as deck_play_no,
                        is_saved(deck.deck_id, :user_account_id) as is_saved
                    FROM deck
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    GROUP BY  deck.deck_id, account.account_id
                    ORDER BY deck.timestamp DESC
                    LIMIT 16
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getDeckTopics(
        string $deck_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        tag.title, 
                        tag.tag_id, 
                        is_followed(tag.tag_id, :user_account_id) as is_followed
                    FROM topic 
                        LEFT JOIN tag ON topic.tag_id = tag.tag_id
                    WHERE deck_id=:deck_id
                SQL
            );

            // Binds the deck_id to the placeholder :deck_id
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);


            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getLogin(
        string $username
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        account.account_id,
                        account.password
                    FROM account
                    WHERE username = :username
                SQL
            );

            // Binds the username to the placeholder :username
            $query->bindValue(":username", $username, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function searchUsers(
        string $search_string,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        account.account_id,
                        account.username,
                        account.avatar,
                        account.account_id = :user_account_id as is_current_user,
                        COUNT (*) as deck_no 
                    FROM account
                        LEFT JOIN deck ON account.account_id = deck.account_id
                    WHERE account.username ILIKE :search_string
                    GROUP BY account.account_id
                    LIMIT 16;
                SQL
            );

            // Binds the search_sting to the placeholder :search_string
            $query->bindValue(":search_string", '%' . $search_string . '%', PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);



            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function searchDecks(
        string $search_string,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id,
                        deck.title,
                        account.username,
                        deck.account_id = :user_account_id as is_owned,
                        is_saved(deck.deck_id, :user_account_id) as is_saved,
                        COUNT (*) as deck_play_no
                    FROM deck
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    WHERE deck.title ILIKE :search_string
                    GROUP BY  deck.deck_id, account.account_id
                    LIMIT 16
                SQL
            );

            // Binds the search_sting to the placeholder :search_string
            $query->bindValue(":search_string", '%' . $search_string . '%', PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function searchTags(
        string $search_string,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        tag.tag_id,
                        tag.title,
                        is_followed(tag.tag_id, :user_account_id) as is_followed
                    FROM tag
                    WHERE tag.title ILIKE :search_string
                    LIMIT 16
                SQL
            );

            // Adds the search string to the query
            $query->bindValue(":search_string", '%' . $search_string . '%', PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function createAccount(
        string $username,
        string $password,
        string $avatar,
        array | null $likes
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the insert 
            $query = $this->db->prepare(
                <<<SQL
                    INSERT INTO account (
                        username, 
                        password,
                        avatar
                    ) VALUES (
                        :username, 
                        :password, 
                        :avatar
                    ) RETURNS account_id
            SQL
            );

            // Binds all the insert values to the corresponding placeholder in the query
            $query->bindValue(":username", $username, PDO::PARAM_STR);
            $query->bindValue(":avatar", $avatar, PDO::PARAM_STR);
            $query->bindValue(":password", $password, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Gets the id of the user that was just created
            $account_id = $query->fetch()["account_id"];

            // If there are any likes
            if ($likes !== null) {
                // Sets up the insert 
                $query = $this->db->prepare(
                    <<<SQL
                        INSERT INTO follow (
                            account_id, 
                            tag_id
                        ) VALUES (
                            :account_id, 
                            :tag_id
                        )
                    SQL
                );

                // Adds the user_id to the query
                $query->bindValue(":account_id", $account_id, PDO::PARAM_STR);

                // Defines the placeholder variable for the tag_id
                $tag = "";

                // binds the tag_id placeholder to the variable $tag
                $query->bindParam("tag_id", $tag, PDO::PARAM_STR);

                // for every tag 
                foreach ($likes as $tag) {
                    // Executes the query with the current tag id
                    $query->execute();
                }
            }

            // Insert has been a success returns the new user_id
            return DbResult::value($account_id);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getStreak(
        string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query 
            $query = $this->db->prepare(
                <<<SQL
                    SELECT user_streak(account.account_id)  as streak
                    FROM account 
                    WHERE account.account_id=:user_account_id
                SQL
            );

            $query->bindValue(":user_account_id", $user_account_id);


            // Executes the query
            $query->execute();

            $streak = $query->fetch()["streak"];

            // Query has been a success returns the found rows
            return DbResult::value($streak);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getUser(
        string $account_id,
        ?string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query 
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        account.account_id, 
                        account.username, 
                        account.timestamp,
                        account.avatar,
                        user_streak(account.account_id)  as streak,
                        account.account_id = :user_account_id as is_current_user,
                        COALESCE(user_deck.deck_no, 0) AS deck_no,
                        COALESCE(user_play.play_no, 0) AS play_no,
                        COALESCE(user_play.average_score, 0) AS average_score
                    FROM account 
                        LEFT JOIN (
                            SELECT account_id, COUNT (*) as deck_no FROM deck GROUP BY account_id
                        ) as user_deck ON user_deck.account_id = account.account_id
                        LEFT JOIN (
                            SELECT 
                                account_id, 
                                COUNT (*) as play_no,
                                AVG(play.score) as average_score
                            FROM play 
                            GROUP BY account_id
                        ) as user_play ON user_play.account_id = account.account_id
                    WHERE account.account_id=:account_id
                SQL
            );


            // Binds the given account_id to the placeholder in the query
            $query->bindValue(":account_id", $account_id, PDO::PARAM_STR);
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getFollows(
        string $account_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        tag.title, 
                        follow.tag_id ,
                        is_followed(tag.tag_id, :user_account_id) as is_followed
                    FROM follow
                        LEFT JOIN tag USING(tag_id)
                    WHERE account_id=:account_id
                SQL
            );

            // Binds the given user_id to the placeholder 
            $query->bindValue(":account_id", $account_id, PDO::PARAM_STR);
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getCreations(
        string $account_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id, 
                        deck.title, 
                        account.username,
                        deck.account_id = :user_account_id as is_owned,
                        is_saved(deck.deck_id, :user_account_id),
                       COUNT (*) as deck_play_no
                    FROM deck
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    WHERE deck.account_id = :account_id
                    GROUP BY  deck.deck_id, account.account_id
                    LIMIT 16
                SQL
            );

            // Binds the account_id given to the placeholder :account_id
            $query->bindValue(":account_id", $account_id, PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getDeck(
        string $deck_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                     SELECT 
                        deck.title, 
                        deck.timestamp, 
                        deck.description, 
                        account.account_id,
                        account.username, 
                        deck.account_id = :user_account_id AS is_owned,
                        is_saved(deck.deck_id, :user_account_id) AS is_saved,
                        COALESCE(play_stats.deck_play_no, 0) as deck_play_no,
                        COALESCE(play_stats.user_play_no, 0) as user_play_no,
                        COALESCE(play_stats.average_score, 0) as average_score,
                        COALESCE(deck_save.save_no, 0) as save_no
                    FROM deck 
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN (
                            SELECT 
                                deck_id,
                                COUNT (*) AS deck_play_no,
                                COUNT(*) FILTER (WHERE account_id = :user_account_id) AS user_play_no,
                                AVG(play.score) FILTER (WHERE account_id = :user_account_id) AS average_score 
                            FROM play 
                            GROUP BY play.deck_id
                        ) AS play_stats ON play_stats.deck_id = deck.deck_id
                        LEFT JOIN (
                            SELECT 
                                deck_id, 
                                COUNT(*) as save_no
                            FROM save
                            GROUP BY deck_id
                        ) AS deck_save ON deck_save.deck_id = deck.deck_id
                    WHERE deck.deck_id = :deck_id
                SQL
            );


            // Binds the deck_id given to the placeholder :deck_id
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getDeckCards(
        string $deck_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        card.card_id, 
                        card.question, 
                        card.answer 
                    FROM card 
                    WHERE deck_id=:deck_id 
                SQL
            );

            // Binds the deck_id given to the placeholder :deck_id
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getPlayCards(
        string $deck_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        card.card_id,
                        card.question,
                        card.answer 
                    FROM card 
                    WHERE deck_id=:deck_id 
                    ORDER BY RANDOM() 
                    LIMIT 20 
                SQL
            );

            // Binds the deck_id given to the placeholder :deck_id
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function popularByTag(
        string $tag_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                SELECT 
                    deck.deck_id, 
                    deck.title, 
                    COUNT (*) as deck_play_no,
                    deck.account_id = :user_account_id as is_owned,
                    account.username, 
                    is_saved(deck.deck_id, :user_account_id) as is_saved
                FROM topic 
                    LEFT JOIN deck ON topic.deck_id = deck.deck_id
                    LEFT JOIN account ON deck.account_id = account.account_id
                    LEFT JOIN play ON play.deck_id = deck.deck_id
                WHERE topic.tag_id = :tag_id
                GROUP BY  deck.deck_id, account.account_id
                ORDER BY deck_play_no DESC 
                LIMIT 16
            SQL
            );

            // Binds the tag_id given to the placeholder :tag_id
            $query->bindValue(":tag_id", $tag_id, PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function newByTag(
        string $tag_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id, 
                        deck.title, 
                        COUNT (*) as deck_play_no,
                        account.username, 
                        deck.account_id = :user_account_id as is_owned,
                        is_saved(deck.deck_id, :user_account_id) as is_saved
                    FROM topic   
                        LEFT JOIN deck ON topic.deck_id = deck.deck_id
                        LEFT JOIN account ON deck.account_id = account.account_id
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    WHERE topic.tag_id = :tag_id
                    GROUP BY  deck.deck_id, account.account_id
                    LIMIT 16
                SQL
            );

            // Binds the tag_id given to the placeholder :tag_id
            $query->bindValue(":tag_id", $tag_id, PDO::PARAM_STR);

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getTag(
        string $tag_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        tag.tag_id,
                        tag.title,
                        is_followed(tag.tag_id, :user_account_id) as is_followed
                    FROM tag 
                    WHERE tag_id = :tag_id
                SQL
            );

            // Binds the tag_id given to the placeholder :tag_id
            $query->bindValue(":tag_id", $tag_id, PDO::PARAM_STR);

            // Binds the tag_id given to the placeholder :tag_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getSaved(
        string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id, 
                        deck.title, 
                       COUNT (*) as deck_play_no,
                        account.username, 
                        deck.account_id = :user_account_id as is_owned,
                        is_saved(deck.deck_id, :user_account_id) as is_saved
                    FROM save  
                        LEFT JOIN deck ON save.deck_id = deck.deck_id
                        LEFT JOIN account ON deck.account_id = account.account_id 
                        LEFT JOIN play ON play.deck_id = deck.deck_id
                    WHERE save.account_id = :user_account_id
                    GROUP BY  deck.deck_id, account.account_id
                    LIMIT 16
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getRecent(
        string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        deck.deck_id, 
                        deck.title, 
                        account.username, 
                        deck.account_id = :user_account_id as is_owned,
                        is_saved(deck.deck_id, :user_account_id) as is_saved,
                        COUNT (*) as deck_play_no
                        FROM play
                            LEFT JOIN deck ON play.deck_id = deck.deck_id
                            LEFT JOIN account ON deck.account_id = account.account_id 
                            LEFT JOIN play as deck_play ON deck_play.deck_id = deck.deck_id
                        WHERE play.account_id = :user_account_id
                        GROUP BY deck.deck_id, account.account_id
                        LIMIT 16
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue("user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function save(
        string $deck_id,
        string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the insert
            $query = $this->db->prepare(
                <<<SQL
                    INSERT INTO save (
                        deck_id, 
                        account_id
                    ) VALUES (
                        :deck_id, 
                        :user_account_id
                    )
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Binds the specified deck_id to the :deck_id placeholder
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            // Executes the insert
            $query->execute();

            // Insert has been an success
            return DbResult::ok();
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function deleteSave(
        string $deck_id,
        string $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the delete
            $query = $this->db->prepare(
                <<<SQL
                    DELETE FROM save 
                    WHERE deck_id=:deck_id AND account_id=:user_account_id
                SQL
            );

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Binds the specified deck_id to the :deck_id placeholder
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            // Executes the delete
            $query->execute();

            // Delete has been an success
            return DbResult::ok();
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function updateAccount(
        string $user_account_id,
        string | null $username,
        string | null $password,
        string| null $avatar,
        array | null $added_likes,
        array | null $removed_likes
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the update
            $query = $this->db->prepare(
                <<<SQL
                    UPDATE
                        user
                    SET
                        username=COALESCE(:username, username),
                        password=COALESCE(:password, password),
                        avatar=COALESCE(:avatar, avatar)
                    WHERE account_id=:user_account_id
                SQL
            );

            // Inserts the parameters to the update statement
            $query->bindValue(":username", $username, PDO::PARAM_STR);
            $query->bindValue(":password", $password, PDO::PARAM_STR);
            $query->bindValue(":avatar", $avatar, PDO::PARAM_STR);
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            $query->execute();

            // If any likes has been added
            if ($added_likes !== null) {
                // Setup the query
                $query = $this->db->prepare(
                    <<<SQL
                        INSERT INTO follow (
                            account_id, 
                            tag_id
                        ) VALUES (
                            :user_account_id, 
                            :tag_id
                        ) 
                    SQL
                );

                // Inserts the parameters to the insert statement
                $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);
                $query->bindParam(":tag_id", $like, PDO::PARAM_STR);

                // Execute the query for each new like
                foreach ($added_likes as $like) {
                    $query->execute();
                }
            }

            // If any likes have been removed 
            if ($removed_likes !== null) {
                // Setup the query
                $query = $this->db->prepare(
                    <<<SQL
                        DELETE FROM follow 
                        WHERE account_id=:user_account_id AND tag_id=:tag_id 
                    SQL
                );

                // Inserts the parameters to the delete statement
                $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);
                $query->bindParam(":tag_id", $like, PDO::PARAM_STR);

                // Execute the query for each like
                foreach ($removed_likes as $like) {
                    $query->execute();
                }
            }

            // All the inserts have been a success 
            return DbResult::ok();
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function deleteAccount(string $user_account_id): DbResult
    {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the delete
            $query = $this->db->prepare("DELETE FROM user WHERE account_id=:account_id");

            // Binds the current users account_id to the placeholder :user_account_id
            $query->bindValue(":user_account_id", $user_account_id, PDO::PARAM_STR);

            // Executes the delete
            $query->execute();

            // Delete has been an success
            return DbResult::ok();
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function createDeck(
        string $user_account_id,
        string $title,
        string $description,
        array | null $topics,
        array $cards
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the insert
            $query = $this->db->prepare(
                <<<SQL
                    INSERT INTO deck (
                        account_id,
                        title,
                        description
                    )
                    VALUES (
                        :account_id,
                        :title,
                        :description
                    ) RETURNING deck_id
                SQL
            );

            $query->bindValue(":account_id", $user_account_id, PDO::PARAM_STR);
            $query->bindValue(":title", $title, PDO::PARAM_STR);
            $query->bindValue(":description", $description, PDO::PARAM_STR);

            $query->execute();

            $deck_id = $query->fetch()["deck_id"];

            $query = $this->db->prepare(
                <<<SQL
                    INSERT INTO card (
                        deck_id, 
                        question, 
                        answer
                    ) VALUES (
                        :deck_id,
                        :question,
                        :answer
                    )
                SQL
            );

            // Initialise variables to be bound to placeholders
            $question = "";
            $answer = "";

            // Inserts the parameters to the insert statement
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            // Binds the variables to the placeholders
            $query->bindParam(":question", $question, PDO::PARAM_STR);
            $query->bindParam(":answer", $answer, PDO::PARAM_STR);

            // For every question replace $question and $answer with the current values then insert them 
            foreach ($cards as ["question" => $question, "answer" => $answer]) {
                $query->execute();
            }

            // If any topics
            if ($topics !== null) {
                $query = $this->db->prepare(
                    <<<SQL
                        INSERT INTO topic (
                            deck_id, tag_id
                        )
                        VALUES (
                            :deck_id,
                            :tag_id
                        )
                    SQL
                );

                // Initialise variables to be bound to placeholders
                $topic = "";

                // Inserts the parameters to the insert statement
                $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

                // Binds the variables to the placeholders
                $query->bindParam(":tag_id", $topic, PDO::PARAM_STR);

                // For every topic replace $topic with the current value then execute the query
                foreach ($topics as $topic) {
                    $query->execute();
                }
            }

            // Query has been a success return the new deck_id
            return DbResult::value($deck_id);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function getAnnotatedTopics(
        string $deck_id,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the query
            $query = $this->db->prepare(
                <<<SQL
                    SELECT 
                        tag_id, 
                        title,
                        is_topic(tag.tag_id, :deck_id) as is_topic,
                        is_followed(tag.tag_id, :user_account_id) as is_followed
                    FROM tag
                SQL
            );

            // Inserts the parameters to the query
            $query->bindValue(":deck_id", $deck_id);
            $query->bindValue(":user_account_id", $user_account_id);

            // Executes the query
            $query->execute();

            // Query has been a success returns the found rows
            return DbResult::query($query);
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function updateDeck(
        string $deck_id,
        string | null $title,
        string | null $description,
        array | null $added_topics,
        array | null $removed_topics,
        array | null $new_cards,
        array | null $edited_cards,
        array | null $deleted_cards
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the update
            $query = $this->db->prepare(
                <<<SQL
                UPDATE Deck SET 
                    title=COALESCE(:title, title), 
                    description=COALESCE(:description, description) 
                WHERE deck_id=:deck_id
            SQL
            );

            $query->bindValue(":title", $title, PDO::PARAM_STR);
            $query->bindValue(":description", $description, PDO::PARAM_STR);
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            $query->execute();

            // If there are any new topics
            if ($added_topics !== null) {
                $query = $this->db->prepare(
                    <<<SQL
            INSERT INTO topic (deck_id, tag_id) 
            VALUES (:deck_id, :tag_id) 
            SQL
                );

                $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);
                $query->bindParam(":tag_id", $topic, PDO::PARAM_STR);

                foreach ($added_topics as $topic) {
                    $query->execute();
                }
            }

            // If any topics have been removed
            if ($removed_topics !== null) {
                $query = $this->db->prepare(
                    <<<SQL
                    DELETE FROM topic 
                    WHERE deck_id=:deck_id AND tag_id=:tag_id 
                SQL
                );

                $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);
                $query->bindParam(":tag_id", $like, PDO::PARAM_STR);

                foreach ($removed_topics as $like) {
                    $query->execute();
                }
            }

            // If there are any deleted cards
            if ($deleted_cards !== null) {
                $query = $this->db->prepare(<<<SQL
                    DELETE FROM card WHERE card_id=:card_id AND deck_id = :deck_id
                SQL);

                $card_id = "";

                $query->bindParam(":card_id", $card_id, PDO::PARAM_STR);
                $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

                foreach ($deleted_cards as $card_id) {
                    $query->execute();
                }
            }

            // If there are any edited cards
            if ($edited_cards !== null) {
                $query = $this->db->prepare(<<<SQL
                UPDATE card SET
                    question = COALESCE(:question, question),
                    answer = COALESCE(:answer, answer)
                WHERE card_id = :card_id AND deck_id = :deck_id
            SQL);

                $question = "";
                $answer = "";
                $card_id = "";

                $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);
                $query->bindParam(":question", $question, PDO::PARAM_STR);
                $query->bindParam(":answer", $answer, PDO::PARAM_STR);
                $query->bindParam(":card_id", $card_id, PDO::PARAM_STR);

                foreach ($edited_cards as ["question" => $question, "answer" => $answer, "card_id" => $card_id]) {
                    var_dump($card_id, $question, $answer);
                    $query->execute();
                }
            }

            // If there are any new cards
            if ($new_cards !== null) {
                $query = $this->db->prepare(<<<SQL
            INSERT INTO card (
                deck_id, question, answer
            )
            VALUES (
                :deck_id,
                :question,
                :answer
            )
        SQL);

                $question = "";
                $answer = "";

                $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);
                $query->bindParam(":question", $question, PDO::PARAM_STR);
                $query->bindParam(":answer", $answer, PDO::PARAM_STR);

                foreach ($new_cards as ["question" => $question, "answer" => $answer]) {
                    $query->execute();
                }
            }

            return DbResult::ok();
        } catch (PDOException $error) {
            var_dump($error);
            // http_response_code(500);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function deleteDeck(
        string $deck_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            // Sets up the delete
            $query = $this->db->prepare("DELETE FROM Deck WHERE deck_id = :deck_id");

            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);

            $query->execute();

            return DbResult::ok();
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }

    function savePlay(
        string $deck_id,
        int $score,
        string | null $user_account_id
    ): DbResult {
        // Tries to execute the operation but if any query fails go to the catch block
        try {
            $query = $this->db->prepare(<<<SQL
                INSERT INTO play (
                    account_id,
                    deck_id,
                    score
                ) VALUES (
                    :account_id,
                    :deck_id,
                    :score
                )
            SQL);

            $query->bindValue(":account_id", $user_account_id, PDO::PARAM_STR);
            $query->bindValue(":deck_id", $deck_id, PDO::PARAM_STR);
            $query->bindValue(":score", $score, PDO::PARAM_INT);

            $query->execute();

            return DbResult::ok();
        } catch (PDOException $error) {
            http_response_code(500);
            var_dump($error);
            // An error has occurred return an error with the code
            return DbResult::error($error->getCode());
        }
    }
}

// Internal state for the DbResult class
enum ResultState
{
        // When an error has occurred 
    case ERROR;

        // When a query result has been returned 
    case QUERY;

        // When the query has been a success and nothing is returned
    case OK;

        // When the query has been a success and a value is returned
    case VALUE;
}

/**
 * Represents the result from the db class
 */
class DbResult
{
    private $state;
    public $error;
    public $query;
    public $value;

    // Returns if the operation was a success or failure
    function isOk(): bool
    {
        // If the state is error return an error otherwise nothing
        return match ($this->state) {
            ResultState::ERROR => false,
            ResultState::QUERY, ResultState::OK, ResultState::VALUE => true
        };
    }

    function rowCount(): int
    {
        return $this->query->rowCount();
    }

    // Checks if the query result has any values
    function isEmpty(): bool
    {
        return $this->query->rowCount() === 0;
    }

    // Gets the result as an array
    function array(): array
    {
        return $this->query->fetchAll();
    }

    // Gets the first value from the query or returns false
    function single(): array | bool
    {
        return $this->query->fetch();
    }

    // Creates a db result all parameters except state are defaulted to null as they aren't all set
    private function __construct(
        ResultState $state,
        PDOStatement | null $query = null,
        string | null $error = null,
        mixed $value = null,
    ) {
        $this->state = $state;
        $this->query = $query;
        $this->error = $error;
        $this->value = $value;
    }

    public static function error(string $error)
    {
        return new DbResult(ResultState::ERROR, error: $error);
    }

    public static function query(PDOStatement $error)
    {
        return new DbResult(ResultState::QUERY, query: $error);
    }

    public static function ok()
    {
        return new DbResult(ResultState::OK);
    }

    public static function value(mixed $value)
    {
        return new DbResult(ResultState::VALUE, value: $value);
    }
}
