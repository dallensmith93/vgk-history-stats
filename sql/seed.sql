-- sql/seed.sql
-- Schema
DROP TABLE IF EXISTS series;
DROP TABLE IF EXISTS seasons;

CREATE TABLE seasons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  start_year INT NOT NULL,
  end_year INT NOT NULL,
  season_label VARCHAR(9) NOT NULL,         -- e.g., 2017-18
  division_finish VARCHAR(20),              -- e.g., '1st', '2nd', '3rd', '4th'
  made_playoffs TINYINT(1) NOT NULL DEFAULT 0,
  made_scf TINYINT(1) NOT NULL DEFAULT 0,
  champions TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE series (
  id INT AUTO_INCREMENT PRIMARY KEY,
  season_id INT NOT NULL,
  round_label VARCHAR(32) NOT NULL,         -- 'First Round', 'Second Round', 'Conference Final', 'Stanley Cup Final', etc.
  opponent VARCHAR(64) NOT NULL,
  result VARCHAR(16) NOT NULL,              -- e.g., '4-1', '3-4'
  outcome ENUM('Won','Lost') NOT NULL,
  FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data current through 2024–25
INSERT INTO seasons (start_year, end_year, season_label, division_finish, made_playoffs, made_scf, champions) VALUES
(2017, 2018, '2017-18', '1st', 1, 1, 0),
(2018, 2019, '2018-19', '3rd', 1, 0, 0),
(2019, 2020, '2019-20', '1st', 1, 0, 0),
(2020, 2021, '2020-21', '2nd', 1, 0, 0),
(2021, 2022, '2021-22', '4th', 0, 0, 0),  -- missed playoffs
(2022, 2023, '2022-23', '1st', 1, 1, 1), -- Cup champs
(2023, 2024, '2023-24', '4th', 1, 0, 0),
(2024, 2025, '2024-25', '1st', 1, 0, 0);

-- Optional: series detail for select seasons

-- 2017-18 playoff run
SET @sid = (SELECT id FROM seasons WHERE season_label = '2017-18');
INSERT INTO series (season_id, round_label, opponent, result, outcome) VALUES
(@sid, 'First Round', 'Los Angeles Kings', '4-0', 'Won'),
(@sid, 'Second Round', 'San Jose Sharks', '4-2', 'Won'),
(@sid, 'Conference Final', 'Winnipeg Jets', '4-1', 'Won'),
(@sid, 'Stanley Cup Final', 'Washington Capitals', '1-4', 'Lost');

-- 2019-20 playoff run
SET @sid = (SELECT id FROM seasons WHERE season_label = '2019-20');
INSERT INTO series (season_id, round_label, opponent, result, outcome) VALUES
(@sid, 'First Round', 'Chicago Blackhawks', '4-1', 'Won'),
(@sid, 'Second Round', 'Vancouver Canucks', '4-3', 'Won'),
(@sid, 'Conference Final', 'Dallas Stars', '1-4', 'Lost');

-- 2020-21 playoff run
SET @sid = (SELECT id FROM seasons WHERE season_label = '2020-21');
INSERT INTO series (season_id, round_label, opponent, result, outcome) VALUES
(@sid, 'First Round', 'Minnesota Wild', '4-3', 'Won'),
(@sid, 'Second Round', 'Colorado Avalanche', '4-2', 'Won'),
(@sid, 'Stanley Cup Semifinal', 'Montreal Canadiens', '2-4', 'Lost');

-- 2022-23 Cup-winning run
SET @sid = (SELECT id FROM seasons WHERE season_label = '2022-23');
INSERT INTO series (season_id, round_label, opponent, result, outcome) VALUES
(@sid, 'First Round', 'Winnipeg Jets', '4-1', 'Won'),
(@sid, 'Second Round', 'Edmonton Oilers', '4-2', 'Won'),
(@sid, 'Conference Final', 'Dallas Stars', '4-2', 'Won'),
(@sid, 'Stanley Cup Final', 'Florida Panthers', '4-1', 'Won');

-- 2023-24
SET @sid = (SELECT id FROM seasons WHERE season_label = '2023-24');
INSERT INTO series (season_id, round_label, opponent, result, outcome) VALUES
(@sid, 'First Round', 'Dallas Stars', '3-4', 'Lost');

-- 2024-25
SET @sid = (SELECT id FROM seasons WHERE season_label = '2024-25');
INSERT INTO series (season_id, round_label, opponent, result, outcome) VALUES
(@sid, 'First Round', 'Minnesota Wild', '4-2', 'Won'),
(@sid, 'Second Round', 'Edmonton Oilers', '1-4', 'Lost');
