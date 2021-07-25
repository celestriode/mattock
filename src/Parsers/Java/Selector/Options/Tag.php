<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "tag" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Tag extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var string The value of the "tag" option.
     */
    private $tag;

    /**
     * @var bool Whether or not this option is inverted.
     */
    private $inverted;

    public function __construct(string $tag, bool $inverted)
    {
        $this->tag = $tag;
        $this->inverted = $inverted;
    }

    /**
     * Returns the value of the "tag" option.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
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
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $inverted = self::shouldInvertValue($parser->getReader());
        $tag = $parser->getReader()->readUnquotedString();

        return new self($tag, $inverted);
    }

    /**
     * Can always add more tags.
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
        return 5;
    }
}