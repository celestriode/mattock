<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "limit" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Limit extends AbstractOption
{
    /**
     * @var int The value of the "limit" option.
     */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * Returns the value of the "limit" option.
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $n = $parser->getReader()->getCursor();
        $limit = $parser->getReader()->readInt();

        if ($limit < 1) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->limitOutOfRange()->createWithContext($parser->getReader(), $limit);
        }

        $parser->getBuilder()->getSelector()->setLimit($limit);
        $parser->getBuilder()->limitSpecified = true;

        return new self($limit);
    }

    /**
     * Cannot use "limit" if using @s or if "limit" was already specified.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return !$builder->getSelector()->targetsSelf() && !$builder->limitSpecified;
    }
}