<?php

namespace Mesd\RuleBundle\Model\Context;

use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;

class ContextCollection implements ContextCollectionInterface
{
    //////////////
    // CONTEXTS //
    //////////////

    //Errors
    const ERROR_MISSING_CONTEXT = 'The requested context was not found';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * Generic Context Array keyed by name.
     *
     * @var array
     */
    private $contexts;

    /**
     * The definition manager reference.
     *
     * @var DefinitionManagerInterface
     */
    private $definitionManager;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param DefinitionManagerInterface $definitionManager The definition manager
     */
    public function __construct(DefinitionManagerInterface $definitionManager)
    {
        //Set stuff
        $this->definitionManager = $definitionManager;

        //Init stuff
        $this->contexts = [];
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Add a context to the collection.
     *
     * @param GenericContext $context Context to add
     */
    public function addContext(GenericContext $context)
    {
        $this->contexts[$context->getName()] = $context;
    }

    /**
     * Creates a new instance of the requested attribute for the given context.
     *
     * @param string $contextName   The name of the context to get the attribute for
     * @param string $attributeName The name of the attribute in the definition manager to create an instance of
     *
     * @return AbstractContextAttribute The new attribute object
     */
    public function createContextAttribute($contextName, $attributeName)
    {
        //Get the attribute from the defintion manager
        $attribute = $this->definitionManager->getContextAttribute($contextName, $attributeName);

        //Add the attribute to the context
        if (!array_key_exists($contextName, $this->contexts)) {
            throw new \Exception(self::ERROR_MISSING_CONTEXT);
        }
        $this->contexts[$contextName]->addAttribute($attribute);

        //Return the new attribute object
        return $attribute;
    }

    /**
     * Creates a new instance of the requested action for the given context.
     *
     * @param string $contextName The name of the context to get the action for
     * @param string $actionName  The name of the action in the definition manager to create an instace of
     *
     * @return AbstractContextAction The new action object
     */
    public function createContextAction($contextName, $actionName)
    {
        //Get the action from the defintion manager
        $action = $this->definitionManager->getContextAction($contextName, $actionName);

        //Add the action to the context
        if (!array_key_exists($contextName, $this->contexts)) {
            throw new \Exception(self::ERROR_MISSING_CONTEXT);
        }
        $this->contexts[$contextName]->addAction($action);

        //Return the new action object
        return $action;
    }

    /**
     * Return an array of contexts keyed by name.
     *
     * @return array Context array
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * Sets the value of the contexts.
     *
     * @param array $values An array of values to set the contexts with keyed by the context name
     */
    public function setValues($values = [])
    {
        foreach ($values as $context => $value) {
            if (array_key_exists($context, $this->contexts)) {
                $this->contexts[$context]->setObject($value);
            }
        }
    }
}
