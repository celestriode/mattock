<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Mattock\Exceptions\Utils\UtilsException;
use IntlChar;

/**
 * A representation of resource locations in Minecraft.
 *
 * @package Celestriode\Mattock\Parser\Java\Utils
 */
class ResourceLocation
{
    public const TAG_TOKEN = '#';
    public const NAMESPACE = 'minecraft';
    public const DELIMITER = ':';

    /**
     * @var string The namespace within the resource location.
     */
    private $namespace;

    /**
     * @var string The path within the resource location.
     */
    private $path;

    /**
     * @var bool Whether or not this resource location derived from a tagged resource location (e.g., #minecraft:test).
     */
    private $tag;

    /**
     * @param string $namespace The namespace within the resource location.
     * @param string $path The path within the resource location.
     */
    public function __construct(string $namespace, string $path, bool $tag = false)
    {
        $this->namespace = $namespace;
        $this->path = $path;
        $this->tag = $tag;
    }

    /**
     * Returns the namespace of the resource location.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Returns the path of the resource location.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns whether or not this resource location derived from a tagged resource location (e.g., #minecraft:test).
     *
     * @return bool
     */
    public function isTag(): bool
    {
        return $this->tag;
    }

    /**
     * Returns the resource location in its flattened form ("namespace:path").
     *
     * @return string
     */
    public function toString(bool $includeTag = false): string
    {
        return (($includeTag && $this->isTag()) ? '#' : '') . ($this->getNamespace() . self::DELIMITER . $this->getPath());
    }

    /**
     * Returns whether or not a flattened resource location ("namespace:path") is equal to the resource.
     *
     * @param string $input The raw string to compare to.
     * @return bool
     */
    public function matches(string $input, bool $checkForTag = false): bool
    {
        return $this->toString($checkForTag) == $input;
    }

    /**
     * Creates a new resource location from a string reader. Resource locations follow the syntax:
     *
     * "namespace:path/to/resource"
     *
     * Or:
     *
     * "path/to/resource"
     *
     * @param StringReader $reader The reader that would contain a resource location.
     * @param bool $checkForTag
     * @return static
     * @throws CommandSyntaxException
     */
    public static function read(StringReader $reader, bool $checkForTag = false): self
    {
        return static::decompose($reader,self::DELIMITER, $checkForTag);
    }

    /**
     * Reads a string until it hits a non-resource-location-compliant character and then attempts to parse what the
     * characters prior as a resource location.
     *
     * @param StringReader $reader
     * @param bool $checkForTag
     * @return static
     * @throws CommandSyntaxException
     */
    public static function readLenient(StringReader $reader, bool $checkForTag = false): self
    {
        $n = $reader->getCursor();

        // If tagged resource locations (e.g., #minecraft:test) should be checked, skip the tag token if it is present.

        if ($checkForTag && $reader->peek() == self::TAG_TOKEN) {

            $reader->skip();
        }

        // Step through the string until it hits a character like , or ] that cannot be in a resource location.

        while ($reader->canRead() && ResourceLocation::isAllowedInResourceLocation($reader->peek())) {

            $reader->skip();
        }

        // Get a substring based on where the cursor ended.

        $string = mb_substr($reader->getString(), $n, $reader->getCursor() - $n);

        // Now try to validate that resource location.

        try {

            return static::read(new StringReader($string), $checkForTag);
        } catch (CommandSyntaxException $e) {

            $reader->setCursor($n);

            throw $e;
        }
    }

    /**
     * Takes in a raw resource string and a delimiter to locate, splits the string at the delimiter, and returns a new
     * resource location using those parts (when applicable). Fills in the namespace with a default if no delimiter
     * was present. e.g. "blah" turns into "minecraft:blah" while "test:blah" remains the same.
     *
     * @param StringReader $reader The reader being used for parsing.
     * @param string $delimiter The delimiter separating the namespace from the path.
     * @param bool $checkForTag
     * @return static
     * @throws CommandSyntaxException
     */
    public static function decompose(StringReader $reader, string $delimiter = self::DELIMITER, bool $checkForTag = false): self
    {
        // If the input is a tag, label it as such.

        $tag = false;

        if ($checkForTag && $reader->peek() == self::TAG_TOKEN) {

            $tag = true;
            $reader->skip();
        }

        $string = $reader->getRemaining();

        $parts = [self::NAMESPACE, $string];
        $index = mb_strpos($string, $delimiter);

        // If a delimiter was present...

        if ($index !== false) {

            // Set the path as the string, but past the delimiter.

            $parts[1] = mb_substr($string, $index + 1);

            // If the delimiter is not the first character...

            if ($index >= 1) {

                // Set the namespace to what was before the index. If index was 0, then no namespace was supplied.

                $parts[0] = mb_substr($string, 0, $index);
            }
        }

        // Validate the namespace.

        $namespace = new StringReader($parts[0]);

        if (!static::isValidNamespace($namespace)) {

            throw UtilsException::getBuiltInExceptions()->invalidResourceNamespace()->createWithContext($namespace, $parts[0]);
        }

        // Validate the path.

        $path = new StringReader($parts[1]);

        if (!static::isValidPath($path)) {

            throw UtilsException::getBuiltInExceptions()->invalidResourcePath()->createWithContext($path, $parts[1]);
        }

        // All good, create and return a new resource location.

        return new static($parts[0], $parts[1], $tag);
    }

    /**
     * Returns whether or not there are any invalid characters in a namespace.
     *
     * @param StringReader $namespace The namespace to validate.
     * @return bool
     */
    public static function isValidNamespace(StringReader $namespace): bool
    {
        // If the namespace is the default, don't bother checking.

        if ($namespace->getString() === self::NAMESPACE) {

            return true;
        }

        while ($namespace->canRead()) {

            // If the character is not valid, return false.

            if (!static::validNamespaceCharacter($namespace->read())) {

                return false;
            }
        }

        // All characters valid, return true.

        return true;
    }

    /**
     * Returns whether or not there are any invalid characters in a path.
     *
     * @param StringReader $path The path to validate.
     * @return bool
     */
    public static function isValidPath(StringReader $path): bool
    {
        while ($path->canRead()) {

            // If the character is not valid, return false.

            if (!static::validPathCharacter($path->read())) {

                return false;
            }
        }

        // ALl characters valid, return true.

        return true;
    }

    /**
     * Returns whether or not the character is allowed in any part of the resource location.
     *
     * @param string $char The character to validate.
     * @return bool
     */
    public static function isAllowedInResourceLocation(string $char): bool
    {
        $ord = IntlChar::ord($char);

        return ($ord >= 48 && $ord <= 57) || ($ord >= 97 && $ord <= 122) // 0-9, a-z
            || $char == '_'
            || $char == ':'
            || $char == '/'
            || $char == '.'
            || $char == '-';
    }

    /**
     * Returns whether or not the character is a valid namespace character.
     *
     * @param string $char The character to validate.
     * @return bool
     */
    protected static function validNamespaceCharacter(string $char): bool
    {
        $ord = IntlChar::ord($char);

        return ($ord >= 48 && $ord <= 57) || ($ord >= 97 && $ord <= 122) // 0-9, a-z
            || $char == '_'
            || $char == ':'
            || $char == '.'
            || $char == '-';
    }

    /**
     * Returns whether or not the character is a valid path character.
     *
     * @param string $char The character to validate.
     * @return bool
     */
    protected static function validPathCharacter(string $char): bool
    {
        return static::isAllowedInResourceLocation($char);
    }
}