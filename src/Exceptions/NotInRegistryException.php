<?php namespace Celestriode\Mattock\Exceptions;

use Celestriode\Captain\Exceptions\DynamicCommandExceptionType;
use Celestriode\Captain\Exceptions\DynamicFunctionInterface;
use Celestriode\DynamicRegistry\AbstractRegistry;

/**
 * Exception to distinguish when an option fails due to a value not being within a registry.
 *
 * @package Celestriode\Mattock\Exceptions
 */
class NotInRegistryException extends DynamicCommandExceptionType
{
    /**
     * @var AbstractRegistry The registry associated with this exception.
     */
    private $registry;

    public function __construct(AbstractRegistry $registry, DynamicFunctionInterface $function)
    {
        $this->registry = $registry;
        parent::__construct($function);
    }

    /**
     * Returns the registry associated with this exception.
     *
     * @return AbstractRegistry
     */
    public function getRegistry(): AbstractRegistry
    {
        return $this->registry;
    }
}