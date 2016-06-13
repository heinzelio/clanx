-- execute this if version is <2 !
ALTER TABLE commitment ADD COLUMN need_train_ticket tinyint(1) NOT NULL DEFAULT 0;

UPDATE info set version = 2;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
