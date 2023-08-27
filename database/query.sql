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