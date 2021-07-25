<?php namespace Celestriode\Mattock\Parsers\Java\NbtPath\Nodes;

use Celestriode\Mattock\Parsers\Java\Nbt\Tags\CompoundTag;

class MatchRootObjectNode implements NodeInterface
{
    public function __construct(CompoundTag $compoundTag)
    {
        //$this->predicate = NbtPathReader::createTagPredicate($compoundTag);
    }
}