<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;

class ByteTag extends AbstractNumberTag
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

        if ($value < -128 || $value > 127) {

            throw new MattockException('Byte should not be set out of -128 to 127 range');
        }

        return $value;
    }

    public function getType(): int
    {
        return self::TAG_BYTE;
    }

    public function toString(): string
    {
        return parent::toString() . 'b';
    }
}