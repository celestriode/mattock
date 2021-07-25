<?php namespace Celestriode\Mattock\Parsers\Java\Selector;

use Celestriode\Mattock\Parsers\Java\Selector\Options\AbstractOption;
use Celestriode\Mattock\Parsers\Java\Selector\Options\Advancements;
use Celestriode\Mattock\Parsers\Java\Selector\Options\GameMode;
use Celestriode\Mattock\Parsers\Java\Selector\Options\Name;
use Celestriode\Mattock\Parsers\Java\Selector\Options\Scores;
use Celestriode\Mattock\Parsers\Java\Selector\Options\Team;
use Celestriode\Mattock\Parsers\Java\Selector\Options\Type;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;
use Celestriode\Mattock\Parsers\Java\Utils\NullablePoint3D;

/**
 * A dynamic selector in Minecraft takes in a set of entities and determines which of them are valid for selection based
 * on options provided to the dynamic selector.
 *
 * For Mattock, its purpose is to build an audit-worthy result to evaluate user input and produce target sampling.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector
 */
class DynamicSelector implements SelectorInterface
{
    public const TYPE_NEAREST = 'p';
    public const TYPE_ALL = 'a';
    public const TYPE_RANDOM = 'r';
    public const TYPE_ENTITY = 'e';
    public const TYPE_SELF = 's';

    public const ORDER_ARBITRARY = 'arbitrary';
    public const ORDER_NEAREST = 'nearest';
    public const ORDER_FURTHEST = 'furthest';
    public const ORDER_RANDOM = 'random';

    private $dynamicType;
    private $includesNonPlayerEntities;
    private $includesDeadEntities;

    private $limit = 2147483647;
    private $sort = self::ORDER_ARBITRARY;
    private $worldLimited = false;

    private $distance;
    private $level;
    private $origin;
    private $deltaCoordinates;
    private $xRotation;
    private $yRotation;

    /**
     * @var AbstractOption[] A list of options that are not flexible.
     */
    private $rigidOptions = [];

    /**
     * @var AbstractOption[] A list of options that process entities in the order specified within the array.
     */
    private $flexibleOptions = [];

    public function __construct(string $dynamicType, bool $includesNonPlayerEntities, bool $includesDeadEntities)
    {
        // Set up required options.

        $this->dynamicType = $dynamicType;
        $this->includesNonPlayerEntities = $includesNonPlayerEntities;
        $this->includesDeadEntities = $includesDeadEntities;

        // Set up default options.

        $this->distance = new MinMaxBounds(null, null);
        $this->level = new MinMaxBounds(null, null);
        $this->origin = new NullablePoint3D(null, null, null);
        $this->deltaCoordinates = new NullablePoint3D(null, null, null);
        $this->xRotation = new MinMaxBounds(null, null);
        $this->yRotation = new MinMaxBounds(null, null);
    }

    /**
     * Returns the dynamic type of this selector (such as p, a, r, e, and s).
     *
     * @return string
     */
    public function getDynamicType(): string
    {
        return $this->dynamicType;
    }

    /**
     * Returns whether or not this selector will target non-player entities.
     *
     * @return bool
     */
    public function includesNonPlayerEntities(): bool
    {
        return $this->includesNonPlayerEntities;
    }

    /**
     * Marks the selector as including non-player entities.
     *
     * @param bool $bl
     */
    public function setIncludesNonPlayerEntities(bool $bl = true): void
    {
        $this->includesNonPlayerEntities = $bl;
    }

    /**
     * Returns whether or not this selector will target dead entities.
     *
     * @return bool
     */
    public function includesDeadEntities(): bool
    {
        return $this->includesDeadEntities;
    }

    /**
     * Returns whether or not the selector will only target the executor.
     *
     * @return bool
     */
    public function targetsSelf(): bool
    {
        return $this->dynamicType == self::TYPE_SELF;
    }

