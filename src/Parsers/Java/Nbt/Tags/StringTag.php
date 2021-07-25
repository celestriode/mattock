<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class StringTag implements TagInterface
{
    private $value = '';

    public function __construct(string $value = '')
    {
        $this->setValue($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getType(): int
    {
        return self::TAG_STRING;
    }
}