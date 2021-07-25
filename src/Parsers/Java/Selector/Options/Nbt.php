<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Nbt\Tags\CompoundTag;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\StringifiedNbtParser;

/**
 * The "nbt" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Nbt extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var CompoundTag The value of the "tag" option.
     */
    private $nbt;

    /**
     * @var bool Whether or not this option is inverted.
     */
    private $inverted;

    public function __construct(CompoundTag $nbt, bool $inverted)
    {
        $this->nbt = $nbt;
        $this->inverted = $inverted;
    }

    /**
     * Returns the value of the "tag" option.
     *
     * @return CompoundTag
     */
    public function getNbt(): CompoundTag
    {
        return $this->nbt;
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
     * @throws MattockException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $inverted = self::shouldInvertValue($parser->getReader());
        $nbt = (new StringifiedNbtParser($parser->getReader()))->parseCompoundTag();

        return new self($nbt, $inverted);
    }

    /**
     * Can always add more NBT.
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
        return 10;
    }
}