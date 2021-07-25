<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

/**
 * @package Celestriode\Mattock\Parsers\Java\Utils
 */
interface PopulationValidatorInterface
{
    public function validateFromPopulation(string $input): bool;
}