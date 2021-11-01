<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

class CompoundTag implements TagInterface
{
    private $tags = [];

    public function put(string $key, TagInterface $tag): TagInterface
    {
        return $this->tags[$key] = $tag;
    }

    public function getType(): int
    {
        return self::TAG_COMPOUND;
    }

    public function toString(): string
    {
        $buffer = '{';
        $num = count($this->tags);
        $i = 0;

        /**
         * @var TagInterface $value
         */
        foreach ($this->tags as $key => $value) {

            $buffer = $buffer . '"' . addslashes($key) . '": ';
            $buffer = $buffer . $value->toString();

            if ($i + 1 != $num) {

                $buffer = $buffer . ',';
            }

            $i++;
        }

        return $buffer . '}';
    }
}