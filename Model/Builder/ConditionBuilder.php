<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Attribute\AttributeInterface;
use Mesd\RuleBundle\Model\Condition\StandardCondition;

class ConditionBuilder implements ConditionBuilderInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The underlying condition.
     *
     * @var StandardCondition
     */
    private $condition;

    /**
     * The attribute.
     *
     * @var AttributeInterface
     */
    private $attribute;

    /**
     * The value of the operator.
     *
     * @var string
     */
    private $operatorValue;

    /**
     * The value of the input.
     *
     * @var mixed
     */
    private $inputValue;

    /**
     * The condition collection builder that spawned this object.
     *
     * @var ConditionCollectionBuilderInterface
     */
    private $parentBuilder;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param ConditionCollectionBuilderInterface $parentBuilder The parent builder
     */
    public function __construct(ConditionCollectionBuilderInterface $parentBuilder)
    {
        //Set stuff
        $this->parentBuilder = $parentBuilder;

        //Set the condition to null until the end
        $this->condition = null;
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Sets the attribute for this condition as a service attribute with the given name.
     *
     * @param string $name The attributes name
     *
     * @return self
     */
    public function setServiceAttribute($name)
    {
        //Get the service attribute via the definition manager
        $this->attribute = $this->parentBuilder->getDefinitionManager()->getServiceAttribute($name);

        return $this;
    }

    /**
     * Sets the attribute to the given cotnext attribute.
     *
     * @param string $contextName   The name of the context
     * @param string $attributeName The name of the attribute
     */
    public function setContextAttribute($contextName, $attributeName)
    {
        //Get the context attribute via the context collection from the ruleset builder
        $this->attribute = $this->parentBuilder->getContextCollection()->createContextAttribute($contextName, $attributeName);

        return $this;
    }

    /**
     * The operator value to use for the comparator.
     *
     * @param string $operatorValue The value of the operator to use
     *
     * @return self
     */
    public function setOperatorValue($operatorValue)
    {
        $this->operatorValue = $operatorValue;

        return $this;
    }

    /**
     * Sets the raw value of the input.
     *
     * @param mixed $inputValue The raw value to use as the input value
     *
     * @return self
     */
    public function setInputValue($inputValue)
    {
        $this->inputValue = $inputValue;

        return $this;
    }

    /**
     * Completes the new condition and returns the parent builder.
     *
     * @return ConditionCollectionBuilderInterface The parent condition collection builder
     */
    public function end()
    {
        //Build the condition
        $condition = new StandardCondition($this->attribute);
        $condition->setOperatorValue($this->operatorValue);
        $condition->setInputValue($this->inputValue);

        //Give the condition to the parent builder
        $this->parentBuilder->addCondition($condition);

        //return the parent builder
        return $this->parentBuilder;
    }
}
