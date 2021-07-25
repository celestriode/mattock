<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\MathHelper;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;

/**
 * The "x_rotation" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class XRotation extends AbstractOption
{
    /**
     * @var MinMaxBounds The boundaries of the rotation.
     */
    private $bounds;

    public function __construct(MinMaxBounds $bounds)
    {
        $this->bounds = $bounds;
    }

    /**
     * Returns the boundaries of the rotation.
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
        $bounds = MinMaxBounds::fromReader($parser->getReader(), false, false);
        MathHelper::wrapDegrees($bounds);

        $parser->getBuilder()->getSelector()->getXRotation()->setFromBounds($bounds);

        return new self($bounds);
    }

    /**
     * Can only specify an X rotation if it hasn't already been specified.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->getSelector()->getXRotation()->isAny();
    }
}