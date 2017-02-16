-- Drop all tables
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS rank;
DROP TABLE IF EXISTS ship;
DROP TABLE IF EXISTS series;
DROP TABLE IF EXISTS star_character;
DROP TABLE IF EXISTS ship_character;
DROP TABLE IF EXISTS series_character;
SET FOREIGN_KEY_CHECKS=1;

-- create all tables
-- Holds all ship information
CREATE TABLE ship (
	ship_ID int  AUTO_INCREMENT NOT NULL,
	ship_name  VARCHAR(255) NOT NULL ,
	PRIMARY KEY (ship_ID)
)ENGINE=InnoDB;

-- holds ranking information (admiral, captain, etc.)
CREATE TABLE rank (
	rank_id int AUTO_INCREMENT NOT NULL,
	rank_name VARCHAR(255),
	PRIMARY KEY (rank_id)
)ENGINE=InnoDB;

-- main table that everything is connected to. 
CREATE TABLE star_character (
	character_ID int AUTO_INCREMENT NOT NULL,
	first_name VARCHAR(255),
	last_name VARCHAR(255) ,
	start_rank int,
	end_rank int,
	birth_place VARCHAR(255),
	PRIMARY KEY (character_ID),
	FOREIGN KEY (start_rank) REFERENCES rank(rank_id),
	FOREIGN KEY (end_rank) REFERENCES rank(rank_id)
)ENGINE=InnoDB DEFAULT CHARSET = utf8;

-- connecting table for characters and which ships they have served on
CREATE TABLE ship_character (
	id int AUTO_INCREMENT NOT NULL,
	character_ID int,
	ship_ID int,
	PRIMARY KEY (id),
	FOREIGN KEY (character_ID) REFERENCES star_character(character_ID),
	FOREIGN KEY (ship_ID) REFERENCES ship(ship_ID) 
)ENGINE=InnoDB DEFAULT CHARSET = utf8;

-- holds series information (id and name)
CREATE TABLE series (
	series_ID int AUTO_INCREMENT NOT NULL,
	series_name VARCHAR(255) NOT NULL,
	PRIMARY KEY (series_ID)
)ENGINE=InnoDB DEFAULT CHARSET = utf8;

-- connecting table for characters and the series they have been on
CREATE TABLE series_character (
	id int AUTO_INCREMENT NOT NULL,
	series_ID int,
	character_ID int,
	PRIMARY KEY (id),
	FOREIGN KEY (series_ID) REFERENCES series(series_ID),
	FOREIGN KEY (character_ID) REFERENCES star_character(character_ID)  
)ENGINE=InnoDB DEFAULT CHARSET = utf8;

-- fill tables
INSERT INTO ship (ship_name) VALUES 
('U.S.S. Enterprise NCC-1701-A'),('U.S.S. Enterprise NCC-1701-D'),( 'U.S.S. Enterprise NCC-1701-E'),
('U.S.S. Stargazer'),('U.S.S. Voyager'),('Deep Space Nine'),('U.S.S. Defiant'),('U.S.S. Farragut'),('U.S.S. Sutherland');

INSERT INTO rank (rank_name) VALUES
('Lieutenant'), ('Ensign'),('First Officer'),('Lieutenant j.g.'),('Captain'),('Emergency Medical Program'),('Lieutenant Commander'),('Admiral'),('Commander'),('Second Officer'),('Chief Medical Officer');

INSERT INTO star_character (first_name, last_name, start_rank, end_rank, birth_place) VALUES
('James', 'Kirk', (SELECT rank_id from rank WHERE rank_name  = 'Lieutenant'),(SELECT rank_id from rank WHERE rank_name  = 'Admiral'), 'Earth' ),
('MR.', 'Spock', (SELECT rank_id from rank WHERE rank_name  = 'Ensign'),(SELECT rank_id from rank WHERE rank_name  = 'Captain'), 'Vulcan' ),
('Jean-Luc', 'Picard', (SELECT rank_id from rank WHERE rank_name  = 'First Officer'),(SELECT rank_id from rank WHERE rank_name  = 'Commander'), 'Earth'),
('MR.', 'Warf', (SELECT rank_id from rank WHERE rank_name  = 'Lieutenant j.g.'),(SELECT rank_id from rank WHERE rank_name  = 'Lieutenant Commander'), "Qo'noS"),
('MR.', 'DATA', (SELECT rank_id from rank WHERE rank_name  = 'Lieutenant'),(SELECT rank_id from rank WHERE rank_name  = 'Second Officer'), 'Omicron Theta'),
('Kathryn', 'Janeway', (SELECT rank_id from rank WHERE rank_name  = 'Captain'),(SELECT rank_id from rank WHERE rank_name  = 'Admiral'), 'Earth' ),
('MR.', 'Doctor', (SELECT rank_id from rank WHERE rank_name  = 'Emergency Medical Program'),(SELECT rank_id from rank WHERE rank_name  = 'Chief Medical Officer'), 'Jupiter Station Holo-Programming Center'),
('Benjamin', 'Sisko', (SELECT rank_id from rank WHERE rank_name  = 'Lieutenant Commander'),(SELECT rank_id from rank WHERE rank_name  = 'Captain'), 'Earth' ),
('Jadzia', 'Dax', (SELECT rank_id from rank WHERE rank_name  = 'Lieutenant'),(SELECT rank_id from rank WHERE rank_name  = 'Lieutenant Commander'), 'Trill');

