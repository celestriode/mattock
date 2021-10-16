<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\ResourceLocation;

/**
 * The "advancements" option for dynamic selectors.
 *
 * TODO: validate advancements.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Advancements extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var array The advancements for this option.
     */
    private $advancements;

    public function __construct(array $advancements)
    {
        $this->advancements = $advancements;
    }

    /**
     * Returns the advancements for this option.
     *
     * @return array
     */
    public function getAdvancements(): array
    {
        return $this->advancements;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $reader = $parser->getReader();
        $advancements = [];
        $reader->expect('{');
        $reader->skipWhitespace();

        // Cycle through all characters until the closing curly bracket.

        while ($reader->canRead() && $reader->peek() != '}') {

            $reader->skipWhitespace();
            $advancement = ResourceLocation::readLenient($reader);
            $reader->skipWhitespace();
            $reader->expect('=');
            $reader->skipWhitespace();

            if ($reader->canRead() && $reader->peek() == '{') {

                $criteria = [];
                $reader->skipWhitespace();
                $reader->expect('{');
                $reader->skipWhitespace();

                while ($reader->canRead() && $reader->peek() != '}') {

                    $reader->skipWhitespace();
                    $criterion = $reader->readUnquotedString();
                    $reader->skipWhitespace();
                    $reader->expect('=');
                    $reader->skipWhitespace();

                    $bl = $reader->readBoolean();

                    $criteria[$criterion] = $bl;

                    $reader->skipWhitespace();

                    if ($reader->canRead() && $reader->peek() == ',') {

                        $reader->skip();
                    }
                }

                $reader->skipWhitespace();
                $reader->expect('}');
                $reader->skipWhitespace();

                $advancements[$advancement->toString()] = $criteria;
            } else {

                $bl = $reader->readBoolean();

                $advancements[$advancement->toString()] = $bl;
            }

            $reader->skipWhitespace();

            if ($reader->canRead() && $reader->peek() == ',') {

                $reader->skip();
            }
        }

        // Expect the closing bracket and return the completed option.

        $reader->expect('}');

        return new self($advancements);
    }

    /**
     * Can only have one "advancements" option specified.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return empty($builder->getSelector()->getAdvancements());
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return 8;
    }
}