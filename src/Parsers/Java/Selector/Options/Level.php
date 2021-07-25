<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Utils\UtilsException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;

/**
 * The "level" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Level extends AbstractRangeOption
{
    /**
     * @var MinMaxBounds The boundaries of the level.
     */
    private $bounds;

    public function __construct(MinMaxBounds $bounds)
    {
        $this->bounds = $bounds;
    }

    /**
     * Returns the boundaries of the level.
     *
     * @return MinMaxBounds
     */
    public function getBounds(): MinMaxBounds
    {
        return $this->bounds;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $bounds = MinMaxBounds::fromReader($parser->getReader(), true);

        if (self::isNegative($bounds)) {

            throw UtilsException::getBuiltInExceptions()->negativeBounds()->createWithContext($parser->getReader(), $bounds->toString());
        }

        $parser->getBuilder()->getSelector()->getLevel()->setFromBounds($bounds);
        $parser->getBuilder()->getSelector()->setIncludesNonPlayerEntities(false);

        return new self($bounds);
    }

    /**
     * Can only specify a level if it hasn't already been specified.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->getSelector()->getLevel()->isAny();
    }
}