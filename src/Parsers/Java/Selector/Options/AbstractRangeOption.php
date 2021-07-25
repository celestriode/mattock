<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;

/**
 * Provides methods for options that are specifically min/max ranges.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
abstract class AbstractRangeOption extends AbstractOption
{
    protected static function isNegative(MinMaxBounds $bounds): bool
    {
        return (
            ($bounds->getMin() !== null && $bounds->getMin() < 0.0)
            || ($bounds->getMax() !== null && $bounds->getMax() < 0.0)
        );
    }
}