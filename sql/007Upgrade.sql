-- execute this if version is <7 !
ALTER TABLE event ADD COLUMN locked tinyint(1) NOT NULL DEFAULT 0;

UPDATE info set version = 7;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
