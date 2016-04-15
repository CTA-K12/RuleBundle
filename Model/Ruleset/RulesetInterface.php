<?php

namespace Mesd\RuleBundle\Model\Ruleset;

use Mesd\RuleBundle\Model\Action\AbstractServiceAction;
use Mesd\RuleBundle\Model\Attribute\AbstractServiceAttribute;
use Mesd\RuleBundle\Model\Context\ContextCollectionAwareInterface;
use Mesd\RuleBundle\Model\Rule\RuleNodeInterface;

interface RulesetInterface extends ContextCollectionAwareInterface
{
    /**
     * Evaluate the enter rule set with the context objects set.
     *
     * @param  array   The array of values to set the contexts by
     *
     * @return boolean The result of the evaluation
     */
    public function evaluate($contextValues = []);

    /**
     * Adds a rule node to the list of root rule nodes that are executed initially upon evaluation.
     *
     * @param RuleNodeInterface $ruleNode A rule node
     */
    public function addRootRuleNode(RuleNodeInterface $ruleNode);

    /**
     * Adds a service attribute to the ruleset.
     *
     * @param AbstractServiceAttribute $attribute The service attribute to add
     */
    public function addServiceAttribute(AbstractServiceAttribute $attribute);

    /**
     * Gets the list of service attributes associated with the ruleset.
     *
     * @return array Array of AbstractServiceAttribute
     */
    public function getServiceAttributes();

    /**
     * Adds a service action to the ruleset.
     *
     * @param AbstractServiceAction $action The service action to add
     */
    public function addServiceAction(AbstractServiceAction $action);

    /**
     * Gets the list of service actions associated with the ruleset.
     *
     * @return array Array of AbstractServiceAction
     */
    public function getServiceActions();

    /**
     * Returns a list of all the attributes this ruleset knows of.
     *
     * @return array All Attributes associated with the ruleset
     */
    public function getAllAttributes();

    /**
     * Returns a list of all the actions this ruleset knows of.
     *
     * @return array All Actions associated with the ruleset
     */
    public function getAllActions();

    /**
     * Get the name of the ruleset.
     *
     * @return string The name of the ruleset
     */
    public function getName();
}
