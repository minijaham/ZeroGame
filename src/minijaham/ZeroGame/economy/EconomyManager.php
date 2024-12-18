<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\economy;

use minijaham\ZeroGame\Loader;
use minijaham\ZeroGame\data\DataManager;
use minijaham\ZeroGame\data\database\Queries;

use Ramsey\Uuid\UuidInterface;

use Exception;
use InvalidArgumentException;

/**
 * Manages player economy (life).
 */
final class EconomyManager extends DataManager
{
    private const STARTING_BALANCE = 100;
    
    /**
     * EconomyManager Constructor.
     * 
     * @param Loader $plugin
     */
    public function __construct(private Loader $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Initializes the EconomyManager and registers necessary event listeners.
     * 
     * @return void
     */
    protected function init() : void
    {
        parent::init();
    }

    /**
     * Returns the DataListener instance for managing economy-related events.
     * 
     * @return EconomyListener
     */
    protected function getListener() : EconomyListener
    {
        return new EconomyListener($this);
    }

    /**
     * Returns the SQL query string used to initialize economy data.
     * 
     * @return string
     */
    protected function getInitQuery() : string
    {
        return Queries::PLAYER_ECONOMY_INIT;
    }

    /**
     * Returns the SQL query string used to create a new economy data entry.
     * 
     * @return string
     */
    protected function getCreateQuery() : string
    {
        return Queries::PLAYER_ECONOMY_CREATE;
    }

    /**
     * Returns the SQL query string used to update economy data.
     * 
     * @return string
     */
    protected function getUpdateQuery() : string
    {
        return Queries::PLAYER_ECONOMY_UPDATE;
    }

    /**
     * Returns the SQL query string used to delete economy data.
     * 
     * @return string
     */
    protected function getDeleteQuery() : string
    {
        return Queries::PLAYER_ECONOMY_DELETE;
    }

    /**
     * Returns the SQL query string used to select economy data.
     * 
     * @return string
     */
    protected function getSelectQuery() : string
    {
        return Queries::PLAYER_ECONOMY_SELECT;
    }

    /**
     * Prepares arguments required for creating a new economy data entry.
     * 
     * @param UuidInterface $uuid The unique identifier of the player.
     * @return array
     */
    protected function prepare_create_arguments(UuidInterface $uuid) : array
    {
        return [
            "uuid" => $uuid->toString(),
            "balance" => self::STARTING_BALANCE,
        ];
    }

    /**
     * Prepares arguments required for updating existing economy data.
     * 
     * @param UuidInterface $uuid The unique identifier of the player.
     * @return array
     */
    protected function prepare_update_arguments(UuidInterface $uuid) : array
    {
        return [
            "uuid" => $uuid->toString(),
            "balance" => $this->data[$uuid->toString()],
        ];
    }

    /**
     * Formats raw data retrieved from the database before storing it in memory.
     * 
     * @param array $data The raw data retrieved from the database.
     * @return int The formatted data.
     */
    protected function format_data(array $data) : int
    {
        return (int) $data["balance"];
    }

    /**
     * Adds the specified amount to a player's balance in memory.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to add to the player's balance.
     * @return void
     */
    public function addMoney(UuidInterface $uuid, int $amount) : void
    {
        $this->updateMoneyInMemory($uuid, $amount, 'add');
    }

    /**
     * Adds the specified amount to a player's balance directly in the database.
     * This function handles asynchronous operations internally and does not yield.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to add to the player's balance.
     * @return void
     */
    public function addMoneyDirect(UuidInterface $uuid, int $amount) : void
    {
        $this->updateMoneyDirect($uuid, $amount, 'add');
    }

    /**
     * Removes the specified amount from a player's balance in memory.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to remove from the player's balance.
     * @return void
     */
    public function removeMoney(UuidInterface $uuid, int $amount) : void
    {
        $this->updateMoneyInMemory($uuid, $amount, 'remove');
    }

    /**
     * Removes the specified amount from a player's balance directly in the database.
     * This function handles asynchronous operations internally and does not yield.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to remove from the player's balance.
     * @return void
     */
    public function removeMoneyDirect(UuidInterface $uuid, int $amount) : void
    {
        $this->updateMoneyDirect($uuid, $amount, 'remove');
    }

    /**
     * Sets a player's balance to the specified amount in memory.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to set as the player's balance.
     * @return void
     */
    public function setMoney(UuidInterface $uuid, int $amount) : void
    {
        $this->updateMoneyInMemory($uuid, $amount, 'set');
    }

    /**
     * Sets a player's balance to the specified amount directly in the database.
     * This function handles asynchronous operations internally and does not yield.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to set as the player's balance.
     * @return void
     */
    public function setMoneyDirect(UuidInterface $uuid, int $amount) : void
    {
        $this->updateMoneyDirect($uuid, $amount, 'set');
    }

    /**
     * Transfers a specified amount from one player to another.
     * Throws an exception if the payer has insufficient balance.
     * 
     * @param UuidInterface $payerUuid Unique identifier of the payer.
     * @param UuidInterface $receiverUuid Unique identifier of the receiver.
     * @param int $amount Amount to transfer.
     * @throws Exception If the payer's balance is insufficient for the transfer.
     * @return void 
     */
    public function pay(UuidInterface $payerUuid, UuidInterface $receiverUuid, int $amount) : void
    {
        $payerBalance = $this->getData($payerUuid);

        if ($payerBalance < $amount) {
            throw new Exception("You don't have enough life to send.");
        }

        $this->updateMoneyInMemory($payerUuid, $amount, 'remove');
        $this->updateMoneyInMemory($receiverUuid, $amount, 'add');
    }

    /**
     * Updates a player's balance in memory based on the specified operation.
     * Valid operations are 'add', 'remove', and 'set'.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to add, remove, or set.
     * @param string $operation Operation type ('add', 'remove', 'set').
     * @return void
     * @throws InvalidArgumentException If an invalid operation is provided.
     */
    private function updateMoneyInMemory(UuidInterface $uuid, int $amount, string $operation) : void
    {
        $this->ensure_valid_entry($uuid);

        $balance = $this->data[$uuid->toString()];
        $newBalance = match ($operation) {
            'add'    => $balance + $amount,
            'remove' => max($balance - $amount, 0),
            'set'    => $amount,
            default  => throw new InvalidArgumentException("Invalid operation '{$operation}'"),
        };
        $this->data[$uuid->toString()] = $newBalance;
    }

    /**
     * Updates a player's balance directly in the database based on the specified operation.
     * 
     * @param UuidInterface $uuid Unique identifier of the player.
     * @param int $amount Amount to add, remove, or set.
     * @param string $operation Operation type ('add', 'remove', 'set').
     * @throws InvalidArgumentException If an invalid operation is provided.
     * @return void
     */
    private function updateMoneyDirect(UuidInterface $uuid, int $amount, string $operation) : void
    {
        $this->runAsync(function () use ($uuid, $amount, $operation) {
            $args = ["uuid" => $uuid->toString()];
            $rows = yield from $this->getDatabase()->asyncSelect(Queries::PLAYER_ECONOMY_SELECT, $args);
    
            if (empty($rows)) {
                throw new InvalidArgumentException("Entry not found for UUID: " . $uuid->toString());
            }
    
            $currentBalance = (int) $rows[0]["balance"];
            $newBalance = match ($operation) {
                'add'    => $currentBalance + $amount,
                'remove' => max($currentBalance - $amount, 0),
                'set'    => $amount,
                default  => throw new InvalidArgumentException("Invalid operation '{$operation}'"),
            };
    
            yield from $this->getDatabase()->asyncChange(Queries::PLAYER_ECONOMY_UPDATE, [
                "uuid" => $uuid->toString(),
                "balance" => $newBalance
            ]);
        });
    }
}