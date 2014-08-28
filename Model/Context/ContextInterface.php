<?php

namespace Mesd\RuleBundle\Model\Context;

use Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute;
use Mesd\RuleBundle\Model\Action\AbstractContextAction;

interface ContextInterface
{
    /**
     * Returns the name of the context
     * 
     * @return string
     */
    public function getName();

    /**
     * Sets the name of the context
     *
     * @param string $name The name to assign to the context
     */
    public function setName($name);

    /**
     * Gets the underlying object that the context is representing
     *
     * @return mixed The underlying object
     */
    public function getObject();

    /**
     * Sets the underlying object
     *
     * @param mixed $object The underlying object
     */
    public function setObject($object);

    /**
     * Returns the list of attributes associated with this context
     *
     * @return array Array of AbstractContextAttribute objects
     */
    public function getAttributes();

    /**
     * Adds an attribute to the context
     * 
     * @param AbstractContextAttribute $attribute The attribute to register with the context
     */
    public function addAttribute(AbstractContextAttribute $attribute);

    /**
     * Gets the array of actions associated with this context
     * 
     * @return array An array of AbstractContextAction objects
     */
    public function getActions();

    /**
     * Adds an action to the context
     * 
     * @param AbstractContextAction $action
     */
    public function addAction(AbstractContextAction $action);
}