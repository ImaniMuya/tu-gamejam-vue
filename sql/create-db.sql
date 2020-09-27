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
    team_id INTEGER,
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
    placeholder TEXT,
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


DROP TABLE IF EXISTS admin_properties;
CREATE TABLE admin_properties (
    name TEXT,
    value TEXT
);

DROP TABLE IF EXISTS admin_logins;
CREATE TABLE admin_logins (
    time TEXT,
    failed BOOLEAN
);

INSERT INTO admin_properties (name, value)
VALUES 
("password_hash", ""),
("current_session", ""),
("session_start", ""),
("one_time_pass", "");

INSERT INTO teams (name, the_secret)
VALUES 
("ed", 1234),
("moni", 4321),
("edrani", 5555);

INSERT INTO people (person_name, email, team_id)
VALUES 
("Joey", "how@you.doing" , 1),
("Monica", "ok@friends.com" , 2),
("Ross", "hi@friends.com" , 3);

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


INSERT INTO subm_questions (question, question_category, placeholder) 
VALUES
("Name", 1, NULL),
("Description", 2, "No room left for improvement"),
("How To Play", 2, "arrow keys to move, space bar to be awesome"),
("Game URL", 1, "https://bestgameever.link"),
("Main Picture", 3, NULL),
("Picture 1", 3, NULL),
("Picture 2", 3, NULL),
("Submission zip", 4, NULL);
