<?php

namespace Mesd\RuleBundle\Model\Context;

use Mesd\RuleBundle\Model\Context\GenericContext;
use Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute;
use Mesd\RuleBundle\Model\Action\AbstractContextAction;

interface ContextCollectionInterface
{
    /**
     * Add a context to the collection
     *
     * @param GenericContext $context Context to add
     */
    public function addContext(GenericContext $context);

    /**
     * Creates a new instance of the requested attribute for the given context
     *
     * @param  string                   $contextName   The name of the context to get the attribute for
     * @param  string                   $attributeName The name of the attribute in the definition manager to create an instance of
     *
     * @return AbstractContextAttribute                The new attribute object
     */
    public function createContextAttribute($contextName, $attributeName);

    /**
     * Creates a new instance of the requested action for the given context
     *
     * @param  string                $contextName The name of the context to get the action for
     * @param  string                $actionName  The name of the action in the definition manager to create an instace of
     *
     * @return AbstractContextAction              The new action object
     */
    public function createContextAction($contextName, $actionName);

    /**
     * Return an array of contexts keyed by name
     *
     * @return array Context array
     */
    public function getContexts();

    /**
     * Sets the value of the contexts
     *
     * @param array $values An array of values to set the contexts with keyed by the context name
     */
    public function setValues($values = []);
}