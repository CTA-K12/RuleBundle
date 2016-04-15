<?php

namespace Mesd\RuleBundle\Model\Definition;

use Mesd\RuleBundle\Model\Action\AbstractContextAction;
use Mesd\RuleBundle\Model\Action\AbstractServiceAction;
use Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute;
use Mesd\RuleBundle\Model\Attribute\AbstractServiceAttribute;
use Mesd\RuleBundle\Model\Builder\RulesetBuilderInterface;
use Mesd\RuleBundle\Model\Context\ContextInterface;
use Mesd\RuleBundle\Model\Input\InputInterface;
use Mesd\RuleBundle\Model\Ruleset\RulesetInterface;

interface DefinitionManagerInterface
{
    /**
     * Registers an input with the Definition Manager.
     *
     * @param string $name      The name to identity the input with
     * @param string $className The name of the input class
     * @param array  $params    The parameters to init the input with following service definition format
     * @param  self
     */
    public function registerInput($name, $className, $params = []);

    /**
     * Registers a context with the Definition Manager.
     *
     * @param string $name               The name to identify the context with
     * @param string $classificationName The name of the classification (e.g. MyBundle\Entity\LaserShark)
     * @param string $classificationType The type of thing it is (e.g. object, interface, primative)
     *
     * @return self
     */
    public function registerContext($name, $classificationName, $classificationType);

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
    public function registerContextAttribute($name, $contextName, $className, $inputName);

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
    public function registerContextAction($name, $contextName, $className, $inputName);

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
    public function registerServiceAttribute($name, $serviceName, $className, $inputName);

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
    public function registerServiceAction($name, $serviceName, $className, $inputName);

    /**
     * Registers a ruleset with the Context Manager.
     *
     * @param string $name     The name of the ruleset
     * @param array  $children The array of children in the following format:
     *                         array('contexts' => array('Log', 'User'),
     *                         'serviceAttributes' => array('Current Date', 'Users Logged In'),
     *                         'serviceActions' => array('Explode Server', 'Mass Log Out'))
     *
     * @return self
     */
    public function registerRuleset($name, $children = []);

    /**
     * Gets the ruleset object associated with the given name.
     *
     * @param string $name The name registered with the ruleset
     *
     * @return RulesetInterface The ruleset registered under the given name
     */
    public function getRuleset($name);

    /**
     * Gets the context registered under the given name.
     *
     * @param string $name The name registered with the context
     *
     * @return ContextItnerface The context registered under the given name
     */
    public function getContext($name);

    /**
     * Gets the a context attribute by context and attribute names.
     *
     * @param string $contextName   The name of the parent context
     * @param string $attributeName The name of the attribute
     *
     * @return AbstractContextAttribute The attribute
     */
    public function getContextAttribute($contextName, $attributeName);

    /**
     * Gets the a context action by context and action names.
     *
     * @param string $contextName The name of the parent context
     * @param string $actionName  The name of the action
     *
     * @return AbstractContextAction The action
     */
    public function getContextAction($contextName, $actionName);

    /**
     * Gets a service attribute by name.
     *
     * @param string $name The name of the service attribute
     *
     * @return AbstractServiceAttribute The service attribute
     */
    public function getServiceAttribute($name);

    /**
     * Gets a service action by name.
     *
     * @param string $name The name of the service action
     *
     * @return AbstractServiceAction The service action
     */
    public function getServiceAction($name);

    /**
     * Gets the input registered under the given name.
     *
     * @param string $name The name the input is registered under
     *
     * @return InputInterface The input registered under the given name
     */
    public function getInput($name);

    /**
     * Create and return a ruleset builder for the given ruleset.
     *
     * @param string $name The name of the ruleset to create a builder for
     *
     * @return RulesetBuilderInterface The builder
     */
    public function getRulesetBuilder($name);

    /**
     * Gets the context registered under the given name along with the associated attributes and actions.
     *
     * @param string $name The name of the context
     *
     * @return ContextInterface The context
     */
    public function getContextWithAttributesAndActions($name);

    /**
     * Gets the array of attributes associated with the given context.
     *
     * @param string $name The name of the parent context
     *
     * @return array The list of attributes
     */
    public function getAllContextAttributes($name);

    /**
     * Gets the array of actions associated with the given context.
     *
     * @param string $name The name of the parent context
     *
     * @return array The list of actions
     */
    public function getAllContextActions($name);

    /**
     * Gets the array of all the contexts registered with the definition manager.
     *
     * @return array The list of contexts
     */
    public function getAllContextDefinitions();

    /**
     * Get the array of all the action definitions for a given context.
     *
     * @param string $contextName The name of the context to get the action definitions for
     *
     * @return array The array of context action definitions
     */
    public function getContextActionDefinitions($contextName);
    /**
     * Get the array of all the attribute definitions for a given context.
     *
     * @param string $contextName The name of the context to get the attribute definitions for
     *
     * @return array The array of context attribute definitions
     */
    public function getContextAttributeDefinitions($contextName);
    /**
     * Gets the list of all the rulesets.
     *
     * @return array List of the rulesets
     */
    public function getAllRulesetDefinitions();

    /**
     * Gets the list of all the service actions.
     *
     * @return array List of the service actions
     */
    public function getAllServiceActionDefinitions();

    /**
     * Gets the list of all the service attributes.
     *
     * @return array List of the service attributes
     */
    public function getAllServiceAttributeDefinitions();

    /**
     * Get the list of all the input definitions.
     *
     * @return array The array of input definitions
     */
    public function getAllInputDefinitions();

    /**
     * Get a list of all the ruleset names that this definition manager currently recognizes.
     *
     * @return array List of ruleset names
     */
    public function getRulesetNames();

    /**
     * Get all the service attribute objects.
     *
     * @return array All of the service attributes
     */
    public function getAllServiceAttributes();

    /**
     * Get all the service action objects.
     *
     * @return array All of the service actions
     */
    public function getAllServiceActions();
}
