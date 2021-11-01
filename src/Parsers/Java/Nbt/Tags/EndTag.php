<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class EndTag implements TagInterface
{
    /**
     * Changes the value into the correct datatype.
     *
     * @param mixed $value The value to change, usually being a string.
     * @return mixed
     */
    public function parseValue($value)
    {
        return $value;
    }

    public function getType(): int
    {
        return self::TAG_END;
    }

    public function toString(): string
    {
        return '';
    }
}