<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\data\database;

/**
 * Class that holds all the PSF queries for sqlite database.
 * 
 * See "resources/sqlite.sql" for more information.
 */
final class Queries
{
    public const PLAYER_USERNAME_INIT   = "player_username.initialize";
    public const PLAYER_USERNAME_SELECT = "player_username.select";
    public const PLAYER_USERNAME_CREATE = "player_username.create";
    public const PLAYER_USERNAME_UPDATE = "player_username.update";
    public const PLAYER_USERNAME_DELETE = "player_username.delete";
    
    public const PLAYER_ECONOMY_INIT   = "player_economy.initialize";
    public const PLAYER_ECONOMY_SELECT = "player_economy.select";
    public const PLAYER_ECONOMY_CREATE = "player_economy.create";
    public const PLAYER_ECONOMY_UPDATE = "player_economy.update";
    public const PLAYER_ECONOMY_DELETE = "player_economy.delete";
}