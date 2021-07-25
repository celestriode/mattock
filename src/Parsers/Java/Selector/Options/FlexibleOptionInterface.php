<?php namespace Celestriode\Mattock\Parsers\Java\Selector\Options;

interface FlexibleOptionInterface
{
    /**
     * Returns the numerical position within the list of flexible options that this option should be specified around.
     * Lower numbers mean the option should be specified before options with higher positions. Positions can be equal
     * to indicate that the order between those options is irrelevant. If 0, then order should be considered irrelevant.
     *
     * The positions provided by default are just recommended; feel free to ignore them and implement your own order.
     *
     * @return int
     */
    public function getPosition(): int;
}