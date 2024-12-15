<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\world;

use pocketmine\Server;
use pocketmine\world\World;

use minijaham\ZeroGame\Loader;
use minijaham\ZeroGame\Manager;
use minijaham\ZeroGame\utils\Format;

final class WorldManager extends Manager
{
    /**
     * WorldManager Constructor.
     * 
     * @param Loader $plugin
     */
    public function __construct(private Loader $plugin)
    {
        parent::__construct($plugin);
    }

    public function init() : void
    {
        $this->load_worlds();
    }

    /**
     * Load all the worlds
     * 
     * @return void
     */
    private function load_worlds() : void
    {
        foreach (array_diff(scandir(Server::getInstance()->getDataPath() . "worlds"), ["..", "."]) as $world) {
            Server::getInstance()->getWorldManager()->loadWorld($world, true);
            $this->plugin->getLogger()->debug(Format::DEBUG . "Loaded world: " . $world);
        }

        $loadedCount = count(Server::getInstance()->getWorldManager()->getWorlds());
        $this->plugin->getLogger()->debug(Format::DEBUG . "Loaded total of " . $loadedCount . "worlds.");
    }

    /**
     * Return default world of the server
     * 
     * @return World
     */
    public static function getDefaultWorld() : World
    {
        return Server::getInstance()->getWorldManager()->getDefaultWorld();
    }
}