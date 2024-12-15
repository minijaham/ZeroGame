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