<?php namespace Celestriode\Mattock\Parsers\Java\Selector;

/**
 * A light definition for all selectors and their common parts.
 *
 * TODO: determine which other parts should be common.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector
 */
interface SelectorInterface
{
    /**
     * Whether or not non-player entities may be targeted by the selector.
     *
     * @return bool
     */
    public function includesNonPlayerEntities(): bool;

    /**
     * The maximum number of entities that can be targeted by the selector.
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Whether or not dead entities may be targeted by the selector.
     *
     * @return bool
     */
    public function includesDeadEntities(): bool;
}