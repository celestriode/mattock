<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Mattock\Exceptions\Utils\UtilsException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * A representation of UUIDs as used in Minecraft. The primary difference between this class and ramsey/uuid is the fact
 * that UUIDs in Minecraft do not have to be fully-formed (e.g. 4-4-4-4-4 is a valid UUID in Minecraft but not with the
 * ramsey/uuid library).
 *
 * @package Celestriode\Mattock\Parsers\Java\Utils
 */
class MinecraftUuid
{
    public const PATTERN = '/^[0-9a-f]{1,8}-[0-9a-f]{1,4}-[0-9a-f]{1,4}-[0-9a-f]{1,4}-[0-9a-f]{1,12}$/i';

    /**
     * Returns whether or not the input string matches the general UUID pattern in Minecraft.
     *
     * @param string $uuid The UUID to validate.
     * @return bool
     */
    public static function valid(string $uuid): bool
    {
        return preg_match(self::PATTERN, $uuid) === 1;
    }

    /**
     * Takes in a UUID string and transforms it into a ramsey/uuid object. Involves prepending zeros to unfinished UUIDs.
     *
     * @param string $uuid The UUID to normalize.
     * @return UuidInterface
     * @throws CommandSyntaxException
     */
    public static function normalize(string $uuid): UuidInterface
    {
        // If the UUID is not valid, throw error.

        if (!static::valid($uuid)) {

            throw UtilsException::getBuiltInExceptions()->invalidUuid()->createWithContext(new StringReader($uuid), $uuid);
        }

        // Split from dashes, and then prepend zeros to the limit for each section.

        $parts = explode('-', $uuid);

        $parts[0] = static::prependZeros($parts[0], 8);
        $parts[1] = static::prependZeros($parts[1], 4);
        $parts[2] = static::prependZeros($parts[2], 4);
        $parts[3] = static::prependZeros($parts[3], 4);
        $parts[4] = static::prependZeros($parts[4], 12);

        // Reassemble the UUID.

        $normalizedUuid = implode('-', $parts);

        // Create and return a ramsey/uuid UUID from the now-normalized UUID.

        return Uuid::fromString($normalizedUuid);
    }

    /**
     * Prepends zeros until reaching the limit for the section.
     *
     * @param string $part The potentially-incomplete UUID to prepend zeros to.
     * @param int $totalCharacters The number of characters long the UUID section should be.
     * @return string
     */
    protected static function prependZeros(string $part, int $totalCharacters): string
    {
        while (strlen($part) < $totalCharacters) {

            $part = '0' . $part;
        }

        return $part;
    }
}