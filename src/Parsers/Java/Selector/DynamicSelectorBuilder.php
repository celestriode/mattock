<?php namespace Celestriode\Mattock\Parsers\Java\Selector;

/**
 * A builder to aid in determining applicability of options.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector
 */
class DynamicSelectorBuilder
{
    /**
     * @var DynamicSelector The dynamic selector associated with this builder.
     */
    private $selector;

    public $sortSpecified = false;
    public $namesInverted = false;
    public $limitSpecified = false;
    public $gameModesInverted = false;
    public $teamsInverted = false;
    public $typesInverted = false;

    public function __construct(DynamicSelector $selector)
    {
        $this->selector = $selector;
    }

    /**
     * Return the dynamic selector as it's being built.
     *
     * @return DynamicSelector
     */
    public function getSelector(): DynamicSelector
    {
        return $this->selector;
    }

    /**
     * Builds and returns the final selector.
     *
     * In this current situation is does utterly nothing for building, but maybe it will in the future.
     *
     * @return DynamicSelector
     */
    public function build(): DynamicSelector
    {
        return $this->getSelector();
    }
}