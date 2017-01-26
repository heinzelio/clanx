-- execute this if version is <12 !

CREATE TABLE `question` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `event_id` int(11) NOT NULL,
    `text` varchar(1000) NOT NULL,
    `hint` varchar(1000) DEFAULT NULL,
    `type` varchar(1) NOT NULL DEFAULT 'T', -- T=Text, F=Flag, S=Selection
    `data` varchar(2000) DEFAULT NULL, -- May be "choices" or other type specific stuff
    `optional` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`) ,
    KEY `event_key` (`event_id`),
    CONSTRAINT `event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
 );

CREATE TABLE `answer` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `question_id` int(11) NOT NULL,
    `commitment_id` int(11) NOT NULL,
    `answer` varchar(1000),
    PRIMARY KEY (`id`) ,
    KEY `question_key` (`question_id`),
    KEY `commitment_key` (`commitment_id`),
    CONSTRAINT `question_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
    CONSTRAINT `commitment_ibfk_1` FOREIGN KEY (`commitment_id`) REFERENCES `commitment` (`id`)
);


-- transform column "remark"
SET @text = 'Bemerkung / Wunsch';
SET @optional = 1;

INSERT INTO question (event_id, `text`, optional)
SELECT id, @text, @optional from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT c.remark, c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
WHERE q.text LIKE @text AND c.remark IS NOT NULL;

-- transform column "possible_start"
SET @text = 'Ich helfe an folgenden Tagen';
SET @hint = 'bitte auch Zeit angeben';
SET @optional = 1;

INSERT INTO question (event_id, `text`, hint, optional)
SELECT id, @text, @hint, @optional from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT c.possible_start, c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
WHERE q.text LIKE @text AND c.possible_start IS NOT NULL;

-- transform column "shirt_size"
SET @text = 'TShirt Grösse';
SET @hint = 'H = Herrenschnitt, D = Damenschnitt';
SET @data = '{"choices":["H-XS","H-S","H-M","H-L","H-XL","H-XXL","D-XS","D-S","D-M","D-L","D-XL","D-XXL"]}';
SET @type = 'S';
SET @optional = 1;

INSERT INTO question (event_id, `text`, hint, optional, type, data)
SELECT id, @text, @hint, @optional, @type, @data from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT CONCAT('H-', c.shirt_size), c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
INNER JOIN user u ON c.user_id = u.id
WHERE q.text LIKE @text AND c.possible_start IS NOT NULL
  AND (u.gender LIKE 'M' OR u.gender IS NULL OR u.gender LIKE '');

INSERT INTO answer (answer, commitment_id, question_id)
SELECT CONCAT('D-', c.shirt_size), c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
INNER JOIN user u ON c.user_id = u.id
WHERE q.text LIKE @text AND c.possible_start IS NOT NULL AND u.gender LIKE 'F';

-- transform column "need_train_ticket"
SET @text = 'Ich brauche ein Zugbillet';
SET @type = 'F';
SET @optional = 0;

INSERT INTO question (event_id, `text`, optional, type)
SELECT id, @text, @optional, @type from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT c.need_train_ticket, c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
WHERE q.text LIKE @text;


UPDATE info set version = 12;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
