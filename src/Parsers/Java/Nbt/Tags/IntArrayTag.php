<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class IntArrayTag extends AbstractArrayTag
{
    public function getType(): int
    {
        return self::TAG_INT_ARRAY;
    }

    public function getListType(): int
    {
        return self::TAG_INT;
    }
}