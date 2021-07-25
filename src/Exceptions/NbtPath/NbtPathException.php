<?php namespace Celestriode\Mattock\Exceptions\NbtPath;

use Celestriode\Mattock\Exceptions\MattockException;

class NbtPathException extends MattockException
{
    private static $builtInReaderExceptions;

    public static function getBuiltInExceptions(): BuiltInNbtPathExceptions
    {
        return self::$builtInReaderExceptions ?? self::$builtInReaderExceptions = new BuiltInNbtPathExceptions();
    }
}