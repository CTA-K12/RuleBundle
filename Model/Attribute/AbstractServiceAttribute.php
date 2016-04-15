<?php

namespace Mesd\RuleBundle\Model\Attribute;

use Mesd\RuleBundle\Model\Comparator\ComparatorInterface;
use Mesd\RuleBundle\Model\Input\InputInterface;

abstract class AbstractServiceAttribute implements AttributeInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The object being used as a service.
     *
     * @var mixed
     */
    protected $serviceObject;

    /**
     * The comparator that this attribute uses.
     *
     * @var ComparatorInterface
     */
    protected $comparator;

    /**
     * The input this attribute and comparator accept.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The name assigned to this attribute.
     *
     * @var string
     */
    protected $name;

    /**
     * The name of the parent service.
     *
     * @var string
     */
    protected $serviceName;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param string $serviceName   The name of the service
     * @param mixed  $serviceObject The service object of this attribute
     */
    public function __construct($serviceName, $serviceObject)
    {
        //Set variables
        $this->serviceObject = $this->serviceObject;
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Gets the name of the attribute.
     *
     * @return string Attribute Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the attribute.
     *
     * @param string $name The name to assign to the attribute
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the description of the attribute.
     *
     * @return string|null Attribute description
     */
    public function getDescription()
    {
        return;
    }

    /**
     * Get the name of the parent context/service.
     *
     * @return string The parent name
     */
    public function getParentName()
    {
        return $this->serviceName;
    }

    /**
     * Sets the comparator that this attribute will use.
     *
     * @param ComparatorInterface $comparator The comparator
     */
    public function setComparator(ComparatorInterface $comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * Gets the comparator for this attribute.
     *
     * @return ComparatorInterface The comparator used by this attribute
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * Sets the input that will be used for the rule form.
     *
     * @param InputInterface $input The input that will be used for this attribute
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Gets the input that will be used for the rule form.
     *
     * @return InputInterface The input that will be used for this attribute
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Sets the value of the attributes comparator operator.
     *
     * @param string $value The value to use for the operator
     */
    public function setOperatorValue($value)
    {
        $this->comparator->setCurrentOperator($value);
    }

    /**
     * Get the value of the operator.
     *
     * @return string The current value of the operator
     */
    public function getOperatorValue()
    {
        return $this->comparator->getCurrentOperator()->getValue();
    }

    /**
     * Sets the value of the input.
     *
     * @param mixed $value The input value
     */
    public function setInputValue($value)
    {
        $this->input->setRawInput($value);
    }

    /**
     * Returns the raw input for the input.
     *
     * @return mixed The raw input value
     */
    public function getInputValue()
    {
        return $this->input->getRawInput();
    }

    /**
     * Gets the value of the attribute ().
     *
     * @return mixed The value of the attribute
     */
    abstract public function getValue();

    /////////////
    // METHODS //
    /////////////


    /**
     * Provides a short hand for getting the underlying object of the parent context
     * NOTE: this really isnt a short hand in this class, but its here to provide consistency between this and the context attribute.
     *
     * @return mixed The object from the parent context
     */
    protected function getObject()
    {
        return $this->serviceObject->getObject();
    }
}
