DROP TABLE IF EXISTS teams;
CREATE TABLE teams (
    team_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    the_secret INTEGER
);

DROP TABLE IF EXISTS people;
CREATE TABLE people (
    person_id INTEGER PRIMARY KEY AUTOINCREMENT,
    person_name TEXT NOT NULL,
    email TEXT,
    team_id INTEGER,
    FOREIGN KEY(team_id) REFERENCES teams(team_id)
    --    ON UPDATE CASCADE
    --    ON DELETE SET NULL
);

DROP TABLE IF EXISTS votes;
CREATE TABLE votes (
    team_id INTEGER NOT NULL,
    theme_id INTEGER NOT NULL,
    ranking INTEGER NOT NULL,
    FOREIGN KEY(team_id) REFERENCES teams(team_id),
    FOREIGN KEY(theme_id) REFERENCES themes(theme_id),
    PRIMARY KEY(team_id, ranking)
);

DROP TABLE IF EXISTS themes;
CREATE TABLE themes (
    theme_id INTEGER PRIMARY KEY AUTOINCREMENT,
    theme TEXT
);

DROP TABLE IF EXISTS awards;
CREATE TABLE awards (
    award_id INTEGER PRIMARY KEY AUTOINCREMENT,
    award TEXT,
    team_id,
    FOREIGN KEY(team_id) REFERENCES teams(team_id)
);

DROP TABLE IF EXISTS subm_categories;
CREATE TABLE subm_categories (
    category_id INTEGER PRIMARY KEY,
    category_name TEXT
);

DROP TABLE IF EXISTS subm_questions;
CREATE TABLE subm_questions (
    question_id INTEGER PRIMARY KEY,
    question TEXT,
    question_category INTEGER,
    FOREIGN KEY(question_category) REFERENCES subm_categories(category_id)
);

DROP TABLE IF EXISTS subm_answers;
CREATE TABLE subm_answers (
    answer TEXT,
    question_id INTEGER,
    team_id INTEGER,
    PRIMARY KEY(question_id, team_id),
    FOREIGN KEY(question_id) REFERENCES subm_questions(question_id),
    FOREIGN KEY(team_id) REFERENCES teams(team_id)
);

INSERT INTO teams (name, the_secret)
VALUES 
("ed", 1234),
("moni", 4321),
("edrani", 5555);

INSERT INTO themes (theme) 
VALUES
("Diverge/Diversion"),
("Futility"),
("The Trail"),
("3 Rules"),
("Night is Coming");

INSERT INTO subm_categories (category_id, category_name) 
VALUES
(1, "text"),
(2, "textarea"),
(3, "image"),
(4, "file");


INSERT INTO subm_questions (question, question_category) 
VALUES
("Name", 1),
("Description", 2),
("How To Play", 2),
("Game URL", 1),
("Main Picture", 3),
("Picture 1", 3),
("Picture 2", 3),
("Submission zip", 4);
