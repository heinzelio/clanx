Whenever you create a new table, make sure you set the
character set to utf8mb4
and the collation to utf8mb4_general_ci

Like this:
    CREATE TABLE answer
    (id INT NOT NULL, value VARCHAR(1000) NULL)
    DEFAULT CHARACTER SET utf8mb4 collate utf8mb4_general_ci
