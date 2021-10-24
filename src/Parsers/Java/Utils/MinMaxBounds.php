<?php namespace Celestriode\Mattock\Parsers\Java\Utils;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Mattock\Exceptions\Utils\UtilsException;
use IntlChar;

/**
 * A holder for a min-max numeric range. If min or max are null, then that represents no bound on that end.
 *
 * Includes parsing from a string.
 *
 * @package Celestriode\Mattock\Parsers\Java\Selector
 */
class MinMaxBounds
{
    private $min;
    private $max;

    public function __construct(?float $min, ?float $max)
    {
        $this->setMin($min);
        $this->setMax($max);
    }

    /**
     * Sets the minimum, or null if no bound.
     *
     * @param float|null $min
     */
    public function setMin(?float $min): void
    {
        $this->min = $min;
    }

    /**
     * Sets the maximum, or null if no bound.
     *
     * @param float|null $max
     */
    public function setMax(?float $max): void
    {
        $this->max = $max;
    }

    /**
     * Sets this boundary's min and max based on another boundary.
     *
     * @param MinMaxBounds $bounds
     */
    public function setFromBounds(self $bounds): void
    {
        $this->setMin($bounds->getMin());
        $this->setMax($bounds->getMax());
    }

    /**
     * Returns the min, null if no bound.
     *
     * @return float|null
     */
    public function getMin(): ?float
    {
        return $this->min;
    }

    /**
     * Returns the max, null if no bound.
     *
     * @return float|null
     */
    public function getMax(): ?float
    {
        return $this->max;
    }

    /**
     * Returns whether or not both min and max are null.
     *
     * @return bool
     */
    public function isAny(): bool
    {
        return $this->getMin() === null && $this->getMax() === null;
    }

    /**
     * Transforms the boundary into a string representation.
     *
     * @return string
     */
    public function toString(): string
    {
        if ($this->getMin() == $this->getMax()) {

            return (string)$this->getMin();
        }

        return $this->getMin() . '..' . $this->getMax();
    }

    /**
     * Creates a bound from a string reader. This means the bound must follow the format:
     *
     * min..max
     *
     * Where min xor max are numbers that may be nonexistent to represent no bound on that end.
     *
     * @param StringReader $reader
     * @param bool $onlyIntegers
     * @param bool $onlyPositive
     * @return static
     * @throws CommandSyntaxException
     */
    public static function fromReader(StringReader $reader, bool $onlyIntegers = false, bool $onlyPositive = true): self
    {
        // If there's no input at all, throw error.

        if (!$reader->canRead()) {

            UtilsException::getBuiltInExceptions()->missingRange()->createWithContext($reader);
        }

        $n = $reader->getCursor();

        try {

            // Get the minimum, if existent.

            $min = static::readNumber($reader, $onlyIntegers); // Min comes first, set it.
            $max = $min; // Not yet sure if it's a range.

            // If there's a max to obtain, obtain it.

            if ($reader->canRead(2) && $reader->peek() == '.' && $reader->peek(1) == '.') {

                // Skip past the .. and obtain the max.

                $reader->skip();
                $reader->skip();

                $max = static::readNumber($reader, $onlyIntegers);

                // If neither min nor max are defined when using .., throw error.

                if ($min === null && $max === null) {

                    $reader->setCursor($n);

                    throw UtilsException::getBuiltInExceptions()->missingMinAndMax()->createWithContext($reader);
                }
            }

            // If at this point, both min and max are null, throw error.

            if ($min === null && $max === null) {

                $reader->setCursor($n);

                throw UtilsException::getBuiltInExceptions()->missingRange()->createWithContext($reader);
            }

            // If max is smaller than min, throw error.

            if ($onlyPositive && $max !== null && $min !== null && $min > $max) {

                $reader->setCursor($n);

                throw UtilsException::getBuiltInExceptions()->minMaxSwapped()->createWithContext($reader, $min . '..' . $max);
            }

            // Throw if the reader didn't use the entire string.

            if ($reader->canRead()) {

                $reader->skip();

                throw UtilsException::getBuiltInExceptions()->invalidRange()->createWithContext($reader, $reader->getString());
            }

            // All good, return a new bound.

            return new static($min, $max);

        } catch (CommandSyntaxException $e) {

            // Some error occurred, reset the cursor and re-throw the error.

            $reader->setCursor($n);

            throw $e;
        }
    }

    /**
     * Reads and returns the next number. Returns null if there was no number (with no other errors).
     *
     * @param StringReader $reader
     * @param bool $onlyIntegers
     * @return float|null
     * @throws CommandSyntaxException
     */
    protected static function readNumber(StringReader $reader, bool $onlyIntegers): ?float
    {
        $n = $reader->getCursor();

        // Skip through characters until the reader reaches an invalid one.

        while ($reader->canRead() && static::isAllowedInputChat($reader)) {

            $reader->skip();
        }

        // Get the substring of the string which should be a number.

        $string = substr($reader->getString(), $n, $reader->getCursor() - $n);

        // If there is no number, then there is no bound.

        if (empty($string)) {

            return null;
        }

        // If there is a number but it's not numeric, throw error.

        if (!is_numeric($string)) {

            throw UtilsException::getBuiltInExceptions()->invalidRange()->createWithContext($reader, $string);
        }

        // Throw if only integers are acceptable.

        if ($onlyIntegers && filter_var($string, FILTER_VALIDATE_INT) === false) {

            throw UtilsException::getBuiltInExceptions()->integerOnly()->createWithContext($reader);
        }

        // Otherwise return that number.

        return (float)$string;
    }

    /**
     * Returns whether or not the next character is allowed within numeric ranges.
     *
     * This includes only numbers and negative signs, as well as period if there aren't two in a row.
     *
     * @param StringReader $reader
     * @return bool
     */
    protected static function isAllowedInputChat(StringReader $reader): bool
    {
        $char = $reader->peek();

        if ($char != '.') {

            return true;
        }

        return !$reader->canRead(2) || $reader->peek(1) != '.';
    }
}