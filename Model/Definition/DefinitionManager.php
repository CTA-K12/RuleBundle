<?php

namespace Mesd\RuleBundle\Model\Definition;

use Mesd\RuleBundle\Model\Action\AbstractContextAction;
use Mesd\RuleBundle\Model\Action\AbstractServiceAction;
use Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute;
use Mesd\RuleBundle\Model\Attribute\AbstractServiceAttribute;
use Mesd\RuleBundle\Model\Builder\RulesetBuilder;
use Mesd\RuleBundle\Model\Builder\RulesetBuilderInterface;
use Mesd\RuleBundle\Model\Context\ContextCollection;
use Mesd\RuleBundle\Model\Context\ContextDefinition;
use Mesd\RuleBundle\Model\Context\ContextInterface;
use Mesd\RuleBundle\Model\Context\GenericContext;
use Mesd\RuleBundle\Model\Ruleset\Ruleset;
use Mesd\RuleBundle\Model\Ruleset\RulesetInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefinitionManager implements DefinitionManagerInterface
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Errors
    const ERROR_INPUT_NOT_FOUND     = 'The requested input name was not found';
    const ERROR_INPUT_INTERFACE     = 'Input classes are required to implement the InputInterface';
    const ERROR_ACTION_NOT_FOUND    = 'The requested action name was not found';
    const ERROR_ACTION_ABSTRACT     = 'Actions classes are required to subclasses of either AbstractContextAction or AbstractServiceAction';
    const ERROR_ATTRIBUTE_NOT_FOUND = 'The requested attribute name was not found';
    const ERROR_ATTRIBUTE_ABSTRACT  = 'Attributes classes are required to subclasses of either AbstractContextAttribute or AbstractServiceAttribute';
    const ERROR_CONTEXT_NOT_FOUND   = 'The requested context name was not found';
    const ERROR_RULESET_NOT_FOUND   = 'The requested ruleset name was not found';

    //Interface names
    const INPUT_INTERFACE            = 'Mesd\RuleBundle\Model\Input\InputInterface';
    const SERVICE_ACTION_ABSTRACT    = 'Mesd\RuleBundle\Model\Action\AbstractServiceAction';
    const SERVICE_ATTRIBUTE_ABSTRACT = 'Mesd\RuleBundle\Model\Attribute\AbstractServiceAttribute';
    const CONTEXT_ACTION_ABSTRACT    = 'Mesd\RuleBundle\Model\Action\AbstractContextAction';
    const CONTEXT_ATTRIBUTE_ABSTRACT = 'Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * Reference to the container (to process input and service attributes and actions).
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Array of contexts.
     *
     * @var array
     */
    private $contexts;

    /**
     * Array of inputs.
     *
     * @var array
     */
    private $inputs;

    /**
     * Array of rulesets.
     *
     * @var array
     */
    private $rulesets;

    /**
     * Array of context attributes.
     *
     * @var array
     */
    private $contextAttributes;

    /**
     * array of context actions.
     *
     * @var array
     */
    private $contextActions;

    /**
     * array of service attributes.
     *
     * @var array
     */
    private $serviceAttributes;

    /**
     * array of service actions.
     *
     * @var array
     */
    private $serviceActions;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param ContainerInterface $container The reference to the service container to process the service actions and attributes and input
     */
    public function __construct(ContainerInterface $container)
    {
        //set stuff
        $this->container = $container;

        //init stuff
        $this->contexts          = [];
        $this->inputs            = [];
        $this->rulesets          = [];
        $this->contextAttributes = [];
        $this->contextActions    = [];
        $this->serviceAttributes = [];
        $this->serviceActions    = [];
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    // REGISTER METHODS


    /**
     * Registers an input with the Definition Manager.
     *
     * @param string $name      The name to identity the input with
     * @param string $className The name of the input class
     * @param array  $params    The parameters to init the input with following service definition format
     * @param  self
     */
    public function registerInput($name, $className, $params = [])
    {
        //Insert into the map
        $this->inputs[$name] = ['class' => $className, 'params' => $params];
    }

    /**
     * Registers a context with the Definition Manager.
     *
     * @param string $name               The name to identify the context with
     * @param string $classificationName The name of the classification (e.g. MyBundle\Entity\LaserShark)
     * @param string $classificationType The type of thing it is (e.g. object, interface, primative)
     *
     * @return self
     */
    public function registerContext($name, $classificationName, $classificationType)
    {
        //Insert into the map
        $this->contexts[$name] = ['cName' => $classificationName, 'cType' => $classificationType];

        //Init the context actions and attributes array
        $this->contextAttributes[$name] = [];
        $this->contextActions[$name]    = [];
    }

    /**
     * Reigsters a context attribute with the Definition Manager.
     *
     * @param string $name        The name to register it under
     * @param string $contextName The name of the parent context
     * @param string $className   The name of attribute class
     * @param string $inputName   The name of the input
     *
     * @return self
     */
    public function registerContextAttribute($name, $contextName, $className, $inputName)
    {
        //Insert into the map
        if (!array_key_exists($contextName, $this->contextAttributes)) {
            $this->contextAttributes[$contextName] = [];
        }

        $this->contextAttributes[$contextName][$name] = ['class' => $className, 'input' => $inputName];
    }

    /**
     * Reigsts a context action with the Definition Manager.
     *
     * @param string $name        The name to register it under
     * @param string $contextName The name of the parent context
     * @param string $className   The name of action class
     * @param string $inputName   The name of the input
     *
     * @return self
     */
    public function registerContextAction($name, $contextName, $className, $inputName)
    {
        //Insert into the map
        if (!array_key_exists($contextName, $this->contextActions)) {
            $this->contextActions[$contextName] = [];
        }

        $this->contextActions[$contextName][$name] = ['class' => $className, 'input' => $inputName];
    }

    /**
     * Registers a service attribute with the Definition Manager.
     *
     * @param string $name        The name to register it under
     * @param string $serviceName The name of the service to use as the parent object
     * @param string $className   The name of the attribute class
     * @param string $inputName   The name of the input to use
     *
     * @return self
     */
    public function registerServiceAttribute($name, $serviceName, $className, $inputName)
    {
        $this->serviceAttributes[$name] = ['service' => $serviceName, 'class' => $className, 'input' => $inputName];
    }

    /**
     * Registers a service action with the Definition Manager.
     *
     * @param string $name        The name to register it under
     * @param string $serviceName The name of the service to use as the parent object
     * @param string $className   The name of the action class
     * @param string $inputName   The name of the input to use
     *
     * @return self
     */
    public function registerServiceAction($name, $serviceName, $className, $inputName)
    {
        $this->serviceActions[$name] = ['service' => $serviceName, 'class' => $className, 'input' => $inputName];
    }

    /**
     * Registers a ruleset with the Context Manager.
     *
     * @param string $name     The name of the ruleset
     * @param array  $children The array of children in the following format:
     *                         array('contexts' => array('Log', 'User'),
     *                         'ruleAttributes' => array('Current Date', 'Users Logged In'),
     *                         'ruleActions' => array('Explode Server', 'Mass Log Out'))
     *
     * @return self
     */
    public function registerRuleset($name, $children = [])
    {
        $this->rulesets[$name] = $children;
    }

    // GET METHODS


    /**
     * Gets the ruleset object associated with the given name.
     *
     * @param string $name The name registered with the ruleset
     *
     * @return RulesetInterface The ruleset registered under the given name
     */
    public function getRuleset($name)
    {
        //Get the tuple
        if (!array_key_exists($name, $this->rulesets)) {
            throw new \Exception(self::ERROR_RULESET_NOT_FOUND);
        }
        $tuple = $this->rulesets[$name];

        //Create the context collection
        $contextCollection = new ContextCollection($this);
        if (array_key_exists('contexts', $tuple)) {
            foreach ($tuple['contexts'] as $context) {
                $contextCollection->addContext($this->getContext($context));
            }
        }

        //Create the ruleset
        $ruleset = new Ruleset($name, $contextCollection);

        //Add in the service attributes
        if (array_key_exists('ruleAttributes', $tuple)) {
            foreach ($tuple['ruleAttributes'] as $attribute) {
                $ruleset->addServiceAttribute($this->getServiceAttribute($attribute));
            }
        }

        //Add in the service actions
        if (array_key_exists('ruleActions', $tuple)) {
            foreach ($tuple['ruleActions'] as $action) {
                $ruleset->addServiceAction($this->getServiceAction($action));
            }
        }

        //return
        return $ruleset;
    }

    /**
     * Gets the context registered under the given name.
     *
     * @param string $name The name registered with the context
     *
     * @return ContextInterface The context registered under the given name
     */
    public function getContext($name)
    {
        //Get the tuple
        if (!array_key_exists($name, $this->contexts)) {
            throw new \Exception(self::ERROR_CONTEXT_NOT_FOUND);
        }
        $tuple = $this->contexts[$name];

        //Build a generic context class
        $context = new GenericContext(new ContextDefinition($tuple['cName'], $tuple['cType']), $name);

        //Return the context
        return $context;
    }

    /**
     * Gets the context registered under the given name along with the associated attributes and actions.
     *
     * @param string $name The name of the context
     *
     * @return ContextInterface The context
     */
    public function getContextWithAttributesAndActions($name)
    {
        //Get the context
        $context = $this->getContext($name);

        //Get the full list of attributes
        foreach ($this->getAllContextAttributes($name) as $attribute) {
            $context->addAttribute($attribute);
        }

        //Get the full list of actions
        foreach ($this->getAllContextActions($name) as $action) {
            $context->addAction($action);
        }

        //Return the context
        return $context;
    }

    /**
     * Gets the array of attributes associated with the given context.
     *
     * @param string $name The name of the parent context
     *
     * @return array The list of attributes
     */
    public function getAllContextAttributes($name)
    {
        $attributes = [];

        //Foreach attribute related to this context add it
        if (array_key_exists($name, $this->contextAttributes)) {
            foreach ($this->contextAttributes[$name] as $attribute => $tuple) {
                $attributes[] = $this->getContextAttribute($name, $attribute);
            }
        }

        return $attributes;
    }

    /**
     * Gets the array of actions associated with the given context.
     *
     * @param string $name The name of the parent context
     *
     * @return array The list of actions
     */
    public function getAllContextActions($name)
    {
        $actions = [];

        //Foreach action related to this context add it
        if (array_key_exists($name, $this->contextActions)) {
            foreach ($this->contextActions[$name] as $action => $tuple) {
                $actions[] = $this->getContextAction($name, $action);
            }
        }

        return $actions;
    }

    /**
     * Gets the a context attribute by context and attribute names.
     *
     * @param string $contextName   The name of the parent context
     * @param string $attributeName The name of the attribute
     *
     * @return AbstractContextAttribute The attribute
     */
    public function getContextAttribute($contextName, $attributeName)
    {
        //Get the tuple
        if (!array_key_exists($contextName, $this->contextAttributes) ||
            !array_key_exists($attributeName, $this->contextAttributes[$contextName])) {
            throw new \Exception(self::ERROR_ATTRIBUTE_NOT_FOUND);
        }
        $tuple = $this->contextAttributes[$contextName][$attributeName];

        //Build the reflection class
        $reflection = new \ReflectionClass($tuple['class']);

        //Check that it is an AbstractContextAttribute
        if (!$reflection->isSubclassOf(self::CONTEXT_ATTRIBUTE_ABSTRACT)) {
            throw new \Exception(self::ERROR_ATTRIBUTE_ABSTRACT);
        }

        //Get the attribute
        $attribute = $reflection->newInstanceArgs();

        //Set the input and name
        $attribute->setInput($this->getInput($tuple['input']));
        $attribute->setName($attributeName);

        //return the attribute (note: parentContext is not yet set)
        return $attribute;
    }

    /**
     * Gets the a context action by context and action names.
     *
     * @param string $contextName The name of the parent context
     * @param string $actionName  The name of the action
     *
     * @return AbstractContextAction The action
     */
    public function getContextAction($contextName, $actionName)
    {
        //Get the tuple
        if (!array_key_exists($contextName, $this->contextActions) ||
            !array_key_exists($actionName, $this->contextActions[$contextName])) {
            throw new \Exception(self::ERROR_ACTION_NOT_FOUND);
        }
        $tuple = $this->contextActions[$contextName][$actionName];

        //Build the reflection class
        $reflection = new \ReflectionClass($tuple['class']);

        //Check that it is an AbstractContextAction
        if (!$reflection->isSubclassOf(self::CONTEXT_ACTION_ABSTRACT)) {
            throw new \Exception(self::ERROR_ACTION_ABSTRACT);
        }

        //Get the action
        $action = $reflection->newInstanceArgs();

        //Set the input and name
        $action->setInput($this->getInput($tuple['input']));
        $action->setName($actionName);

        //Return the action (NOTE: parentContext is not yet set)
        return $action;
    }

    /**
     * Gets a service attribute by name.
     *
     * @param string $name The name of the service attribute
     *
     * @return AbstractServiceAttribute The service attribute
     */
    public function getServiceAttribute($name)
    {
        //Get the tuple
        if (!array_key_exists($name, $this->serviceAttributes)) {
            throw new \Exception(self::ERROR_ATTRIBUTE_NOT_FOUND);
        }
        $tuple = $this->serviceAttributes[$name];

        //Build the refelection class
        $reflection = new \ReflectionClass($tuple['class']);

        //Check that it is an AbstractServiceAttribute
        if (!$reflection->isSubclassOf(self::SERVICE_ATTRIBUTE_ABSTRACT)) {
            throw new \Exception(self::ERROR_ATTRIBUTE_ABSTRACT);
        }

        //Get the attribute
        $attribute = $reflection->newInstanceArgs([$tuple['service'], $this->container->get($tuple['service'])]);

        //Add the input and name
        $attribute->setInput($this->getInput($tuple['input']));
        $attribute->setName($name);

        //Return the attribute
        return $attribute;
    }

    /**
     * Gets a service action by name.
     *
     * @param string $name The name of the service action
     *
     * @return AbstractServiceAction The service action
     */
    public function getServiceAction($name)
    {
        //Get the tuple
        if (!array_key_exists($name, $this->serviceActions)) {
            throw new \Exception(self::ERROR_ACTION_NOT_FOUND);
        }
        $tuple = $this->serviceActions[$name];

        //Build the reflection class
        $reflection = new \ReflectionClass($tuple['class']);

        //Check that it is an AbstractServiceAction
        if (!$reflection->isSubclassOf(self::SERVICE_ACTION_ABSTRACT)) {
            throw new \Exception(self::ERROR_ACTION_ABSTRACT);
        }

        //Get the service and create the action
        $action = $reflection->newInstanceArgs([$tuple['service'], $this->container->get($tuple['service'])]);

        //Add the input and name
        $action->setInput($this->getInput($tuple['input']));
        $action->setName($name);

        //Return the action
        return $action;
    }

    /**
     * Gets the input registered under the given name.
     *
     * @param string $name The name the input is registered under
     *
     * @return InputInterface The input registered under the given name
     */
    public function getInput($name)
    {
        //Get the tuple from the array
        if (!array_key_exists($name, $this->inputs)) {
            throw new \Exception(self::ERROR_INPUT_NOT_FOUND);
        }
        $tuple = $this->inputs[$name];

        //Create a reflection class from the class name
        $reflection = new \ReflectionClass($tuple['class']);

        //Check that the input class has the correct interface
        if (!$reflection->implementsInterface(self::INPUT_INTERFACE)) {
            throw new \Exception(self::ERROR_INPUT_INTERFACE);
        }

        //Process the params
        $parameters = [];
        foreach ($tuple['params'] as $param) {
            //Check if a parameter is a reference to a service
            if (is_string($param)) {
                if ('@' === substr($param, 0, 1)) {
                    //Get the item from the container
                    $param = $this->container->get(substr($param, 1));
                }
            }

            //Add to the parameters array
            $parameters[] = $param;
        }

        //Create the input, set its name, and return it
        $input = $reflection->newInstanceArgs($parameters);
        $input->setName($name);

        return $input;
    }

    /**
     * Create and return a ruleset builder for the given ruleset.
     *
     * @param string $name The name of the ruleset to create a builder for
     *
     * @return RulesetBuilderInterface The builder
     */
    public function getRulesetBuilder($name)
    {
        //Create and return the builder
        return new RulesetBuilder($this, $name);
    }

    /**
     * Gets the array of all the contexts registered with the definition manager.
     *
     * @return array The list of contexts
     */
    public function getAllContextDefinitions()
    {
        return $this->contexts;
    }

    /**
     * Get the array of all the action definitions for a given context.
     *
     * @param string $contextName The name of the context to get the action definitions for
     *
     * @return array The array of context action definitions
     */
    public function getContextActionDefinitions($contextName)
    {
        return $this->contextActions[$contextName];
    }

    /**
     * Get the array of all the attribute definitions for a given context.
     *
     * @param string $contextName The name of the context to get the attribute definitions for
     *
     * @return array The array of context attribute definitions
     */
    public function getContextAttributeDefinitions($contextName)
    {
        return $this->contextAttributes[$contextName];
    }

    /**
     * Gets the list of all the rulesets.
     *
     * @return array List of the rulesets
     */
    public function getAllRulesetDefinitions()
    {
        return $this->rulesets;
    }

    /**
     * Gets the list of all the service actions.
     *
     * @return array List of the service actions
     */
    public function getAllServiceActionDefinitions()
    {
        return $this->serviceActions;
    }

    /**
     * Gets the list of all the service attributes.
     *
     * @return array List of the service attributes
     */
    public function getAllServiceAttributeDefinitions()
    {
        return $this->serviceAttributes;
    }

    /**
     * Get the list of all the input definitions.
     *
     * @return array The array of input definitions
     */
    public function getAllInputDefinitions()
    {
        return $this->inputs;
    }

    /**
     * Get a list of all the ruleset names that this definition manager currently recognizes.
     *
     * @return array List of ruleset names
     */
    public function getRulesetNames()
    {
        return array_keys($this->rulesets);
    }

    /**
     * Get all the service attribute objects.
     *
     * @return array All of the service attributes
     */
    public function getAllServiceAttributes()
    {
        $attributes = [];

        //Get all of the service attribute objects
        foreach ($this->serviceAttributes as $name => $tuple) {
            $attributes[] = $this->getServiceAttribute($name);
        }

        //return the array
        return $attributes;
    }

    /**
     * Get all the service action objects.
     *
     * @return array All of the service actions
     */
    public function getAllServiceActions()
    {
        $actions = [];

        //Get all of the service action objects
        foreach ($this->serviceActions as $name => $tuple) {
            $actions[] = $this->getServiceAction($name);
        }

        //Return the array
        return $actions;
    }
}
