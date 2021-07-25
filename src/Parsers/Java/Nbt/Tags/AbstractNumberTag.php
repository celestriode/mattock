<?php namespace Celestriode\Mattock\Parsers\Java\Nbt\Tags;

use Celestriode\Mattock\Exceptions\MattockException;

abstract class AbstractNumberTag implements TagInterface
{
    protected $value = 0;

    /**
     * Changes the value into the correct datatype.
     *
     * @param mixed $value The value to change, usually being a string.
     * @return int|float
     */
    abstract public function parseValue($value);

    /**
     * @throws MattockException
     */
    public function __construct($value = null)
    {
        $this->setValue($this->parseValue($value));
    }

    /**
     * @throws MattockException
     */
    public function setValue($value)
    {
        if (is_string($value) || !is_numeric($value)) {

            throw new MattockException('Value must not be a string and must be numeric'); // TODO: better exc for all instances.
        }

        $this->value = $value;
    }

    public function getType(): int
    {
        return self::TAG_NUMERIC;
    }
}