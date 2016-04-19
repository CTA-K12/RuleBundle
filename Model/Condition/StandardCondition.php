<?php

namespace Mesd\RuleBundle\Model\Condition;

use Mesd\RuleBundle\Model\Attribute\AttributeInterface;

class StandardCondition implements ConditionInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The attribute that the condition uses as a base.
     *
     * @var AttributeInterface
     */
    private $attribute;

    //////////////////
    // BASE METHODS //
    //////////////////

    /**
     * Constructor.
     *
     * @param AttributeInterface $attribute The attribute that the condition is based on
     */
    public function __construct(AttributeInterface $attribute)
    {
        //Set stuff
        $this->attribute = $attribute;
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////

    /**
     * Evaluates the condition.
     *
     * @return boolean The result of the condition
     */
    public function evaluate()
    {
        //Return the result of the attribute compared with the input with the attributes comparator
        return $this->attribute->getComparator()->compare($this->attribute->getValue(), $this->attribute->getInput()->getValue());
    }

    /**
     * Returns true if the condition is a collection of conditions.
     *
     * @return boolean Whether the condition is a collection of conditions or not
     */
    public function isCollection()
    {
        return false;
    }

    /////////////
    // METHODS //
    /////////////

    public function __toString()
    {
        return $this->attribute->getName() . " " .
        $this->attribute->getOperatorValue() . " " .
        $this->attribute->getInputValue()
        ;
    }

    /**
     * Set the value of the conditions operator.
     *
     * @param string $value The value to set the comparators operator to
     */
    public function setOperatorValue($value)
    {
        $this->attribute->setOperatorValue($value);
    }

    /**
     * Get the value of the operator.
     *
     * @return string The value of the operator
     */
    public function getOperatorValue()
    {
        return $this->attribute->getOperatorValue();
    }

    /**
     * Set the value of the input.
     *
     * @param mixed $value The input value
     */
    public function setInputValue($value)
    {
        $this->attribute->setInputValue($value);
    }

    /**
     * Get the value fo the input.
     *
     * @return mixed The value of the input
     */
    public function getInputValue()
    {
        return $this->attribute->getInputValue();
    }

    /**
     * Returns this conditions attribute.
     *
     * @return AttributeInterface The attribute used for this condition
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
