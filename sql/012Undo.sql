-- execute this if version is =12 !
DROP TABLE `answer`;
DROP TABLE `question`;

UPDATE info set version = 11;

SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
