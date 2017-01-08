-- execute this if version is <11 !
ALTER TABLE user ADD is_association_member tinyint(1) NOT NULL DEFAULT 0;
-- event is a keyword on mysql.
ALTER TABLE `event` ADD is_for_association_members tinyint(1) NOT NULL DEFAULT 0;

UPDATE info set version = 11;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
