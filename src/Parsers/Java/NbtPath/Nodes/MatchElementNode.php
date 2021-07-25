<?php namespace Celestriode\Mattock\Parsers\Java\NbtPath\Nodes;

use Celestriode\Mattock\Parsers\Java\Nbt\Tags\CompoundTag;

class MatchElementNode implements NodeInterface
{
    /**
     * @var CompoundTag
     */
    private $pattern;

    public function __construct(CompoundTag $compoundTag)
    {
        $this->pattern = $compoundTag;
        //$this->predicate = NbtPathReader::createTagPredicate($compoundTag);
    }
}