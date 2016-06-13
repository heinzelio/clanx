CREATE TABLE info (version int NOT NULL);
INSERT INTO info (version) VALUES (0);

-- execute this if version is <1 !
ALTER TABLE commitment ADD COLUMN possible_start datetime NULL;
ALTER TABLE commitment ADD COLUMN possible_end datetime NULL;
ALTER TABLE commitment ADD COLUMN shirt_size varchar(10) NULL;

UPDATE info set version = 1;


-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
