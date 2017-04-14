-- execute this if version is <13 !

ALTER TABLE `user`
ADD COLUMN `is_protected` TINYINT(1) NOT NULL DEFAULT 0;

UPDATE info set version = 13;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
