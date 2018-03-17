-- execute this if version is <17 !

-- with an update of FOS User Bundle, salt may now be null, if bcrypt is used.

ALTER TABLE `user` CHANGE `salt` `salt` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
UPDATE user set salt = null where salt = '';

-- with an update of FOS User Bundle, this columns must have a default value.
ALTER TABLE `user` CHANGE `locked` `locked` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `user` CHANGE `expired` `expired` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `user` CHANGE `credentials_expired` `credentials_expired` TINYINT(1) NOT NULL DEFAULT '0';

UPDATE info set version = 17;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
