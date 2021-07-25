<?php namespace Celestriode\Mattock\Exceptions\Nbt;

use Celestriode\Mattock\Exceptions\MattockException;

class NbtException extends MattockException
{
    private static $builtInReaderExceptions;

    public static function getBuiltInExceptions(): BuiltInNbtExceptions
    {
        return self::$builtInReaderExceptions ?? self::$builtInReaderExceptions = new BuiltInNbtExceptions();
    }
}