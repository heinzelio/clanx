-- execute this if version is <15 !

CREATE TABLE `setting` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `can_register` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
);

INSERT INTO `setting` (`can_register`) VALUES (1);

UPDATE info set version = 15;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
