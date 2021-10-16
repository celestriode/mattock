<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\DynamicRegistry\AbstractRegistry;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "gamemode" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class GameMode extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var AbstractRegistry|null Valid game modes for use with this option.
     */
    protected static $gamemodeRegistry;

    /**
     * @var string The value of the "gamemode" option.
     */
    private $gameMode;

    /**
     * @var bool Whether or not the option is inverted.
     */
    private $inverted;

    public function __construct(string $gameMode, bool $inverted)
    {
        $this->gameMode = $gameMode;
        $this->inverted = $inverted;
    }

    /**
     * Returns the value of the "gamemode" option.
     *
     * @return string
     */
    public function getGameMode(): string
    {
        return $this->gameMode;
    }

    /**
     * Returns whether or not this option is inverted.
     *
     * @return bool
     */
    public function inverted(): bool
    {
        return $this->inverted;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $n = $parser->getReader()->getCursor();
        $inverted = self::shouldInvertValue($parser->getReader());

        // If this option is a duplicate, throw error.

        if (!$inverted && !empty($parser->getBuilder()->getSelector()->getGameModes())) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->duplicateInvertibleOption()->createWithContext($parser->getReader(), 'gamemode');
        }

        $gameMode = $parser->getReader()->readUnquotedString();

        // If the value is not a valid game mode, throw error.

        if (self::$gamemodeRegistry !== null && !self::$gamemodeRegistry->has($gameMode)) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->unknownGameMode(self::$gamemodeRegistry)->createWithContext($parser->getReader(), $gameMode);
        }

        $parser->getBuilder()->getSelector()->setIncludesNonPlayerEntities(false);

        if ($inverted) {

            $parser->getBuilder()->gameModesInverted = true;
        }

        return new self($gameMode, $inverted);
    }

    /**
     * Ensures that more than one gamemode= has not been set. More than one gamemode=! is allowed.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->gameModesInverted || empty($builder->getSelector()->getGameModes());
    }

    /**
     * Sets the game mode registry to the input registry. This is where a list of valid game modes would be supplied.
     *
     * @param AbstractRegistry $registry
     */
    public static function setGamemodeRegistry(AbstractRegistry $registry): void
    {
        self::$gamemodeRegistry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return 2;
    }
}