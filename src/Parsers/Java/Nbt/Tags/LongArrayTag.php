<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class LongArrayTag extends AbstractArrayTag
{
    public function getType(): int
    {
        return self::TAG_LONG_ARRAY;
    }

    public function getListType(): int
    {
        return self::TAG_LONG;
    }

    public function toString(): string
    {
        return '[L;' . $this->packListToString() . ']';
    }
}