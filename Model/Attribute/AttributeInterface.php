<?php

namespace Mesd\RuleBundle\Model\Attribute;

use Mesd\RuleBundle\Model\Comparator\ComparatorInterface;
use Mesd\RuleBundle\Model\Input\InputInterface;

interface AttributeInterface
{
    /**
     * Gets the name of the attribute
     *
     * @return string The attributes name
     */
    public function getName();

    /**
     * Sets the name of the attribute
     *
     * @param string $name The name to assign to the attribute
     */
    public function setName($name);

    /**
     * Gets the description of the attribute if one exists
     *
     * @return string|null The attributes description
     */
    public function getDescription();

    /**
     * Get the name of the parent context/service
     *
     * @return string The parent name
     */
    public function getParentName();

    /**
     * Gets the attributes value
     *
     * @return mixed The value of the attribute
     */
    public function getValue();

    /**
     * Sets the comparator that this attribute will use
     *
     * @param ComparatorInterface $comparator The comparator
     */
    public function setComparator(ComparatorInterface $comparator);

    /**
     * Gets the comparator for this attribute
     *
     * @return ComparatorInterface The comparator used by this attribute
     */
    public function getComparator();

    /**
     * Sets the input that will be used for the rule form
     *
     * @param InputInterface $input The input that will be used for this attribute
     */
    public function setInput(InputInterface $input);

    /**
     * Gets the input that will be used for the rule form
     *
     * @return InputInterface The input that will be used for this attribute
     */
    public function getInput();

    /**
     * Sets the value of the attributes comparator operator
     *
     * @param string $value The value to use for the operator
     */
    public function setOperatorValue($value);

    /**
     * Get the value of the operator
     * 
     * @return string The current value of the operator
     */
    public function getOperatorValue();

    /**
     * Sets the value of the input
     *
     * @param mixed $value The input value
     */
    public function setInputValue($value);

    /**
     * Returns the raw input for the input
     *
     * @return mixed The raw input value
     */
    public function getInputValue();

}