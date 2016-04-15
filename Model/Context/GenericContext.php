<?php

namespace Mesd\RuleBundle\Model\Context;

use Mesd\RuleBundle\Model\Action\AbstractContextAction;
use Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute;

class GenericContext implements ContextInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The name of the context.
     *
     * @var string
     */
    private $name;

    /**
     * The context definition.
     *
     * @var ContextDefinition
     */
    private $contextDefinition;

    /**
     * The object that is the current value of the context.
     *
     * @var mixed
     */
    private $object;

    /**
     * The array of attributes associated with this context.
     *
     * @var array
     */
    private $attributes;

    /**
     * THe array of actions associated with this context.
     *
     * @var array
     */
    private $actions;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param ContextDefinition $contextDefinition The definition of the context
     * @param string            $name              The name of the context (will default to the classification name of the context definition)
     */
    public function __construct(ContextDefinition $contextDefinition, $name = null)
    {
        //Set stuff
        $this->contextDefinition = $contextDefinition;

        //Set the name (if its null, use the definition name)
        if (null === $name) {
            $this->name = $contextDefinition->getName();
        } else {
            $this->name = $name;
        }

        //Init stuff
        $this->attributes = [];
        $this->actions    = [];
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Returns the name of the context.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the context.
     *
     * @param string $name The name to assign to the context
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the underlying object that the context is representing.
     *
     * @return mixed The underlying object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Sets the underlying object.
     *
     * @param mixed $object The underlying object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * Returns the list of attributes associated with this context.
     *
     * @return array Array of AbstractContextAttribute objects
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Adds an attribute to the context.
     *
     * @param AbstractContextAttribute $attribute The attribute to register with the context
     */
    public function addAttribute(AbstractContextAttribute $attribute)
    {
        //Set the parent context of the attribute
        $attribute->setParentContext($this);

        //Add the attribute in
        $this->attributes[] = $attribute;
    }

    /**
     * Gets the array of actions associated with this context.
     *
     * @return array An array of AbstractContextAction objects
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Adds an action to the context.
     *
     * @param AbstractContextAction $action
     */
    public function addAction(AbstractContextAction $action)
    {
        //Set the parent context of the action
        $action->setParentContext($this);

        //Add the action int
        $this->actions[] = $action;
    }
}
