-- execute this if version is =17 !

UPDATE user set salt = '' where salt IS NULL;

ALTER TABLE `user` CHANGE `salt` `salt` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

UPDATE info set version = 16;

SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
