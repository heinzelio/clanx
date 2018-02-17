-- execute this if version is <18 !

-- first fix the collations/CHARSET
-- https://github.com/chriglburri/clanx/issues/86
--

-- --> !!! SET your DATABASE NAME HERE !!!
--             ↓↓↓↓↓
ALTER DATABASE clanx CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
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
ALTER TABLE `user` CONVERT TO CHARACTER SET utf8mb4 collate utf8mb4_general_ci;

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migration_versions` (`version`) VALUES ('20180217190022');


-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
