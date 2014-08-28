<?php

namespace Mesd\RuleBundle\Model\Rule;

use Mesd\RuleBundle\Model\Action\ActionInterface;
use Mesd\RuleBundle\Model\Condition\ConditionCollectionInterface;

interface RuleInterface
{
    /**
     * Evaluate the rule
     *
     * @return boolean           The resulting evaluation of the rule
     */
    public function evaluate();

    /**
     * Returns the name of the rule
     *
     * @return string The name of the rule
     */
    public function getName();

    /**
     * Returns the description of the rule (or null if there is none)
     *
     * @return string|null The description of the rule
     */
    public function getDescription();

    /**
     * Add an action to be called when the rule evals to true
     *
     * @param ActionInterface $action The action to call
     */
    public function addThenAction(ActionInterface $action);

    /**
     * Get the actions to be called when the rule evals to true
     *
     * @return \SplQueue The then actions
     */
    public function getThenActions();

    /**
     * Add an action to be called when the rule evals to false
     *
     * @param ActionInterface $action The action to call
     */
    public function addElseAction(ActionInterface $action);

    /**
     * Get the actions to be called when the rule evals to false
     *
     * @return \SplQueue The else actions
     */
    public function getElseActions();

    /**
     * Get the condition collection of the rule
     *
     * @return ConditionCollectionInterface The root condition collection
     */
    public function getConditions();
}