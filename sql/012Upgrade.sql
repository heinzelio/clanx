USE clanx;

SELECT version from info;
-- execute this if version is <12 !

-- -----------------------------------------------
SELECT 'CREATE TABLE `question`' AS next_step;
CREATE TABLE `question` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `event_id` int(11) NOT NULL,
    `text` varchar(1000) NOT NULL,
    `hint` varchar(1000) DEFAULT NULL,
    `type` varchar(1) NOT NULL DEFAULT 'T', -- T=Text, F=Flag, S=Selection
    `data` varchar(2000) DEFAULT NULL, -- May be "choices" or "default" or other type specific stuff
    `optional` tinyint(1) NOT NULL DEFAULT '0',
	`aggregate` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`) ,
    KEY `event_key` (`event_id`),
    CONSTRAINT `event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
 );

-- -----------------------------------------------
SELECT 'CREATE TABLE `answer`' AS next_step;
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

-- -----------------------------------------------
SELECT 'CREATE VIEW `event_question_stat`' AS next_step;
CREATE 
    ALGORITHM = UNDEFINED
VIEW clanx.event_question_stat 
AS
	SELECT q.event_id, q.text, a.answer, count('x') AS `count`
	FROM question q
	INNER JOIN answer a
	ON q.id = a.question_id
	WHERE q.aggregate=1
	AND q.event_id = 1
	GROUP BY q.event_id, q.text, a.answer
	ORDER BY q.event_id, q.text, a.answer
;


-- -----------------------------------------------
SELECT 'transform column "commitment.remark"' AS next_step;
SET @text = 'Bemerkung / Wunsch';
SET @optional = 1;
SET @aggregate = 0;

INSERT INTO question (event_id, `text`, optional, aggregate)
SELECT id, @text, @optional, @aggregate from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT c.remark, c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
WHERE q.text LIKE @text AND c.remark IS NOT NULL;

-- -----------------------------------------------
SELECT 'transform column "commitment.possible_start"' AS next_step;
SET @text = 'Ich helfe an folgenden Tagen';
SET @hint = 'bitte auch Zeit angeben';
SET @aggregate = 0;

INSERT INTO question (event_id, `text`, hint, aggregate)
SELECT id, @text, @hint, @aggregate from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT c.possible_start, c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
WHERE q.text LIKE @text AND c.possible_start IS NOT NULL;

-- -----------------------------------------------
SELECT 'transform column "commitment.shirt_size"' AS next_step;
SET @text = 'TShirt Grösse';
SET @hint = 'H = Herrenschnitt, D = Damenschnitt';
SET @type = 'S';
SET @data = '{"choices":["H-XS","H-S","H-M","H-L","H-XL","H-XXL","D-XS","D-S","D-M","D-L","D-XL","D-XXL"]}';
SET @aggregate = 1;

INSERT INTO question (event_id, `text`, hint, type, data, aggregate)
SELECT id, @text, @hint, @type, @data, @aggregate from event;

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

-- -----------------------------------------------
SELECT 'transform column "commitment.need_train_ticket"' AS next_step;
SET @text = 'Ich brauche ein Zugbillet';
SET @type = 'F';
SET @optional = 0;
SET @aggregate = 1;

INSERT INTO question (event_id, `text`, type, optional, aggregate)
SELECT id, @text, @type, @optional, @aggregate from event;

INSERT INTO answer (answer, commitment_id, question_id)
SELECT c.need_train_ticket, c.id, q.id
FROM question q
INNER JOIN event e ON q.event_id = e.id
INNER JOIN commitment c ON e.id = c.event_id
WHERE q.text LIKE @text;


-- -----------------------------------------------
SELECT 'update db version' AS next_step;
UPDATE info set version = 12;
SELECT version from info;

-- The "info" table with the "version" column is a nice thought.
-- But without the possibility of using flow control statements
-- (like IF) in the batch mode, it makes absolutely no sense :(
