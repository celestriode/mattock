<?php namespace Celestriode\Mattock\Parsers\Java;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Exceptions\Nbt\NbtException;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\AllElementsNode;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\CompoundChildNode;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\IndexedElementNode;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\MatchElementNode;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\MatchObjectNode;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\MatchRootObjectNode;
use Celestriode\Mattock\Parsers\Java\NbtPath\Nodes\NodeInterface;
use Celestriode\Mattock\Parsers\Java\NbtPath\NbtPathContainer;

class NbtPathParser
{
    private $reader;

    public function __construct(StringReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @throws MattockException
     * @throws CommandSyntaxException
     */
    public static function parse(string $string): NbtPathContainer
    {
        $nbtReader = new self(new StringReader($string));

        return $nbtReader->parsePath();
    }

    /**
     * @throws CommandSyntaxException|MattockException
     */
    public function parsePath(): NbtPathContainer
    {
        $list = [];
        $n = $this->reader->getCursor();
        $bl = true;

        while ($this->reader->canRead() && $this->reader->peek() != ' ') {

            $node = $this->parseNode($bl);
            $list[] = $node;
            $bl = false;

            if (
                !$this->reader->canRead()
                || ($char = $this->reader->peek()) == ' '
                || $char == '['
                || $char == '{'
            ) {

                continue;
            }

            $this->reader->expect('.');
        }

        return new NbtPathContainer(mb_substr($this->reader->getString(), $n, $this->reader->getCursor() - $n), ...$list);
    }

    /**
     * @throws MattockException
     * @throws CommandSyntaxException
     */
    protected function parseNode(bool $bl): NodeInterface
    {
        switch ($this->reader->peek()) {

            case '{':
                if (!$bl) {

                    throw NbtException::getBuiltInExceptions()->invalidNode()->createWithContext($this->reader);
                }

                $compoundTag = (new StringifiedNbtParser($this->reader))->parseCompoundTag();

                return new MatchRootObjectNode($compoundTag);
            case '[':
                $this->reader->skip();
                $char = $this->reader->peek();

                if ($char == '{') {

                    $compoundTag = (new StringifiedNbtParser($this->reader))->parseCompoundTag();
                    $this->reader->expect(']');

                    return new MatchElementNode($compoundTag);
                }

                if ($char == ']') {

                    $this->reader->skip();

                    return new AllElementsNode(); // TODO: singleton.
                }

                $n = $this->reader->readInt();
                $this->reader->expect(']');
                return new IndexedElementNode($n);
            case '"':
                $string = $this->reader->readString();
                return $this->readObjectNode($string);
        }

        $string = $this->readUnquotedName();

        return $this->readObjectNode($string);
    }

    /**
     * @throws MattockException
     * @throws CommandSyntaxException
     */
    private function readObjectNode(string $string): NodeInterface
    {
        if ($this->reader->canRead() && $this->reader->peek() == '{') {

            $compoundTag = (new StringifiedNbtParser($this->reader))->parseCompoundTag();

            return new MatchObjectNode($string, $compoundTag);
        }

        return new CompoundChildNode($string);
    }

    /**
     * @throws CommandSyntaxException
     */
    private function readUnquotedName(): string
    {
        $n = $this->reader->getCursor();

        while ($this->reader->canRead() && $this->isAllowedInUnquotedNames($this->reader->peek())) {

            $this->reader->skip();
        }

        if ($this->reader->getCursor() == $n) {

            throw NbtException::getBuiltInExceptions()->invalidNode()->createWithContext($this->reader);
        }

        return mb_substr($this->reader->getString(), $n, $this->reader->getCursor() - $n);
    }

    private function isAllowedInUnquotedNames(string $char): bool
    {
        return (
            $char != ' '
            && $char != '"'
            && $char != '['
            && $char != ']'
            && $char != '.'
            && $char != '{'
            && $char != '}'
        );
    }
}