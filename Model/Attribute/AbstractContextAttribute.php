<?php

namespace Mesd\RuleBundle\Model\Attribute;

use Mesd\RuleBundle\Model\Comparator\ComparatorInterface;
use Mesd\RuleBundle\Model\Context\ContextInterface;
use Mesd\RuleBundle\Model\Input\InputInterface;

abstract class AbstractContextAttribute implements AttributeInterface
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
     * The name assigned to the attribute.
     *
     * @var string
     */
    protected $name;

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
        return $this->getParentContext()->getName();
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
