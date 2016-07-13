-- execute this if version is <9 !
ALTER TABLE user ADD COLUMN is_regular tinyint(1) NOT NULL DEFAULT 0;

UPDATE user
SET is_regular=1
WHERE LOWER(email) IN (
    SELECT LOWER(mail) COLLATE utf8_unicode_ci FROM legacy_user
)

UPDATE info set version = 9;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
