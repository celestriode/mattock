<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;

class LongTag extends AbstractNumberTag
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

        if (bccomp($value, '9223372036854775807', 0) === 1 || bccomp('-9223372036854775808', $value, 0) === 1) { // TODO: make sure this works

            throw new MattockException('Long should not be set out of -9223372036854775808 to 9223372036854775807 range');
        }

        return (int)$value;
    }

    public function getType(): int
    {
        return self::TAG_LONG;
    }
}