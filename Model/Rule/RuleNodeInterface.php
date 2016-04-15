<?php

namespace Mesd\RuleBundle\Model\Rule;


interface RuleNodeInterface
{
    /**
     * Evaluates the current node.
     *
     * @return boolean The result of the rule evaluation
     */
    public function evaluate();

    /**
     * Return the name of the underlying rule.
     *
     * @return string The name of the underlying rule
     */
    public function getName();

    /**
     * Get the underlying rule object.
     *
     * @return RuleInterface The underlying rule object
     */
    public function getRule();

    /**
     * Add a rule node to the list of rules that will be executed if this rule results in true.
     *
     * @param RuleNodeInterface $thenRule A rule node
     */
    public function addThenRule(RuleNodeInterface $thenRule);

    /**
     * Gets the list of rule nodes that are to be evaluated if this rule node evals to true.
     *
     * @return array Array of RuleNodeInterface
     */
    public function getThenRules();

    /**
     * Add a rukle node to the list of rules that will be executed if this rule results in false.
     *
     * @param RuleNodeInterface $elseRule A rule node
     */
    public function addElseRule(RuleNodeInterface $elseRule);

    /**
     * Gets the list of rule nodes that are to be evaluated if this rule node evals to false.
     *
     * @return array Array of RuleNodeInterface
     */
    public function getElseRules();
}
