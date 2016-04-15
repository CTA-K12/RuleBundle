<?php

namespace Mesd\RuleBundle\Model\Action;

use Mesd\RuleBundle\Model\Input\InputInterface;

abstract class AbstractServiceAction implements ActionInterface
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
     * The name of the service this is being built from.
     *
     * @var string
     */
    protected $serviceName;

    /**
     * The input this attribute and comparator accept.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The name assigned to the action.
     *
     * @var string
     */
    protected $name;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param string $serviceName   THe name of the service
     * @param mixed  $serviceObject The service object of this attribute
     */
    public function __construct($serviceName, $serviceObject)
    {
        //Set variables
        $this->serviceName   = $serviceName;
        $this->serviceObject = $serviceObject;
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Gets the name of the action.
     *
     * @return string Action Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the action.
     *
     * @param string $name [description]
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
     * Sets the value of the input.
     *
     * @param mixed $value The input value
     */
    public function setInputValue($value)
    {
        $this->input->setRawInput($value);
    }

    /**
     * Get the raw input value.
     *
     * @return mixed The raw input value
     */
    public function getInputValue()
    {
        return $this->input->getRawInput();
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
     * Perform the action.
     */
    abstract public function perform();

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
        return $this->serviceObject;
    }
}
