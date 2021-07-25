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
     * @param string $namespace The namespace within the resource location.
     * @param string $path The path within the resource location.
     */
    protected function __construct(string $namespace, string $path)
    {
        $this->namespace = $namespace;
        $this->path = $path;
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
     * Returns the resource location in its flattened form ("namespace:path").
     *
     * @return string
     */
    public function toString(): string
    {
        return ($this->getNamespace() . self::DELIMITER . $this->getPath());
    }

    /**
     * Returns whether or not a flattened resource location ("namespace:path") is equal to the resource.
     *
     * @param string $input The raw string to compare to.
     * @return bool
     */
    public function matches(string $input): bool
    {
        return $this->toString() == $input;
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
     * @return static
     * @throws CommandSyntaxException
     */
    public static function read(StringReader $reader): self
    {
        $n = $reader->getCursor();

        // Cycle through all characters until the end.

        while ($reader->canRead() && static::isAllowedInResourceLocation($reader->peek())) {

            $reader->skip();
        }

        $string = mb_substr($reader->getString(), $n, $reader->getCursor() - $n);

        try {

            return static::decompose($reader, $string);

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
     * @param string $string The raw resource string to be transformed into a ResourceLocation object.
     * @param string $delimiter The delimiter separating the namespace from the path.
     * @return static
     * @throws CommandSyntaxException
     */
    public static function decompose(StringReader $reader, string $string, string $delimiter = self::DELIMITER): self
    {
        $parts = [self::NAMESPACE, $string];
        $index = mb_strpos($string, $delimiter);

        // If a delimiter was present...

        if ($index !== false) {

            // Set the path as the string, but past the delimiter.

            $parts[1] = mb_substr($string, $index + 1);

            // If the delimiter is not the first character...

            if ($index >= 1) {

                // Set the namespace to what was before the index.

                $parts[0] = mb_substr($string, 0, $index);
            }
        }

        // Validate the namespace.

        if (!static::isValidNamespace($parts[0])) {

            throw UtilsException::getBuiltInExceptions()->invalidResourceNamespace()->createWithContext($reader, $parts[0]);
        }

        // Validate the path.

        if (!static::isValidPath($parts[1])) {

            throw UtilsException::getBuiltInExceptions()->invalidResourcePath()->createWithContext($reader, $parts[1]);
        }

        // All good, create and return a new resource location.

        return new static($parts[0], $parts[1]);
    }

    /**
     * Returns whether or not there are any invalid characters in a namespace.
     *
     * @param string $namespace The namespace to validate.
     * @return bool
     */
    public static function isValidNamespace(string $namespace): bool
    {
        // If the namespace is the default, don't bother checking.

        if ($namespace === self::NAMESPACE) {

            return true;
        }

        // Split by unicode characters.

        $characters = preg_split('//u', $namespace, -1, PREG_SPLIT_NO_EMPTY);

        // Cycle through each character.

        for ($i = 0, $j = count($characters); $i < $j; $i++) {

            // If the character is not valid, return false.

            if (!static::validNamespaceCharacter($characters[$i])) {

                return false;
            }
        }

        // All characters valid, return true.

        return true;
    }

    /**
     * Returns whether or not there are any invalid characters in a path.
     *
     * @param string $path The path to validate.
     * @return bool
     */
    public static function isValidPath(string $path): bool
    {
        // Split by unicode characters.

        $characters = preg_split('//u', $path, -1, PREG_SPLIT_NO_EMPTY);

        // Cycle through each character.

        for ($i = 0, $j = count($characters); $i < $j; $i++) {

            // If the character is not valid, return false.

            if (!static::validPathCharacter($characters[$i])) {

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