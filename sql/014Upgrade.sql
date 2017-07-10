-- execute this if version is <14 !

ALTER TABLE `event`
ADD COLUMN `is_visible` TINYINT(1) NOT NULL DEFAULT 0;
-- DEFAULT 0: new events will be invisible
UPDATE `event` SET `is_visible` = 1;
-- UPDATE 1: existing events must stay visible

UPDATE info set version = 14;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
