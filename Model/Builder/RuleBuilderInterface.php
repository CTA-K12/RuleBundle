<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Action\ActionInterface;
use Mesd\RuleBundle\Model\Context\ContextCollectionAwareInterface;

interface RuleBuilderInterface extends ConditionCollectionContainableInterface, ContextCollectionAwareInterface
{
    /**
     * Start a new action to be called if the rule evals to true.
     *
     * @return ActionBuilderInterface Builder for the new action
     */
    public function startThenAction();

    /**
     * Start a new action to be called if the rule evals to false.
     *
     * @return ActionBuilderInterface Builder for the new action
     */
    public function startElseAction();

    /**
     * Add an action to be called if the conditions evaluate to true.
     *
     * @param ActionInterface $action The action to add
     */
    public function addThenAction(ActionInterface $action);

    /**
     * Add an action to be called if the conidtions evaluate to false.
     *
     * @param ActionInterface $action THe action to add
     */
    public function addElseAction(ActionInterface $action);

    /**
     * Add a rule to process if this rule evals to true.
     *
     * @param string $name The name of the rule
     *
     * @return self
     */
    public function addThenRule($name);

    /**
     * Add a rule to process if this evals to false.
     *
     * @param string $name The name of the rule
     *
     * @return self
     */
    public function addElseRule($name);

    /**
     * Method to return the original RulesetBuilder to allow for chaining.
     *
     * @return RulesetBuilderInterface The original rulesetbuilder that spawned this rule builder
     */
    public function end();
}
