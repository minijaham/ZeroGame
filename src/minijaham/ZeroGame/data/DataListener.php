<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\data;

use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerLoginEvent,
    PlayerQuitEvent
};

/**
 * Base event listener for data managers.
 * 
 * NOTE: The '@priority' tag is not inherited. It serves as documentation.
 *       Be sure to add '@priority' annotations on overridden functions as needed.
 */
abstract class DataListener implements Listener
{
    /**
     * DataListener Constructor.
     * 
     * @param DataManager $manager Instance of the DataManager for handling player data.
     */
    protected function __construct(private DataManager $manager){}
    
    /**
     * Handles player login event. Loads player data from the database into memory.
     * Executed at the lowest priority to ensure data is available for other events.
     * 
     * @param PlayerLoginEvent $event The player login event instance.
     * @priority LOWEST
     */
    protected function on_player_login(PlayerLoginEvent $event) : void
    {
        $player = $event->getPlayer();
        $uuid   = $player->getUniqueId();
        
        $this->manager->start($uuid);
    }

    /**
     * Handles player quit event. Saves player data to the database and removes it from memory.
     * Executed at the highest priority to ensure data is available for other events before closing.
     * 
     * @param PlayerQuitEvent $event The player quit event instance.
     * @priority HIGHEST
     */
    protected function on_player_leave(PlayerQuitEvent $event) : void
    {
        $player = $event->getPlayer();
        $uuid   = $player->getUniqueId();
        
        $this->manager->close($uuid);
    }

    /**
     * Retrieves the associated DataManager instance.
     * 
     * @return DataManager The DataManager instance for this listener.
     */
    final protected function getManager() : DataManager
    {
        return $this->manager;
    }
}