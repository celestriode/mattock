<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

/**
 * Representation of a 3-dimensional point in Minecraft. Values are optional.
 *
 * @package Celestriode\Mattock\Parsers\Java\Utils
 */
class NullablePoint3D
{
    protected $x;
    protected $y;
    protected $z;

    public function __construct(?float $x = null, ?float $y = null, ?float $z = null)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function getZ(): ?float
    {
        return $this->z;
    }

    public function setX(float $x): void
    {
        $this->x = $x;
    }

    public function setY(float $y): void
    {
        $this->y = $y;
    }

    public function setZ(float $z): void
    {
        $this->z = $z;
    }
}