    /**
     * Returns the maximum number of targets the selector is allowed to target.
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Sets the maximum number of targets the selector is allowed to target.
     *
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Returns the type of sorting this selector will use.
     *
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * Sets the type of sorting this selector will use.
     *
     * @param string $sort
     */
    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * Returns the "distance" option for this selector.
     *
     * @return MinMaxBounds
     */
    public function getDistance(): MinMaxBounds
    {
        return $this->distance;
    }

    /**
     * Returns the "level" option for this selector.
     *
     * @return MinMaxBounds
     */
    public function getLevel(): MinMaxBounds
    {
        return $this->level;
    }

    /**
     * Returns the "x/y/z" option for this selector.
     *
     * @return NullablePoint3D
     */
    public function getOrigin(): NullablePoint3D
    {
        return $this->origin;
    }

    /**
     * Returns the "dx/dy/dz" options for this selector.
     *
     * @return NullablePoint3D
     */
    public function getDeltaCoordinates(): NullablePoint3D
    {
        return $this->deltaCoordinates;
    }

    /**
     * Returns the "level" option for this selector.
     *
     * @return MinMaxBounds
     */
    public function getXRotation(): MinMaxBounds
    {
        return $this->xRotation;
    }

    /**
     * Returns the "level" option for this selector.
     *
     * @return MinMaxBounds
     */
    public function getYRotation(): MinMaxBounds
    {
        return $this->yRotation;
    }

    /**
     * Marks the selector as only being able to select entities in the executor's dimension.
     *
     * @param bool $bl
     */
    public function setWorldLimited(bool $bl = true): void
    {
        $this->worldLimited = $bl;
    }

    /**
     * Returns whether or not the selector is world-limited.
     *
     * @return bool
     */
    public function isWorldLimited(): bool
    {
        return $this->worldLimited;
    }

    /**
     * Adds an option to the list of rigid options; those whose order is predefined.
     *
     * @param string $name
     * @param AbstractOption $option
     */
    public function addRigidOption(AbstractOption $option): void
    {
        $this->rigidOptions[] = $option;
    }

    /**
     * Returns all rigid options.
     *
     * @return AbstractOption[]
     */
    public function getRigidOptions(): array
    {
        return $this->rigidOptions;
    }

    /**
     * Adds an option to the list of flexible options; those whose order is defined based on their order in the array.
     *
     * @param string $name
     * @param AbstractOption $option
     */
    public function addFlexibleOption(AbstractOption $option): void
    {
        $this->flexibleOptions[] = $option;
    }

    /**
     * Returns all flexible options.
     *
     * @return AbstractOption[]
     */
    public function getFlexibleOptions(): array
    {
        return $this->flexibleOptions;
    }

    /**
     * Returns all flexible option that are of the input class.
     *
     * @param string $className
     * @return array
     */
    public function getFlexibleOptionsByClass(string $className): array
    {
        $buffer = [];

        // Cycle though flexible options and build up the buffer.

        foreach ($this->flexibleOptions as $option) {

            if ($option instanceof $className) {

                $buffer[] = $option;
            }
        }

        // Return the completed buffer.

        return $buffer;
    }

    /**
     * Returns all name options.
     *
     * @return Name[]
     */
    public function getNames(): array
    {
        return $this->getFlexibleOptionsByClass(Name::class);
    }

    /**
     * Returns all game mode options.
     *
     * @return GameMode[]
     */
    public function getGameModes(): array
    {
        return $this->getFlexibleOptionsByClass(GameMode::class);
    }

    /**
     * Returns all team options.
     *
     * @return Team[]
     */
    public function getTeams(): array
    {
        return $this->getFlexibleOptionsByClass(Team::class);
    }

    /**
     * Returns all type options.
     *
     * @return Type[]
     */
    public function getTypes(): array
    {
        return $this->getFlexibleOptionsByClass(Type::class);
    }

    /**
     * Returns all score options.
     *
     * @return Scores[]
     */
    public function getScores(): array
    {
        return $this->getFlexibleOptionsByClass(Scores::class);
    }

    /**
     * Returns all score options.
     *
     * @return Advancements[]
     */
    public function getAdvancements(): array
    {
        return $this->getFlexibleOptionsByClass(Advancements::class);
    }
}