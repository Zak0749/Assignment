WITH EventsWithGaps AS (
    SELECT timestamp,
        julianday(
            date(
                LAG (timestamp, -1, julianday("now") + 1) OVER (
                    ORDER BY timestamp ASC
                )
            )
        ) - julianday(date(timestamp)) as untilNext
    FROM Events
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
LIMIT 1;