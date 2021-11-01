<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Parsers\Java\Nbt\TagUtils;

abstract class AbstractListTag implements TagInterface
{
    private $values = [];

    abstract public function getListType(): int;

    /**
     * @throws MattockException
     */
    public function __construct(TagInterface ...$tags)
    {
        for ($i = 0, $j = count($tags); $i < $j; $i++) {

            $this->add($tags[$i]);
        }
    }

    /**
     * @throws MattockException
     */
    public function add(TagInterface $tag): TagInterface
    {
        if (!$this->canAdd($tag)) {

            throw new MattockException('Cannot add tag of type ' . TagUtils::convertToString($tag->getType()) . ' to list of type ' . TagUtils::convertToString($this->getListType()));
        }

        $this->values[] = $tag;

        return $tag;
    }

    public function canAdd(TagInterface $tag): bool
    {
        return $this->getListType() === $tag->getType();
    }

    protected function packListToString(): string
    {
        $buffer = '';

        for ($i = 0, $j = count($this->values); $i < $j; $i++) {

            $buffer = $buffer . $this->values[$i]->toString();

            if ($i + 1 != $j) {

                $buffer = $buffer . ',';
            }
        }

        return $buffer;
    }

    public function toString(): string
    {
        return '[' . $this->packListToString() . ']';
    }
}