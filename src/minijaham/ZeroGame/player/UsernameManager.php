<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\player;

use pocketmine\Server;

use minijaham\ZeroGame\Loader;
use minijaham\ZeroGame\data\DataManager;
use minijaham\ZeroGame\data\database\Queries;

use Ramsey\Uuid\UuidInterface;

use InvalidArgumentException;

/**
 * Manages player username.
 * This is mostly for the player UUID to username lookup.
 */
final class UsernameManager extends DataManager
{
    /**
     * UsernameManager Constructor.
     * 
     * @param Loader $plugin Instance of the main Loader plugin.
     */
    public function __construct(private Loader $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Returns the DataListener instance for managing player-related events.
     * 
     * @return UsernameListener
     */
    protected function getListener() : UsernameListener
    {
        return new UsernameListener($this);
    }

    /**
     * Returns the SQL query string used to initialize player data management.
     * 
     * @return string SQL query for player data initialization.
     */
    protected function getInitQuery() : string
    {
        return Queries::PLAYER_USERNAME_INIT;
    }

    /**
     * Returns the SQL query string used to create a new player data entry.
     * 
     * @return string SQL query for creating player data.
     */
    protected function getCreateQuery() : string
    {
        return Queries::PLAYER_USERNAME_CREATE;
    }

    /**
     * Returns the SQL query string used to update player data.
     * 
     * @return string SQL query for updating player data.
     */
    protected function getUpdateQuery() : string
    {
        return Queries::PLAYER_USERNAME_UPDATE;
    }

    /**
     * Returns the SQL query string used to delete player data.
     * 
     * @return string SQL query for deleting player data.
     */
    protected function getDeleteQuery() : string
    {
        return Queries::PLAYER_USERNAME_DELETE;
    }

    /**
     * Returns the SQL query string used to select player data.
     * 
     * @return string SQL query for selecting player data.
     */
    protected function getSelectQuery() : string
    {
        return Queries::PLAYER_USERNAME_SELECT;
    }

    /**
     * Prepares arguments required for creating a new player data entry.
     * 
     * @param UuidInterface $uuid The unique identifier of the player.
     * @return array The arguments for the `create` SQL query.
     * @throws InvalidArgumentException If the player cannot be found for the given UUID.
     */
    protected function prepare_create_arguments(UuidInterface $uuid) : array
    {
        $player = Server::getInstance()->getPlayerByUUID($uuid);
        if ($player === null) {
            throw new InvalidArgumentException("Player not found for UUID: " . $uuid->toString());
        }

        return [
            "uuid" => $uuid->toString(),
            "username" => $player->getName(),
        ];
    }

    /**
     * Prepares arguments required for updating an existing player data entry.
     * 
     * @param UuidInterface $uuid The unique identifier of the player.
     * @return array The arguments for the `update` SQL query.
     * @throws InvalidArgumentException If the player data cannot be found in memory.
     */
    protected function prepare_update_arguments(UuidInterface $uuid) : array
    {
        return [
            "uuid" => $uuid->toString(),
            "username" => $this->data[$uuid->toString()],
        ];
    }

    /**
     * Formats raw data retrieved from the database before storing it in memory.
     * 
     * @param array $data The raw data retrieved from the database.
     * @return string The formatted data
     */
    protected function format_data(array $data) : string
    {
        return (string) $data["username"];
    }
}