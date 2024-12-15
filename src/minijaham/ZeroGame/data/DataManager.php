<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\data;

use pocketmine\Server;

use minijaham\ZeroGame\Loader;
use minijaham\ZeroGame\Manager;

use poggit\libasynql\DataConnector;

use SOFe\AwaitGenerator\Await;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use Generator;
use InvalidArgumentException;

/**
 * Abstract class representing a data manager for handling player-related data.
 * This class provides asynchronous methods for managing player data in memory
 * and synchronizing with the database.
 */
abstract class DataManager extends Manager
{
    /**
     * Stores online player data in memory, indexed by UUID.
     * 
     * @var array<string, mixed> $data [uuid => data]
     */
    protected array $data = [];

    /**
     * Tracks valid keys in memory, indexed by UUID.
     * 
     * This is to avoid O(n) of time complexity when checking if the data exists in $data.
     * Unfortunately, PHP's isset() function returns false if the associated value to
     * a key is null, which then we'll need to use array_key_exists—which has an O(n) time complexity.
     * 
     * @var array<string, bool> $keys [uuid => true]
     */
    protected array $keys = [];
    
    /**
     * DataManager Constructor.
     * 
     * @param Loader $plugin
     */
    protected function __construct(private Loader $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Initializes the data manager by executing a setup query.
     * Called during manager setup to ensure database structure and prepare initial data.
     * 
     * @return void
     */
    protected function init() : void
    {
        Await::f2c(function() {
            yield from $this->getDatabase()->asyncGeneric($this->getInitQuery());
        });

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this->getListener(), $this->getPlugin());
    }

    /**
     * Returns the DataListener instance that the manager will be using to handle
     * player data registration.
     * 
     * @return DataListener
     */
    abstract protected function getListener() : DataListener;

    /**
     * Returns the SQL query string used to initialize the data manager.
     * This query is executed during the initialization phase to ensure
     * the necessary database structure is prepared.
     * 
     * Subclasses must implement this method to provide the appropriate SQL query
     * for initializing their specific data.
     * 
     * @return string The SQL query used for initializing the data manager.
     */
    abstract protected function getInitQuery() : string;

    /**
     * Returns the SQL query string used to create a new data entry in the database.
     * This query is executed when the `create` method is called to insert new data.
     * 
     * Subclasses must implement this method to provide the appropriate SQL query
     * for creating new data specific to their domain.
     * 
     * @return string The SQL query for creating a new data entry.
     */
    abstract protected function getCreateQuery() : string;

    /**
     * Returns the SQL query string used to update an existing data entry in the database.
     * This query is executed when the `update` method is called to synchronize memory data with the database.
     * 
     * Subclasses must implement this method to provide the appropriate SQL query
     * for updating data specific to their domain.
     * 
     * @return string The SQL query for updating an existing data entry.
     */
    abstract protected function getUpdateQuery() : string;

    /**
     * Returns the SQL query string used to delete an existing data entry in the database.
     * This query is executed when the `delete` method is called to remove data permanently.
     * 
     * Subclasses must implement this method to provide the appropriate SQL query
     * for deleting data specific to their domain.
     * 
     * @return string The SQL query for deleting a data entry.
     */
    abstract protected function getDeleteQuery() : string;

    /**
     * Returns the SQL query string used to fetch data from the database.
     * This query is executed when retrieving data, such as during the `start` or `get_direct` method calls.
     * 
     * Subclasses must implement this method to provide the appropriate SQL query
     * for selecting data specific to their domain.
     * 
     * @return string The SQL query for selecting data from the database.
     */
    abstract protected function getSelectQuery() : string;

    /**
     * Prepares the arguments required for the SQL query used in the `create` method.
     * This method ensures that all necessary data is structured correctly
     * for insertion into the database.
     * 
     * Subclasses must implement this method to define the specific arguments
     * required for creating a new data entry in their domain.
     * 
     * @param UuidInterface $uuid The unique identifier of the entity being created.
     * @return array The arguments for the `create` SQL query, formatted as key-value pairs.
     */
    abstract protected function prepare_create_arguments(UuidInterface $uuid) : array;

    /**
     * Prepares the arguments required for the SQL query used in the `update` method.
     * This method ensures that all necessary data is structured correctly
     * for updating the database with the current memory state.
     * 
     * Subclasses must implement this method to define the specific arguments
     * required for updating a data entry in their domain.
     * 
     * @param UuidInterface $uuid The unique identifier of the entity being updated.
     * @return array The arguments for the `update` SQL query, formatted as key-value pairs.
     */
    abstract protected function prepare_update_arguments(UuidInterface $uuid) : array;

    /**
     * Formats raw data retrieved from the database before storing it in memory.
     * This method is useful for transforming or sanitizing data
     * (e.g., converting numeric strings to integers, handling nullable fields).
     * 
     * Subclasses can override this method to implement custom formatting logic.
     * 
     * @param array $data The raw data retrieved from the database.
     * @return mixed The formatted data
     */
    protected function formatData(array $data) : mixed
    {
        return $data; // Default implementation returns the raw data.
    }

    /**
     * Retrieves player data associated with the specified UUID.
     * If data is not found in memory, it attempts to load it from the database.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return mixed Player data associated with the UUID, or a Generator object.
     */
    final public function getData(UuidInterface $uuid) : mixed
    {
        try {
            $this->ensure_valid_entry($uuid); // This will throw InvalidArgumentException if player is offline
            return $this->data[$uuid->toString()]; // Fetch from memory
        } catch (InvalidArgumentException $e) {
            return $this->get_direct($uuid); // Fetch from database, and return Generator.
        }
    }

