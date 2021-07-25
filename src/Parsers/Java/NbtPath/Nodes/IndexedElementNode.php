<?php namespace Celestriode\Mattock\Parsers\Java\NbtPath\Nodes;

class IndexedElementNode implements NodeInterface
{
    /**
     * @var int
     */
    private $index;

    public function __construct(int $index)
    {
        $this->index = $index;
    }
}