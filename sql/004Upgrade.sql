-- execute this if version is <4 !
ALTER TABLE commitment MODIFY possible_start varchar(200);

UPDATE info set version = 4;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
