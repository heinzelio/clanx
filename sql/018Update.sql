-- execute this if version is <18 !

-- first fix the collations/CHARSET
-- https://github.com/chriglburri/clanx/issues/86
--

-- --> !!! SET your DATABASE NAME HERE !!!
--             ↓↓↓↓↓
ALTER DATABASE MY_DB_NAME_PLEASE_CHANGE_ME CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
--             ↑↑↑↑↑
ALTER TABLE `answer` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `commitment` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `companion` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `department` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `duty` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `event` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `info` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `legacy_user` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `question` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `setting` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;
ALTER TABLE `shift` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;

-- username_canonical and email_canonical have a uniqe index.
-- On hostpoint, they can not be longer than 767 bytes.
-- That are 191 utf8 chars, but we take 180. It's a nice number.
ALTER TABLE `user` CHANGE `username` `username` VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `user` CHANGE `username_canonical` `username_canonical` VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `user` CHANGE `email` `email` VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `user` CHANGE `email_canonical` `email_canonical` VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `user` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migration_versions` (`version`) VALUES ('20180217190022');


-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
