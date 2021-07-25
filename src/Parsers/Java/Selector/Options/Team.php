<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "team" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Team extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var string The value of the "team" option.
     */
    private $team;

    /**
     * @var bool Whether or not this option is inverted.
     */
    private $inverted;

    protected function __construct(string $team, bool $inverted)
    {
        $this->team = $team;
        $this->inverted = $inverted;
    }

    /**
     * Returns the value of the "team" option.
     *
     * @return string
     */
    public function getTeam(): string
    {
        return $this->team;
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
        $team = $parser->getReader()->readUnquotedString();

        // If not inverted and there are already inverted teams, option is not applicable.

        if (!$inverted && !empty($parser->getBuilder()->getSelector()->getTeams())) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->duplicateInvertibleOption()->createWithContext($parser->getReader(), 'team');
        }

        // Mark names as having been inverted.

        if ($inverted) {

            $parser->getBuilder()->teamsInverted = true;
        }

        return new self($team, $inverted);
    }

    /**
     * Ensures that more than one team= has not been set. More than one team=! is allowed.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->teamsInverted || empty($builder->getSelector()->getTeams());
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return 3;
    }
}