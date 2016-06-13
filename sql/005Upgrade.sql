-- execute this if version is <5 !
ALTER TABLE commitment MODIFY COLUMN possible_start varchar(1000) NULL;

UPDATE info set version = 5;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
