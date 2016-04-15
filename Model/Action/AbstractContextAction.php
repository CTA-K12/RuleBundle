<?php

namespace Mesd\RuleBundle\Model\Action;

use Mesd\RuleBundle\Model\Context\ContextInterface;
use Mesd\RuleBundle\Model\Input\InputInterface;

abstract class AbstractContextAction implements ActionInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The parent context.
     *
     * @var ContextInterface
     */
    protected $parentContext;

    /**
     * The input this attribute and comparator accept.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The name assigned to this action.
     *
     * @var string
     */
    protected $name;

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
     * Get the name of the parent context/service.
     *
     * @return string The parent name
     */
    public function getParentName()
    {
        return $this->getParentContext()->getName();
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
     * Performs the action.
     */
    abstract public function perform();

    /////////////
    // METHODS //
    /////////////


    /**
     * Provides a short hand for getting the underlying object of the parent context.
     *
     * @return mixed The object from the parent context
     */
    protected function getObject()
    {
        return $this->parentContext->getObject();
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Gets the The parent context.
     *
     * @return ContextInterface
     */
    public function getParentContext()
    {
        return $this->parentContext;
    }

    /**
     * Sets the The parent context.
     *
     * @param ContextInterface $parentContext the parent context
     *
     * @return self
     */
    public function setParentContext(ContextInterface $parentContext)
    {
        $this->parentContext = $parentContext;

        return $this;
    }
}
