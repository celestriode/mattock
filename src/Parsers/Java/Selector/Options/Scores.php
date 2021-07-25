<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;

/**
 * The "scores" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Scores extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var array The scores for this option.
     */
    private $scores;

    public function __construct(array $scores)
    {
        $this->scores = $scores;
    }

    /**
     * Returns the scores for this option.
     *
     * Structure is: ['objective_name' => MinMaxBounds]
     *
     * @return array
     */
    public function getScores(): array
    {
        return $this->scores;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $reader = $parser->getReader();
        $scores = [];
        
        $reader->expect('{');
        $reader->skipWhitespace();

        // Cycle through characters until the closing bracket is found.
        
        while ($reader->canRead() && $reader->peek() != '}') {
            
            $reader->skipWhitespace();

            // Get the objective name.

            $objective = $reader->readUnquotedString();

            // If the objective was already specified, throw error.

            if (array_key_exists($objective, $scores)) {

                throw SelectorException::getBuiltInExceptions()->duplicateObjective()->createWithContext($reader, $objective);
            }

            $reader->skipWhitespace();
            $reader->expect('=');
            $reader->skipWhitespace();

            // Get the score range.

            $bounds = MinMaxBounds::fromReader($reader);

            // Add the range to the objective.

            $scores[$objective] = $bounds;

            $reader->skipWhitespace();

            if ($reader->canRead() && $reader->peek() == ',') {

                $reader->skip();
            }
        }

        // Expect the closing bracket and return the finished option.

        $reader->expect('}');

        return new self($scores);
    }

    /**
     * Can only use one "scores" option.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return empty($builder->getSelector()->getScores());
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return 7;
    }
}