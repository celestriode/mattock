<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

/**
 * Representation of a 3-dimensional point in Minecraft. Values are required.
 *
 * @package Celestriode\Mattock\Parsers\Java\Utils
 */
class Point3D extends NullablePoint3D
{
    public function __construct(float $x, float $y, float $z)
    {
        parent::__construct($x, $y, $z);
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function getZ(): float
    {
        return $this->z;
    }
}