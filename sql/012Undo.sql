USE clanx;

SELECT version from info;
-- execute this if version is =12 !

-- -----------------------------------------------
SELECT 'DROP VIEW `event_question_stat`' AS next_step;
DROP VIEW `event_question_stat`;

-- -----------------------------------------------
SELECT 'DROP TABLE `answer`' AS next_step;
DROP TABLE `answer`;

-- -----------------------------------------------
SELECT 'DROP TABLE `question`' AS next_step;
DROP TABLE `question`;

-- -----------------------------------------------
SELECT 'update db version' AS next_step;
UPDATE info set version = 11;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
