<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;

class ShortTag extends AbstractNumberTag
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

        $value = (int)$value;

        if ($value < -32768 || $value > 32767) {

            throw new MattockException('Integer should not be set out of -32768 to 32767 range');
        }

        return $value;
    }

    public function getType(): int
    {
        return self::TAG_SHORT;
    }

    public function toString(): string
    {
        return parent::toString() . 's';
    }
}