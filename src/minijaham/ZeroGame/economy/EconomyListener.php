<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\economy;

use pocketmine\event\player\{
    PlayerLoginEvent,
    PlayerQuitEvent
};

use minijaham\ZeroGame\data\DataManager;
use minijaham\ZeroGame\data\DataListener;

final class EconomyListener extends DataListener
{
    public function __construct(private DataManager $manager)
    {
        parent::__construct($manager);
    }

    /**
     * @priority LOWEST
     */
    public function on_player_login(PlayerLoginEvent $event) : void
    {
        parent::on_player_login($event);
    }

    /**
     * @priority HIGHEST
     */
    public function on_player_leave(PlayerQuitEvent $event) : void
    {
        parent::on_player_leave($event);
    }
}