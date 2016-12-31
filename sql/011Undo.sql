-- execute this if version is <11 !
ALTER TABLE `user` DROP `is_association_member`;
ALTER TABLE `event` DROP `is_for_association_members`;

UPDATE info set version = 10;

SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
