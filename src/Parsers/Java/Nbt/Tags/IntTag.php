<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;

class IntTag extends AbstractNumberTag
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

        if ($value < -2147483648 || $value > 2147483647) {

            throw new MattockException('Integer should not be set out of -2147483648 to 2147483647 range');
        }

        return $value;
    }

    public function getType(): int
    {
        return self::TAG_INT;
    }
}