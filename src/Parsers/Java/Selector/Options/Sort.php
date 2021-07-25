<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelector;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "sort" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Sort extends AbstractOption
{
    /**
     * @var string The order that entities will be sorted in.
     */
    private $order;

    public function __construct(string $order)
    {
        $this->order = $order;
    }

    /**
     * Returns the order that entities will be sorted in.
     *
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * Processes the value of "sort".
     *
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws \Exception
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $n = $parser->getReader()->getCursor();
        $sort = $parser->getReader()->readUnquotedString();

        if (
            $sort != DynamicSelector::ORDER_ARBITRARY
            && $sort != DynamicSelector::ORDER_NEAREST
            && $sort != DynamicSelector::ORDER_FURTHEST
            && $sort != DynamicSelector::ORDER_RANDOM
        ) {
            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->unknownSort()->createWithContext($parser->getReader(), $sort);
        }

        $parser->getBuilder()->getSelector()->setSort($sort);
        $parser->getBuilder()->sortSpecified = true;

        return new self($sort);
    }

    /**
     * Determines whether the "sort" option is applicable.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return !$builder->getSelector()->targetsSelf() && !$builder->sortSpecified;
    }
}