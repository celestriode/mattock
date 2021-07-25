<?php namespace Celestriode\Mattock\Parsers\Java\Selector;

use Ramsey\Uuid\UuidInterface;

/**
 * A direct selector uses an entity UUID for target selection.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector
 */
class DirectSelector implements SelectorInterface
{
    /**
     * @var UuidInterface The UUID of the entity to select.
     */
    private $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Returns the UUID of the entity to select.
     *
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @inheritdoc
     */
    public function includesNonPlayerEntities(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getLimit(): int
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function includesDeadEntities(): bool
    {
        return true;
    }
}