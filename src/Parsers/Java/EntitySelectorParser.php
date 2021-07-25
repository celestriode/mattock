<?php namespace Celestriode\Mattock\Parsers\Java;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\Selector\DirectSelector;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelector;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Selector\Options\FlexibleOptionInterface;
use Celestriode\Mattock\Parsers\Java\Selector\PlayerSelector;
use Celestriode\Mattock\Parsers\Java\Selector\SelectorInterface;
use Celestriode\Mattock\Parsers\Java\Utils\MinecraftUuid;
use Celestriode\Mattock\Parsers\Java\Selector\Options\AbstractOption;

/**
 * Parses entity selectors in Java edition, throwing exceptions for any syntax errors.
 *
 * Entity selectors include player names, entity UUIDs, and dynamic selectors.
 *
 * @package Celestriode\Mattock\Parsers\Java
 */
class EntitySelectorParser
{
    /**
     * @var StringReader The string reader being used to parse an entity selector.
     */
    private $reader;

    /**
     * @var bool Whether or not dynamic selectors should be allowed, meaning only names and UUIDs can be used.
     */
    private $allowSelectors;

    /**
     * @var DynamicSelectorBuilder|null The selector builder that holds all the selector options.
     */
    private $builder;

    public function __construct(StringReader $reader, bool $allowSelectors = true)
    {
        $this->reader = $reader;
        $this->allowSelectors = $allowSelectors;
    }

    /**
     * Returns the selector builder that will hold build options obtained from parsing.
     *
     * @return DynamicSelectorBuilder|null
     */
    public function getBuilder(): ?DynamicSelectorBuilder
    {
        return $this->builder;
    }

    /**
     * Returns the reader at its current position. Use with dynamic selector options to parse input.
     *
     * @return StringReader
     */
    public function getReader(): StringReader
    {
        return $this->reader;
    }

    /**
     * Parses the raw input provided. Returns an entity selector if there were no issues.
     *
     * The types of entity selectors returned can include a player name, an entity UUID, or a dynamic selector.
     *
     * @return SelectorInterface
     * @throws CommandSyntaxException
     * @throws MattockException
     */
    public function parse(): SelectorInterface
    {
        // If a dynamic selector is defined, parse it.

        if ($this->reader->canRead() && $this->reader->peek() == '@') {

            // If dynamic selectors are not allowed, throw error.

            if (!$this->allowSelectors) {

                throw SelectorException::getBuiltInExceptions()->selectorsNotAllowed()->createWithContext($this->reader);
            }

            // Otherwise parse and return the selector.

            $this->reader->skip();
            $this->parseDynamicSelector();

            return $this->finalizeDynamicSelector();
        }

        // If it's not a dynamic selector, attempt to parse it as a player name or entity UUID.

        return $this->parseNameOrUuid();
    }

    /**
     * Parse the input as a dynamic selector.
     *
     * @throws CommandSyntaxException
     * @throws MattockException
     */
    protected function parseDynamicSelector(): void
    {
        // If there's nothing after the @, throw an error.

        if (!$this->reader->canRead()) {

            throw SelectorException::getBuiltInExceptions()->missingSelectorType()->createWithContext($this->reader);
        }

        $n = $this->reader->getCursor();
        $char = $this->reader->read();

        // Check the selector type.

        switch ($char) {

            case DynamicSelector::TYPE_NEAREST:

                $this->builder = new DynamicSelectorBuilder(new DynamicSelector(DynamicSelector::TYPE_NEAREST, false, true));

                $this->getBuilder()->getSelector()->setLimit(1);
                $this->getBuilder()->getSelector()->setSort(DynamicSelector::ORDER_NEAREST);
                break;
            case DynamicSelector::TYPE_ALL:

                $this->builder = new DynamicSelectorBuilder(new DynamicSelector(DynamicSelector::TYPE_ALL, false, true));

                $this->getBuilder()->getSelector()->setLimit(2147483647);
                $this->getBuilder()->getSelector()->setSort(DynamicSelector::ORDER_ARBITRARY);
                break;
            case DynamicSelector::TYPE_RANDOM:

                $this->builder = new DynamicSelectorBuilder(new DynamicSelector(DynamicSelector::TYPE_RANDOM, false, true));

                $this->getBuilder()->getSelector()->setLimit(1);
                $this->getBuilder()->getSelector()->setSort(DynamicSelector::ORDER_RANDOM);
                break;
            case DynamicSelector::TYPE_SELF:

                $this->builder = new DynamicSelectorBuilder(new DynamicSelector(DynamicSelector::TYPE_SELF, true, true));

                $this->getBuilder()->getSelector()->setLimit(1);
                $this->getBuilder()->getSelector()->setSort(DynamicSelector::ORDER_ARBITRARY);
                break;
            case DynamicSelector::TYPE_ENTITY:

                $this->builder = new DynamicSelectorBuilder(new DynamicSelector(DynamicSelector::TYPE_ENTITY, true, false));

                $this->getBuilder()->getSelector()->setLimit(2147483647);
                $this->getBuilder()->getSelector()->setSort(DynamicSelector::ORDER_ARBITRARY);
                break;
            default:

                $this->reader->setCursor($n);
                throw SelectorException::getBuiltInExceptions()->unknownSelectorType()->createWithContext($this->reader, $char);
        }

        // If there is an opening square bracket, then options are specified.

        if ($this->reader->canRead() && $this->reader->peek() == '[') {

            $this->reader->skip();
            $this->parseOptions();
        }
    }

