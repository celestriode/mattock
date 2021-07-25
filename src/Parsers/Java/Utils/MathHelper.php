<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

/**
 * Quick helper class for mathematical functions.
 *
 * @package Celestriode\Mattock\Parsers\Java\Utils
 */
class MathHelper
{
    /**
     * Ensures a degree is between -180 and 180.
     *
     * @param float $degree
     * @return float
     */
    public static function wrapDegree(float $degree): float
    {
        $wrapped = $degree % 360.0;

        if ($wrapped >= 180.0) {

            $wrapped -= 360.0;
        }

        if ($wrapped < -180) {

            $wrapped += 360.0;
        }

        return $wrapped;
    }

    /**
     * Ensures the degrees within a min/max boundary are between -180 and 180.
     *
     * @param MinMaxBounds $bounds
     */
    public static function wrapDegrees(MinMaxBounds $bounds): void
    {
        if ($bounds->getMin() !== null) {

            $bounds->setMin(static::wrapDegree($bounds->getMin()));
        }

        if ($bounds->getMax() !== null) {

            $bounds->setMax(static::wrapDegree($bounds->getMax()));
        }
    }
}