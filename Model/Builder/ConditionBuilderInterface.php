<?php

namespace Mesd\RuleBundle\Model\Builder;


interface ConditionBuilderInterface
{
    /**
     * Sets the attribute for this condition as a service attribute with the given name.
     *
     * @param string $name The attributes name
     *
     * @return self
     */
    public function setServiceAttribute($name);

    /**
     * Sets the attribute to the given cotnext attribute.
     *
     * @param string $contextName   The name of the context
     * @param string $attributeName The name of the attribute
     */
    public function setContextAttribute($contextName, $attributeName);

    /**
     * The operator value to use for the comparator.
     *
     * @param string $operatorValue The value of the operator to use
     *
     * @return self
     */
    public function setOperatorValue($operatorValue);

    /**
     * Sets the raw value of the input.
     *
     * @param mixed $inputValue The raw value to use as the input value
     *
     * @return self
     */
    public function setInputValue($inputValue);

    /**
     * Completes the new condition and returns the parent builder.
     *
     * @return ConditionCollectionBuilderInterface The parent condition collection builder
     */
    public function end();
}
