<?php namespace Celestriode\Mattock\Exceptions\Utils;

use Celestriode\Mattock\Exceptions\MattockException;

class UtilsException extends MattockException
{
    private static $builtInSelectorExceptions;

    public static function getBuiltInExceptions(): BuiltInUtilsExceptions
    {
        return self::$builtInSelectorExceptions ?? self::$builtInSelectorExceptions = new BuiltInUtilsExceptions();
    }
}