<?php namespace Celestriode\Mattock\Parsers\Java\NbtPath\Nodes;

class CompoundChildNode implements NodeInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}