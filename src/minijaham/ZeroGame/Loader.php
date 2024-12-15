<?php

declare(strict_types=1);

namespace minijaham\ZeroGame;

// pocketmine
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;

// Extends Manager
use minijaham\ZeroGame\world\WorldManager;

// Extends DataManager
use minijaham\ZeroGame\player\UsernameManager;

// Utility
use minijaham\ZeroGame\utils\exception\ShitCodeException;

// libasynql
use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;

// InvMenu
use muqsit\invmenu\InvMenuHandler;

final class Loader extends PluginBase
{
    use SingletonTrait;

    // Database instance
    private static DataConnector $database;

    /**
     * List of managers
     * 
     * @var array<string, Manager>
     */
    private static array $managers = [];

    /**
     * PocketMine Load Function.
     * 
     * This function enables SingletonTrait.
     * 
     * @return void
     */
    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    /**
     * PocketMine Enable Function.
     * 
     * This function holds various processes to initialize the plugin.
     * 
     * @return void
     */
    protected function onEnable() : void
    {
        $this->initialize_database();
        $this->initialize_managers();

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        /** Repeating task to call save_all every minute (1200 ticks) */
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void {
            $this->save_all(shutdown: false);
        }), 1200);
    }

    /**
     * PocketMine Disable Function.
     * 
     * This function holds the code to close the database.
     * 
     * @return void
     */
    protected function onDisable() : void
    {
        if(!isset($this->database)) {
            return;
        }

        $this->save_all(shutdown: true);

        $this->database->close();
    }

    /**
     * Function to initialize data and its management classes.
     * 
     * @return void
     */
    private function initialize_database() : void
    {
        $settings = [
            "type" => "sqlite",
            "sqlite" => ["file" => "sqlite.sql"],
            "worker-limit" => 1
        ];

        self::$database = libasynql::create(self::getInstance(), $settings, ["sqlite" => "sqlite.sql"]);

        self::$database->waitAll();
    }

    /**
     * Function to get database instance.
     * 
     * @return DataConnector
     */
    public static function getDatabase() : DataConnector
    {
        return self::$database;
    }

    /**
     * Function to initialize all managers and add them to the memory.
     * 
     * @return void
     */
    private function initialize_managers() : void
    {
        // Manager children
        $this->register_manager(new WorldManager($this));

        // DataManager children
        $this->register_manager(new UsernameManager($this));
    }

    /**
     * Registers a manager to the memory.
     * 
     * @param Manager $manager
     * @return void
     */
    private function register_manager(Manager $manager) : void
    {
        self::$managers[get_class($manager)] = $manager;
    }

    /**
     * Retrieve a manager by its class name.
     *
     * @param class-string<Manager> $className
     * @return Manager
     */
    public static function fetch(string $className) : Manager
    {
        if (!isset(self::$managers[$className])) {
            throw new ShitCodeException("Manager {$className} is not registered.");
        }

        return self::$managers[$className];
    }

    /**
     * Function to save all player data
     * 
     * @return void
     */
    private function save_all(bool $shutdown) : void
    {
        foreach (self::$managers as $manager) {
            if ($manager instanceof DataManager) {
                $shutdown ? $manager->close_all() : $manager->update_all();
            }
        }
    }
}