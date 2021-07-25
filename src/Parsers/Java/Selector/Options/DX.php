<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "dx" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class DX extends AbstractOption
{
    /**
     * @var float The value of this coordinate.
     */
    private $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value of this coordinate.
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $dx = $parser->getReader()->readDouble();

        $parser->getBuilder()->getSelector()->getDeltaCoordinates()->setX($dx);
        $parser->getBuilder()->getSelector()->setWorldLimited(true);

        return new self($dx);
    }

    /**
     * Can only specify a DX coordinate if it wasn't already specified.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->getSelector()->getDeltaCoordinates()->getX() === null;
    }
}