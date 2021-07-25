<?php namespace Celestriode\Mattock\Parsers\Java\NbtPath;

use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\NodeInterface;

class NbtPathContainer
{
    /**
     * @var string
     */
    private $original;

    /**
     * @var NodeInterface[]
     */
    private $nodes;

    public function __construct(string $original, NodeInterface ...$nodes)
    {
        $this->original = $original;
        $this->nodes = $nodes;
    }
}