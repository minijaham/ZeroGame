-- #!sqlite

-- # { player_username
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_username (
    uuid VARCHAR(36) PRIMARY KEY,
    username VARCHAR(16)
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT username FROM player_username WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :username string
INSERT OR REPLACE INTO player_username(uuid, username)
VALUES (:uuid, :username);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :username string
UPDATE player_username SET username = :username WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_username WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_economy
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_economy (
    uuid VARCHAR(36) PRIMARY KEY,
    balance INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT balance FROM player_economy WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :balance int
INSERT OR REPLACE INTO player_economy(uuid, balance)
VALUES (:uuid, :balance);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :balance int
UPDATE player_economy SET balance = :balance WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_economy WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_level
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_level (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT,
    status_points INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_level WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
-- #      :status_points int
INSERT OR REPLACE INTO player_level(uuid, level, experience, status_points)
VALUES (:uuid, :level, :experience, :status_points);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
-- #      :status_points int
UPDATE player_level SET level = :level, experience = :experience, status_points = :status_points WHERE uuid = :uuid;
-- #  }

-- #  { add_level
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET level = level + :amount WHERE uuid = :uuid;
-- #  }

-- #  { remove_level
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET level = GREATEST(level - :amount, 0) WHERE uuid = :uuid;
-- #  }

-- #  { set_level
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET level = :amount WHERE uuid = :uuid;
-- #  }

-- #  { add_experience
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET experience = experience + :amount WHERE uuid = :uuid;
-- #  }

-- #  { remove_experience
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET experience = GREATEST(experience - :amount, 0) WHERE uuid = :uuid;
-- #  }

-- #  { set_experience
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET experience = :amount WHERE uuid = :uuid;
-- #  }

-- #  { add_status_points
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET status_points = status_points + :amount WHERE uuid = :uuid;
-- #  }

-- #  { remove_status_points
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET status_points = GREATEST(status_points - :amount, 0) WHERE uuid = :uuid;
-- #  }

-- #  { set_status_points
-- #      :uuid string
-- #      :amount int
UPDATE player_level SET status_points = :amount WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_level WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_status_atk
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_status_atk (
    uuid VARCHAR(36) PRIMARY KEY,
    points INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT points FROM player_status_atk WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :points int
INSERT OR REPLACE INTO player_status_atk(uuid, points)
VALUES (:uuid, :points);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :points int
UPDATE player_status_atk SET points = :points WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_status_atk WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_status_def
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_status_def (
    uuid VARCHAR(36) PRIMARY KEY,
    points INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT points FROM player_status_def WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :points int
INSERT OR REPLACE INTO player_status_def(uuid, points)
VALUES (:uuid, :points);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :points int
UPDATE player_status_def SET points = :points WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_status_def WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_status_vit
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_status_vit (
    uuid VARCHAR(36) PRIMARY KEY,
    points INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT points FROM player_status_vit WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :points int
INSERT OR REPLACE INTO player_status_vit(uuid, points)
VALUES (:uuid, :points);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :points int
UPDATE player_status_vit SET points = :points WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_status_vit WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession (
    uuid VARCHAR(36) PRIMARY KEY,
    primary_profession_id INT NULL,
    secondary_profession_id INT NULL,
    tertiary_profession_id INT NULL
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
INSERT OR REPLACE INTO player_profession(uuid, primary_profession_id, secondary_profession_id, tertiary_profession_id)
VALUES (:uuid, NULL, NULL, NULL); -- player profession id will first be null.
-- #  }

-- #  { update
-- #      :uuid string
-- #      :primary_profession_id ?int
-- #      :secondary_profession_id ?int
-- #      :tertiary_profession_id ?int
UPDATE player_profession
SET
    primary_profession_id   = :primary_profession_id,
    secondary_profession_id = :secondary_profession_id,
    tertiary_profession_id  = :tertiary_profession_id
WHERE uuid = :uuid;
-- #  }

-- #  { reset
-- #      :uuid string
UPDATE player_profession
SET
    primary_profession_id = NULL,
    secondary_profession_id = NULL,
    tertiary_profession_id = NULL
WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession_combat
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession_combat (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession_combat WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
INSERT OR REPLACE INTO player_profession_combat(uuid, level, experience)
VALUES (:uuid, :level, :experience);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
UPDATE player_profession_combat SET level = :level, experience = :experience WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession_combat WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession_cooking
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession_cooking (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession_cooking WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
INSERT OR REPLACE INTO player_profession_cooking(uuid, level, experience)
VALUES (:uuid, :level, :experience);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
UPDATE player_profession_cooking SET level = :level, experience = :experience WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession_cooking WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession_crafting
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession_crafting (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession_crafting WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
INSERT OR REPLACE INTO player_profession_crafting(uuid, level, experience)
VALUES (:uuid, :level, :experience);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
UPDATE player_profession_crafting SET level = :level, experience = :experience WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession_crafting WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession_farming
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession_farming (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession_farming WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
INSERT OR REPLACE INTO player_profession_farming(uuid, level, experience)
VALUES (:uuid, :level, :experience);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
UPDATE player_profession_farming SET level = :level, experience = :experience WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession_farming WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession_fishing
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession_fishing (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession_fishing WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
INSERT OR REPLACE INTO player_profession_fishing(uuid, level, experience)
VALUES (:uuid, :level, :experience);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
UPDATE player_profession_fishing SET level = :level, experience = :experience WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession_fishing WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_profession_mining
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_profession_mining (
    uuid VARCHAR(36) PRIMARY KEY,
    level INT,
    experience INT
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_profession_mining WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :level int
-- #      :experience int
INSERT OR REPLACE INTO player_profession_mining(uuid, level, experience)
VALUES (:uuid, :level, :experience);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :level int
-- #      :experience int
UPDATE player_profession_mining SET level = :level, experience = :experience WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_profession_mining WHERE uuid = :uuid;
-- #  }
-- # }




-- # { player_trinket
-- #  { initialize
CREATE TABLE IF NOT EXISTS player_trinket (
    uuid VARCHAR(36) PRIMARY KEY,
    ring_id INT NULL,
    ring_level INT NULL,
    necklace_id INT NULL,
    necklace_level INT NULL,
    rune_1_id INT NULL,
    rune_1_level INT NULL,
    rune_2_id INT NULL,
    rune_2_level INT NULL,
    rune_3_id INT NULL,
    rune_3_level INT NULL
);
-- #  }

-- #  { select
-- #      :uuid string
SELECT * FROM player_trinket WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
INSERT OR REPLACE INTO player_trinket(
    uuid,
    ring_id, ring_level,
    necklace_id, necklace_level,
    rune_1_id, rune_1_level,
    rune_2_id, rune_2_level,
    rune_3_id, rune_3_level
)
VALUES (
    :uuid,
    NULL, NULL,
    NULL, NULL,
    NULL, NULL,
    NULL, NULL,
    NULL, NULL
);
-- #  }

-- #  { update
-- #      :uuid string
-- #      :ring_id ?int
-- #      :ring_level ?int
-- #      :necklace_id ?int
-- #      :necklace_level ?int
-- #      :rune_1_id ?int
-- #      :rune_1_level ?int
-- #      :rune_2_id ?int
-- #      :rune_2_level ?int
-- #      :rune_3_id ?int
-- #      :rune_3_level ?int
UPDATE player_trinket
SET
    ring_id = :ring_id,
    ring_level = :ring_level,
    necklace_id = :necklace_id,
    necklace_level = :necklace_level,
    rune_1_id = :rune_1_id,
    rune_1_level = :rune_1_level,
    rune_2_id = :rune_2_id,
    rune_2_level = :rune_2_level,
    rune_3_id = :rune_3_id,
    rune_3_level = :rune_3_level
WHERE uuid = :uuid;
-- #  }

-- #  { reset
-- #      :uuid string
UPDATE player_trinket
SET
    ring_id = NULL,
    ring_level = NULL,
    necklace_id = NULL,
    necklace_level = NULL,
    rune_1_id = NULL,
    rune_1_level = NULL,
    rune_2_id = NULL,
    rune_2_level = NULL,
    rune_3_id = NULL,
    rune_3_level = NULL
WHERE uuid = :uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM player_trinket WHERE uuid = :uuid;
-- #  }
-- # }

