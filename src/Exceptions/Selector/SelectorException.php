<?php namespace Celestriode\Mattock\Exceptions\Selector;

use Celestriode\Mattock\Exceptions\MattockException;

class SelectorException extends MattockException
{
    private static $builtInSelectorExceptions;

    public static function getBuiltInExceptions(): BuiltInSelectorExceptions
    {
        return self::$builtInSelectorExceptions ?? self::$builtInSelectorExceptions = new BuiltInSelectorExceptions();
    }
}