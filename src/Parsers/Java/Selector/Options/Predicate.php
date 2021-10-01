<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\ResourceLocation;

/**
 * The "predicate" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Predicate extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var ResourceLocation The resource location of the predicate.
     */
    private $predicate;

    /**
     * @var bool Whether or not this option is inverted.
     */
    private $inverted;

    public function __construct(ResourceLocation $predicate, bool $inverted)
    {
        $this->predicate = $predicate;
        $this->inverted = $inverted;
    }

    /**
     * Returns the resource location of the predicate.
     *
     * @return ResourceLocation
     */
    public function getPredicate(): ResourceLocation
    {
        return $this->predicate;
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
        $inverted = self::shouldInvertValue($parser->getReader());
        $predicate = ResourceLocation::readLenient($parser->getReader());

        return new self($predicate, $inverted);
    }

    /**
     * Can always add more "predicate" options.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return 9;
    }
}