INSERT INTO ship_character (character_ID, ship_ID) VALUES
((SELECT character_ID FROM star_character WHERE first_name = 'James' AND last_name = 'Kirk'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Farragut')),
((SELECT character_ID FROM star_character WHERE first_name = 'James' AND last_name = 'Kirk'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Enterprise NCC-1701-A')),
((SELECT character_ID FROM star_character WHERE last_name = 'Spock'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Enterprise NCC-1701-A')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jean-Luc' AND last_name = 'Picard'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Stargazer')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jean-Luc' AND last_name = 'Picard'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Enterprise NCC-1701-D')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jean-Luc' AND last_name = 'Picard'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Enterprise NCC-1701-E')),
((SELECT character_ID FROM star_character WHERE last_name = 'Warf'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Enterprise NCC-1701-D')),
((SELECT character_ID FROM star_character WHERE last_name = 'Warf'), (SELECT ship_ID FROM ship WHERE ship_name = 'Deep Space Nine')),
((SELECT character_ID FROM star_character WHERE last_name = 'Warf'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Defiant')),
((SELECT character_ID FROM star_character WHERE last_name = 'DATA'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Enterprise NCC-1701-D')),
((SELECT character_ID FROM star_character WHERE last_name = 'DATA'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Sutherland')),
((SELECT character_ID FROM star_character WHERE first_name = 'Kathryn' AND last_name = 'Janeway'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Voyager')),
((SELECT character_ID FROM star_character WHERE last_name = 'Doctor'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Voyager')),
((SELECT character_ID FROM star_character WHERE first_name = 'Benjamin' AND last_name = 'Sisko'), (SELECT ship_ID FROM ship WHERE ship_name = 'Deep Space Nine')),
((SELECT character_ID FROM star_character WHERE first_name = 'Benjamin' AND last_name = 'Sisko'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Defiant')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jadzia' AND last_name = 'Dax'), (SELECT ship_ID FROM ship WHERE ship_name = 'Deep Space Nine')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jadzia' AND last_name = 'Dax'), (SELECT ship_ID FROM ship WHERE ship_name = 'U.S.S. Defiant'));

INSERT INTO series (series_name) VALUES
('Star Trek'), ('Star Trek: The Next Generation'), ('Star Trek: Deep Space Nine'), ('Star Trek: Voyager');

INSERT INTO series_character ( character_ID, series_ID) VALUES
((SELECT character_ID FROM star_character WHERE first_name = 'James' AND last_name = 'Kirk'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek')),
((SELECT character_ID FROM star_character WHERE first_name = 'James' AND last_name = 'Kirk'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: The Next Generation')),
((SELECT character_ID FROM star_character WHERE last_name = 'Spock'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jean-Luc' AND last_name = 'Picard'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: The Next Generation')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jean-Luc' AND last_name = 'Picard'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: Deep Space Nine')),
((SELECT character_ID FROM star_character WHERE last_name = 'Warf'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: The Next Generation')),
((SELECT character_ID FROM star_character WHERE last_name = 'Warf'), (SELECT series_ID FROM series WHERE series_name = 'Deep Space Nine')),
((SELECT character_ID FROM star_character WHERE last_name = 'DATA'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: The Next Generation')),
((SELECT character_ID FROM star_character WHERE first_name = 'Kathryn' AND last_name = 'Janeway'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: Voyager')),
((SELECT character_ID FROM star_character WHERE last_name = 'Doctor'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: Voyager')),
((SELECT character_ID FROM star_character WHERE first_name = 'Benjamin' AND last_name = 'Sisko'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: Deep Space Nine')),
((SELECT character_ID FROM star_character WHERE first_name = 'Jadzia' AND last_name = 'Dax'), (SELECT series_ID FROM series WHERE series_name = 'Star Trek: Deep Space Nine'));



-- shows character information
SELECT 	first_name AS 'First Name', 
		last_name AS 'Last Name', 
		(select rank_name from rank where rank_id = start_rank) as 'Starting Rank',
		(select rank_name from rank where rank_id = end_rank) as 'Ending Rank',
		birth_place AS 'Birth Place'
		FROM star_character;

-- Shows character and ship interactions
SELECT 	star_character.first_name AS  'First Name', star_character.last_name AS  'Last Name', ship.ship_name AS  'Ship Name'
		FROM ship_character
		INNER JOIN star_character ON ship_character.character_ID = star_character.character_ID
		INNER JOIN ship ON ship.ship_ID = ship_character.ship_ID;

-- Shows series character interactions
SELECT 	star_character.character_ID, star_character.first_name AS  'First Name', star_character.last_name AS  'Last Name', series.series_name AS  'Series Name'
		FROM series_character
		INNER JOIN star_character ON star_character.character_ID = series_character.character_ID
		INNER JOIN series ON series_character.series_ID = series.series_ID
		ORDER BY star_character.character_ID;

DELETE FROM star_character WHERE character_ID = 1;
