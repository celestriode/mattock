<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Mattock\Exceptions\Selector\InvalidOption;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * An option narrows down the entities to target. This base class includes option registration options.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
abstract class AbstractOption
{
    /**
     * @var string[] A mapping of option names to their parsing classes.
     */
    private static $options = [
        'name' => Name::class,
        'distance' => Distance::class,
        'level' => Level::class, // forced to be last, see parser.finalizePredicates()
        'x' => X::class,
        'y' => Y::class,
        'z' => Z::class,
        'dx' => DX::class,
        'dy' => DY::class,
        'dz' => DZ::class,
        'x_rotation' => XRotation::class, // forced to be 3rd last
        'y_rotation' => YRotation::class, // forced to be 2nd last
        'limit' => Limit::class,
        'sort' => Sort::class,
        'gamemode' => GameMode::class,
        'team' => Team::class,
        'type' => Type::class,
        'tag' => Tag::class,
        'nbt' => Nbt::class,
        'scores' => Scores::class,
        'advancements' => Advancements::class,
        'predicate' => Predicate::class,
    ];

    /**
     * Parses input for the value of an option, creating a new option instance if so. If test() was true, handle()
     * should be called.
     *
     * @param EntitySelectorParser $parser
     * @return static
     */
    abstract public static function handle(EntitySelectorParser $parser): self;

    /**
     * Tests whether or not the option is applicable based on previously-specified options.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    abstract public static function test(DynamicSelectorBuilder $builder): bool;

    /**
     * Returns whether or not there is a ! as the next value. Used with dynamic selector options to invert input. Skips
     * it if so.
     *
     * @param StringReader $reader
     * @return bool
     */
    protected static function shouldInvertValue(StringReader $reader): bool
    {
        $reader->skipWhitespace();

        if ($reader->canRead() && $reader->peek() == '!') {

            $reader->skip();
            $reader->skipWhitespace();

            return true;
        }

        return false;
    }

    /**
     * Returns whether or not the next character is "#", indicating a tag identifier. Skips it if so.
     *
     * @param StringReader $reader
     * @return bool
     */
    protected static function isTag(StringReader $reader): bool
    {
        $reader->skipWhitespace();

        if ($reader->canRead() && $reader->peek() == '#') {

            $reader->skip();
            $reader->skipWhitespace();

            return true;
        }

        return false;
    }

    /**
     * Registers an option with a name and a class. Can optionally overwrite options already registered.
     *
     * @param string $optionName
     * @param string $className
     * @param bool $overwrite
     * @throws InvalidOption
     */
    final public static function register(string $optionName, string $className, bool $overwrite = false): void
    {
        // If the option already exists and it's not set to be overwritten, throw an error.

        if (!$overwrite && isset(self::$options[$optionName])) {

            throw new InvalidOption('Option "' . $optionName . '" is already registered');
        }

        // If the associated class doesn't exist, also throw an error.

        if (!class_exists($className) || !is_subclass_of($className, self::class)) {

            throw new InvalidOption('Invalid class name or class for option "' . $optionName . '": ' . $className);
        }

        // All good, register the option.

        self::$options[$optionName] = $className;
    }

    /**
     * Creates and returns a singleton of the parser for the specific option.
     *
     * @param EntitySelectorParser $parser
     * @param string $name
     * @return string
     * @throws CommandSyntaxException
     */
    final public static function getClassName(EntitySelectorParser $parser, string $name): string
    {
        $option = self::$options[$name] ?? null;

        // Ensure the option exists.

        if ($option !== null && is_subclass_of($option, self::class)) {

            // Test if the option is allowed to be used.

            if ($option::test($parser->getBuilder())) {

                // If so, return the class name of the option.

                return $option;
            }

            // If not, throw error.

            throw SelectorException::getBuiltInExceptions()->inapplicableOption()->createWithContext($parser->getReader(), $name);
        }

        // If the option does not exist, throw error.

        throw SelectorException::getBuiltInExceptions()->unknownOption()->createWithContext($parser->getReader(), $name);
    }
}