    /**
     * @throws MattockException
     * @throws CommandSyntaxException
     */
    public function parseOptions()
    {
        $this->reader->skipWhitespace();

        // Loop until there's a closing square bracket or no more content.

        while ($this->reader->canRead() && $this->reader->peek() != ']') {

            $this->reader->skipWhitespace();

            // Get the name of the option.

            $name = $this->reader->readString();

            // Get the option parser based on the name.

            /**
             * @var AbstractOption $optionClass
             */
            $optionClass = AbstractOption::getClassName($this, $name);

            // If the next character isn't an equals sign, then the option value is missing.

            $this->reader->skipWhitespace();

            if (!$this->reader->canRead() || $this->reader->peek() != '=') {

                throw SelectorException::getBuiltInExceptions()->missingOptionValue()->createWithContext($this->reader, $name);
            }

            // Skip past the equals sign.

            $this->reader->skip();
            $this->reader->skipWhitespace();

            // Have the option parse the value.

            $option = $optionClass::handle($this);

            // Store based on whether or not it's flexible.

            if ($option instanceof FlexibleOptionInterface) {

                $this->getBuilder()->getSelector()->addFlexibleOption($option);
            } else {

                $this->getBuilder()->getSelector()->addRigidOption($option);
            }

            // If there's nothing left, the option was too greedy.

            $this->reader->skipWhitespace();

            if (!$this->reader->canRead()) {

                continue;
            }

            // If the next character is a comma, skip it and check for an option name.

            if ($this->reader->peek() == ',') {

                $this->reader->skip();

                continue;
            }

            // If instead the next character is a closing square bracket, that's the end of the loop.

            if ($this->reader->peek() == ']') {

                break;
            }

            // However, if there is still content that the current option did not handle, then throw an error.

            throw new MattockException('Option "' . $name . '" succeeded but did not use the whole value as it should have');
        }

        // If there was no more content (AKA the next character that should be ] doesn't exist), throw error.

        if (!$this->reader->canRead()) {

            throw SelectorException::getBuiltInExceptions()->missingEndOfOptions()->createWithContext($this->reader);
        }

        // Skip past the ].

        $this->reader->skip();
    }

    /**
     * Attempt to parse the input as a player name or entity UUID.
     *
     * @throws CommandSyntaxException
     */
    protected function parseNameOrUuid(): SelectorInterface
    {
        $string = $this->reader->readString();

        // If the string is a valid UUID, use it.

        if (MinecraftUuid::valid($string)) {

            return new DirectSelector(MinecraftUuid::normalize($string));
        }

        // Otherwise attempt to parse it as a player name. If it's invalid, throw an error.

        if (empty($string) || strlen($string) > 16) {

            throw SelectorException::getBuiltInExceptions()->invalidNameOrUuid()->createWithContext($this->reader, $string);
        }

        return new PlayerSelector($string);
    }

    /**
     * Creates a new entity selector based on the results of parsing.
     *
     * @return SelectorInterface
     */
    public function finalizeDynamicSelector(): SelectorInterface
    {
        return $this->getBuilder()->getSelector();
    }
}