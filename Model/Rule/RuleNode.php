<?php

namespace Mesd\RuleBundle\Model\Rule;

use Mesd\RuleBundle\Model\Rule\RuleNodeInterface;

class RuleNode implements RuleNodeInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The underlying rule
     * @var RuleInterface
     */
    private $rule;

    /**
     * Rules to evaluate if this rule evals to true
     * @var array
     */
    private $thenRules;

    /**
     * Rules to evaluate if this rule evals to false
     * @var array
     */
    private $elseRules;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param RuleInterface $rule The rule to have this node encapsulate
     */
    public function __construct(RuleInterface $rule) {
        //Set stuff
        $this->rule = $rule;

        //Init stuff
        $this->thenRules = array();
        $this->elseRules = array();
    }


    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Evaluates the current node
     *
     * @return boolean The result of the rule evaluation
     */
    public function evaluate() {
        //evaluate the underlying rule
        return $this->rule->evaluate();
    }


    /**
     * Return the name of the underlying rule
     *
     * @return string The name of the underlying rule
     */
    public function getName() {
        return $this->rule->getName();
    }


    /**
     * Get the underlying rule object
     *
     * @return RuleInterface The underlying rule objec
     */
    public function getRule() {
        return $this->rule;
    }


    /**
     * Add a rule node to the list of rules that will be executed if this rule results in true
     *
     * @param RuleNodeInterface $thenRule A rule node
     */
    public function addThenRule(RuleNodeInterface $thenRule) {
        $this->thenRules[$thenRule->getName()] = $thenRule;
    }

    /**
     * Gets the list of rule nodes that are to be evaluated if this rule node evals to true
     *
     * @return array Array of RuleNodeInterface
     */
    public function getThenRules() {
        return $this->thenRules;
    }

    /**
     * Add a rukle node to the list of rules that will be executed if this rule results in false
     *
     * @param RuleNodeInterface $elseRule A rule node
     */
    public function addElseRule(RuleNodeInterface $elseRule) {
        $this->elseRules[$elseRule->getName()] = $elseRule;
    }

    /**
     * Gets the list of rule nodes that are to be evaluated if this rule node evals to false
     *
     * @return array Array of RuleNodeInterface
     */
    public function getElseRules() {
        return $this->elseRules;
    }
}