<?php
// Get classes and functions
use database\DB;
use function cards\deck_card;
use function cards\tag_card;
use function cards\user_card;

// Connect to the database
$db = new DB();

// Get a max of 10 tags that mach the search string
$query = $db->prepare(<<<SQL
    SELECT tag_id,
        title
    FROM Tag
    WHERE title LIKE :search_string
    LIMIT 10
  SQL);
$query->bindValue(":search_string", "%" . $_GET["search_string"] . "%", SQLITE3_TEXT);
$results = $query->execute();
if ($results->fetchArray()) : ?>
    <h2>Tags</h2>

    <ul class="horizontal-list">
        <?php $results->reset();
        while ($tag = $results->fetchArray()) {
            echo tag_card($tag);
        } ?>
    </ul>
<?php endif;

// Get a max of 10 users that mach the search string
$query = $db->prepare(<<<SQL
    SELECT User.user_id,
        username,
        COUNT(Deck.user_id) as deck_num
    FROM User
        LEFT JOIN Deck ON User.user_id = Deck.user_id
    WHERE username LIKE :search_string
    GROUP BY User.user_id;
    LIMIT 10
SQL);
$query->bindValue(":search_string", "%" . $_GET["search_string"] . "%");
$results = $query->execute();
if ($results->fetchArray()) : ?>
    <h2>Users</h2>

    <ul class="grid-list">
        <?php $results->reset();
        while ($user = $results->fetchArray()) {
            echo user_card($user);
        } ?>
    </ul>
<?php endif;

// Get a max of 10 decks that mach the search string
$query = $db->prepare(<<<SQL
    SELECT deck_id,
        title,
        plays,
        username,
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
    LIMIT 10
SQL);
$query->bindValue(":search_string", "%" . $_GET["search_string"] . "%");
$results = $query->execute();

// Check if result set is empty
if ($results->fetchArray()) : ?>
    <h2>Users</h2>

    <ul class="grid-list">
        <?php
        $results->reset();
        while ($deck = $results->fetchArray()) {
            echo deck_card($deck);
        } ?>
    </ul>
<?php endif ?>