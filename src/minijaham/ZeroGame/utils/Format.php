<?php

declare(strict_types=1);

namespace minijaham\ZeroGame\utils;

use pocketmine\utils\TextFormat;

final class Format extends TextFormat
{
    public const DEBUG = self::GRAY . "[" . self::YELLOW . "DEBUG"     . self::GRAY . "] " . self::WHITE;
    public const ADMIN = self::GRAY . "[" . self::YELLOW . "ADMIN"     . self::GRAY . "] " . self::RESET;
    public const STAFF = self::GRAY . "[" . self::GOLD   . "STAFFCHAT" . self::GRAY . "] " . self::RESET;

    public const SUCCESS = self::GRAY . "(" . self::GREEN . "!" . self::GRAY . ") " . self::GREEN;
    public const WARNING = self::GRAY . "(" . self::RED   . "!" . self::GRAY . ") " . self::RED;
    public const NOTIFY  = self::GRAY . "(" . self::GOLD  . "!" . self::GRAY . ") " . self::GREEN;

    public const PLAYER_ONLY = self::WARNING . "You cannot use this command from console.";
    public const PLAYER_NOT_FOUND = self::WARNING . "Cannot find target player.";

    /**
     * Returns a formated string for a given amount of integer
     * 
     * @param int $integer
     * @return string
     */
    public static function format_int(int $integer) : string
    {
        return number_format($integer);
    }
}