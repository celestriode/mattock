<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class ListTag extends AbstractListTag
{
    private $type = 0;

    public function getType(): int
    {
        return self::TAG_LIST;
    }

    public function getListType(): int
    {
        return $this->type;
    }

    public function canAdd(TagInterface $tag): bool
    {
        if ($this->type === 0) {

            $this->type = $tag->getType();
        }

        return parent::canAdd($tag);
    }
}