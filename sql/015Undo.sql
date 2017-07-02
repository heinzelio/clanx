-- execute this if version is =14 !
DROP TABLE `setting`;

UPDATE info set version = 14;

SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
