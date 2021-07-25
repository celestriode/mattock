<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Mattock\Exceptions\Selector\SelectorException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;
use Celestriode\Mattock\Parsers\Java\Selector\DynamicSelectorBuilder;
use Celestriode\Mattock\Parsers\Java\Utils\PopulationValidatorInterface;
use Celestriode\Mattock\Parsers\Java\Utils\ResourceLocation;

/**
 * The "type" option. Valid entity types and tags are left blank, to be filled based on a validator function provided.
 * This allows the option's values to be dynamically loaded through some condition (such as the Minecraft version). Use
 * the setTypeValidator() or setEntityTagValidator() methods to supply a validation function.
 *
 * TODO: don't use populators here. Basic structure is fine to verify, but not values. Save that for audits.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector\Options
 */
class Type extends AbstractOption implements FlexibleOptionInterface
{
    /**
     * @var PopulationValidatorInterface|null Validator for types.
     */
    protected static $typeValidator;

    /**
     * @var PopulationValidatorInterface|null Validator for entity tags.
     */
    protected static $entityTagValidator;

    /**
     * @var ResourceLocation The resource location of the "type" option.
     */
    private $resourceLocation;

    /**
     * @var bool Whether or not this option is inverted.
     */
    private $inverted;

    /**
     * @var bool Whether or not this option uses an entity tag.
     */
    private $isEntityTag;

    public function __construct(ResourceLocation $resourceLocation, bool $inverted, bool $isEntityTag)
    {
        $this->resourceLocation = $resourceLocation;
        $this->inverted = $inverted;
        $this->isEntityTag = $isEntityTag;
    }

    /**
     * Returns the resource location of the entity type or tag.
     *
     * @return ResourceLocation
     */
    public function getResourceLocation(): ResourceLocation
    {
        return $this->resourceLocation;
    }

    /**
     * Returns whether or not this option is inverted.
     *
     * @return bool
     */
    public function inverted(): bool
    {
        return $this->inverted;
    }

    /**
     * Returns whether or not the option uses an entity tag.
     *
     * @return bool
     */
    public function isEntityTag(): bool
    {
        return $this->isEntityTag;
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

        // Ensure the option is not a duplicate.

        if (!$inverted && $parser->getBuilder()->typesInverted) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->duplicateInvertibleOption()->createWithContext($parser->getReader(), 'type');
        }

        // Marks types as being inverted.

        if ($inverted) {

            $parser->getBuilder()->typesInverted = true;
        }

        // If the value is an entity tag...

        /** @noinspection PhpIfWithCommonPartsInspection */
        if (self::isTag($parser->getReader())) {

            $resourceLocation = ResourceLocation::read($parser->getReader());

            // Validate the entity tag.

            if (self::$entityTagValidator === null || !self::$entityTagValidator->validateFromPopulation($resourceLocation->toString())) {

                $parser->getReader()->setCursor($n);

                throw SelectorException::getBuiltInExceptions()->unknownEntityTag()->createWithContext($parser->getReader(), $resourceLocation->toString());
            }

            // Return the completed type.

            return new self($resourceLocation, $inverted, true);
        }

        $resourceLocation = ResourceLocation::read($parser->getReader());

        // Validate the entity type.

        if (self::$typeValidator === null || !self::$typeValidator->validateFromPopulation($resourceLocation->toString())) {

            $parser->getReader()->setCursor($n);

            throw SelectorException::getBuiltInExceptions()->unknownEntityType()->createWithContext($parser->getReader(), $resourceLocation->toString());
        }

        // Restrict the selector to players only if specified.

        if ($resourceLocation->matches('minecraft:player')) {

            $parser->getBuilder()->getSelector()->setIncludesNonPlayerEntities(false);
        }

        // Return the completed type.

        return new self($resourceLocation, $inverted, false);
    }

    /**
     * Ensures that more than one type= has not been set. More than one type=! is allowed.
     *
     * @param DynamicSelectorBuilder $builder
     * @return bool
     */
    public static function test(DynamicSelectorBuilder $builder): bool
    {
        return $builder->typesInverted || empty($builder->getSelector()->getTypes());
    }

    /**
     * Sets the entity type validator for the "type" option.
     *
     * @param PopulationValidatorInterface $validator
     */
    public static function setTypeValidator(PopulationValidatorInterface $validator): void
    {
        self::$typeValidator = $validator;
    }

    /**
     * Sets the entity tag validator for the "type" option.
     *
     * @param PopulationValidatorInterface $validator
     */
    public static function setEntityTagValidator(PopulationValidatorInterface $validator): void
    {
        self::$entityTagValidator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return ($this->inverted()) ? 4 : 1;
    }
}