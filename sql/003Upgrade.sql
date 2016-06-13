-- execute this if version is <3 !
ALTER TABLE event ADD COLUMN description varchar(2000) NULL;

UPDATE info set version = 3;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
