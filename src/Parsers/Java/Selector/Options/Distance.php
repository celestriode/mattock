<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Utils\UtilsException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;

/**
 * The "distance" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Distance extends AbstractRangeOption
{
    /**
     * @var MinMaxBounds The boundaries of the distance.
     */
    private $bounds;

    public function __construct(MinMaxBounds $bounds)
    {
        $this->bounds = $bounds;
    }

    /**
     * Returns the boundaries of the distance.
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
        $bounds = MinMaxBounds::fromReader($parser->getReader());

        if (self::isNegative($bounds)) {

            throw UtilsException::getBuiltInExceptions()->negativeBounds()->createWithContext($parser->getReader(), $bounds->toString());
        }

        $parser->getBuilder()->getSelector()->getDistance()->setFromBounds($bounds);
        $parser->getBuilder()->getSelector()->setWorldLimited();

        return new self($bounds);
    }

    /**
     * Can only use a distance if it hasn't been defined yet.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->getSelector()->getDistance()->isAny();
    }
}