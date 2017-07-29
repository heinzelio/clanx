-- execute this if version is <16 !

-- just update the "is regular" flag for the new season

UPDATE user SET is_regular = 1 WHERE ID IN
(
    SELECT c.user_id
    FROM commitment c
    INNER JOIN event e
    ON c.event_id = e.id
    WHERE e.name LIKE 'CHANGE THIS !!!'
)

UPDATE info set version = 16;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
