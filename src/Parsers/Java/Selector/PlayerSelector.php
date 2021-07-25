<?php namespace Celestriode\Mattock\Parsers\Java\Selector;

/**
 * A selector that selects players based on their in-game username.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector
 */
class PlayerSelector implements SelectorInterface
{
    /**
     * @var string The name of the player to be selected.
     */
    private $playerName;

    public function __construct(string $playerName)
    {
        $this->playerName = $playerName;
    }

    /**
     * Returns the name of the player to be selected.
     *
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    /**
     * @inheritdoc
     */
    public function includesNonPlayerEntities(): bool
    {
        return false;
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