-- execute this if version is <11 !
ALTER TABLE `user` DROP `is_protected`;

UPDATE info set version = 12;

SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
