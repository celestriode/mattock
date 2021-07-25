<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "z" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Z extends AbstractOption
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
        $z = $parser->getReader()->readDouble();

        $parser->getBuilder()->getSelector()->getOrigin()->setZ($z);
        $parser->getBuilder()->getSelector()->setWorldLimited(true);

        return new self($z);
    }

    /**
     * Can only specify a Z coordinate if it wasn't already specified.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->getSelector()->getOrigin()->getZ() === null;
    }
}