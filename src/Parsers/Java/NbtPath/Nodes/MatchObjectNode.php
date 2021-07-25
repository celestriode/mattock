<?php namespace Celestriode\Mattock\Parsers\Java\NbtPath\Nodes;

use Celestriode\Mattock\Parsers\Java\Nbt\Tags\CompoundTag;

class MatchObjectNode implements NodeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var CompoundTag
     */
    private $pattern;

    public function __construct(string $name, CompoundTag $compoundTag)
    {
        $this->name = $name;
        $this->pattern = $compoundTag;
        //$this->predicate = NbtPathReader::createTagPredicate($compoundTag);
    }
}