    /**
     * Executes asynchronous code using Await.
     * 
     * @param callable $generatorFunction The asynchronous function to run.
     * @return void
     */
    final protected function runAsync(callable $generatorFunction) : void
    {
        Await::f2c($generatorFunction);
    }

    /**
     * Saves all online player data to the database asynchronously.
     * 
     * @return void
     */
    final public function close_all() : void
    {
        foreach ($this->data as $uuid_str => $player_data) {
            $uuid = Uuid::fromString($uuid_str);
            $this->close($uuid);
        }
    }

    /**
     * Updates all online player data to the database asynchronously.
     * 
     * @return void
     */
    final public function update_all() : void
    {
        foreach ($this->data as $uuid_str => $player_data) {
            $uuid = Uuid::fromString($uuid_str);
            $this->update($uuid);
        }
    }

    /**
     * Ensures that a valid entry exists for the specified UUID in memory.
     * Throws an exception if no data is found for the given UUID.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @throws InvalidArgumentException If no data is found for the UUID.
     * @return void
     */
    final protected function ensure_valid_entry(UuidInterface $uuid) : void
    {
        $uuid_str = $uuid->toString();

        if (!isset($this->keys[$uuid_str])) {
            throw new InvalidArgumentException("Could not find data associated with {$uuid_str}.");
        }
    }

    /**
     * Adds a player's data to memory.
     * Called within create and start methods to allow fast-paced data management for online players.
     * 
     * @param UuidInterface $uuid
     * @param mixed $data
     * @return void
     */
    final protected function add_entry(UuidInterface $uuid, mixed $data) : void
    {
        $this->data[$uuid->toString()] = $this->formatData($data);
    }

    /**
     * Removes a player's data from memory.
     * Called within delete and close methods to clean up memory usage.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return void
     */
    final protected function remove_entry(UuidInterface $uuid) : void
    {
        unset($this->data[$uuid->toString()]);
    }

    /**
     * Adds a key to the '$keys' tracker when data is created or loaded.
     * 
     * @param UuidInterface $uuid
     * @return void
     */
    final protected function add_key(UuidInterface $uuid) : void
    {
        $this->keys[$uuid->toString()] = true;
    }

    /**
     * Removes a key from the '$keys' tracker when data is deleted or closed.
     * 
     * @param UuidInterface $uuid
     * @return void
     */
    final protected function remove_key(UuidInterface $uuid) : void
    {
        unset($this->keys[$uuid->toString()]);
    }

    /**
     * Creates a new entry for player data in the database and memory.
     *
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return void
     */
    final public function create(UuidInterface $uuid) : void
    {
        $this->runAsync(function() use ($uuid) {
            $args = $this->prepare_create_arguments($uuid);
            yield from $this->getDatabase()->asyncInsert($this->getCreateQuery(), $args);

            $this->add_entry($uuid, $args);
            $this->add_key($uuid);
        });
    }

    /**
     * Deletes a player’s data from the database and memory.
     *
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return void
     */
    final public function delete(UuidInterface $uuid) : void
    {
        $this->runAsync(function() use ($uuid) {
            try {
                // If data exists in the entry.
                $this->ensure_valid_entry($uuid);

                if (($player = Server::getInstance()->getPlayerByUuid($uuid)) !== null) {
                    $player->kick("An admin has initiated to reset all your data.");
                    // simply kicking the player will handle the data reset, due to the join / leave listener.
                }
            } catch (InvalidArgumentException) {}
            
            yield from $this->getDatabase()->asyncChange($this->getDeleteQuery(), ["uuid" => $uuid->toString()]);
        });
    }

    /**
     * Starts a session for player data by loading it from the database into memory.
     *
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return void
     */
    final public function start(UuidInterface $uuid) : void
    {
        $this->runAsync(function() use ($uuid) {
            $args = ["uuid" => $uuid->toString()];
            $rows = yield from $this->getDatabase()->asyncSelect($this->getSelectQuery(), $args);

            // If data doesn't exist within the database (player's first time joining)
            if (empty($rows)) {
                $this->create($uuid);
            } else {
                $this->add_entry($uuid, $rows[0]);
            }
        });
    }

    /**
     * Closes a player’s data session, saves data to the database, and removes it from memory.
     *
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return void
     */
    final public function close(UuidInterface $uuid) : void
    {
        $this->runAsync(function() use ($uuid) {
            $this->update($uuid);
            $this->remove_entry($uuid);
        });
    }

    /**
     * Updates player data in the database with current memory data.
     *
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return void
     */
    final public function update(UuidInterface $uuid) : void
    {
        $this->runAsync(function() use ($uuid) {
            $this->ensure_valid_entry($uuid);

            $args = $this->prepare_update_arguments($uuid);
            yield from $this->getDatabase()->asyncChange($this->getUpdateQuery(), $args);
        });
    }

    /**
     * Retrieves data directly from the database for the specified UUID.
     *
     * @param UuidInterface $uuid Unique identifier of the player.
     * @return Generator
     */
    final public function get_direct(UuidInterface $uuid) : Generator
    {
        $rows = yield from $this->getDatabase()->asyncSelect($this->getSelectQuery(), ["uuid" => $uuid->toString()]);

        // If there isn't any data returned.
        if (empty($rows)) {
            throw new InvalidArgumentException("No data found for UUID: {$uuid->toString()}.");
        }

        return $this->formatData($rows[0]);
    }

    /**
     * Returns the current list of all loaded player data in memory.
     * 
     * @return array List of player data indexed by UUID.
     */
    final public function getAll() : array
    {
        return $this->data;
    }

    /**
     * Retrieves the database connector instance from the plugin.
     * 
     * @return DataConnector Database connector instance.
     */
    final protected function getDatabase() : DataConnector
    {
        return $this->getPlugin()->getDatabase();
    }
}
