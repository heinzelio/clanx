-- execute this if version is <10 !
ALTER TABLE companion ADD COLUMN remark varchar(1000) DEFAULT NULL;

UPDATE info set version = 10;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
