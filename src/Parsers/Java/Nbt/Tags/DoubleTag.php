<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;

class DoubleTag extends AbstractNumberTag
{
    /**
     * Changes the value into the correct datatype.
     *
     * @param mixed $value The value to change, usually being a string.
     * @return int|float
     * @throws MattockException
     */
    public function parseValue($value)
    {
        if (!is_numeric($value)) {

            throw new MattockException('Value must be numeric');
        }

        return (double)$value;
    }

    public function getType(): int
    {
        return self::TAG_DOUBLE;
    }

    public function toString(): string
    {
        return parent::toString() . 'd';
    }
}