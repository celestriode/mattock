<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;

/**
 * The "name" option for dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Name extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var string The name of the entity that should (or shouldn't) be selected.
     */
    private $name;

    /**
     * @var bool Whether or not to invert this option.
     */
    private $inverted;

    public function __construct(string $name, bool $inverted)
    {
        $this->name = $name;
        $this->inverted = $inverted;
    }

    /**
     * Returns the name of the entity that should (or shouldn't) be selected.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * If inverted, the selected entity cannot have the specified name.
     *
     * @return bool
     */
    public function inverted(): bool
    {
        return $this->inverted;
    }

    /**
     * @param EntitySelectorParser $parser
     * @return AbstractOption
     * @throws CommandSyntaxException
     */
    public static function handle(EntitySelectorParser $parser): AbstractOption
    {
        $n = $parser->getReader()->getCursor();
        $inverted = self::shouldInvertValue($parser->getReader());
        $name = $parser->getReader()->readString();

        // If not inverted and there are already inverted names, option is not applicable.

        if (!$inverted && !empty($parser->getBuilder()->getSelector()->getNames())) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->duplicateInvertibleOption()->createWithContext($parser->getReader(), 'name');
        }

        // Mark names as having been inverted.

        if ($inverted) {

            $parser->getBuilder()->namesInverted = true;
        }

        return new self($name, $inverted);
    }

    /**
     * Ensures that more than one name= has not been set. More than one name=! is allowed.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->namesInverted || empty($builder->getSelector()->getNames());
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return 3;
    }
}