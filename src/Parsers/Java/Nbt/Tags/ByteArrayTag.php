<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class ByteArrayTag extends AbstractArrayTag
{
    public function getType(): int
    {
        return self::TAG_BYTE_ARRAY;
    }

    public function getListType(): int
    {
        return self::TAG_BYTE;
    }
}