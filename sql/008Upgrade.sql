-- execute this if version is <8 !
CREATE TABLE `companion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `department_id` int(11) NOT NULL,
    `name` varchar(200) NOT NULL,
    `email` varchar(255),
    `phone` varchar(50),
    `is_regular` tinyint(1) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `department_key` (`department_id`),
    CONSTRAINT `companion_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
);

UPDATE info set version = 8;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
