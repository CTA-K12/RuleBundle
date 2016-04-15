<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Context\ContextCollectionAwareInterface;
use Mesd\RuleBundle\Model\Rule\RuleNodeInterface;
use Mesd\RuleBundle\Model\Ruleset\RulesetInterface;

interface RulesetBuilderInterface extends ContextCollectionAwareInterface
{
    /**
     * Starts a new rule to add to the ruleset and returns the rule builder.
     *
     * @param string $name The name of the new rule
     *
     * @return RuleBuilderInterface The new builder for the new rule
     */
    public function startRule($name);

    /**
     * Add a rule node to the ruleset.
     *
     * @param RuleNodeInterface $ruleNode The rule node to add
     *
     * @return self
     */
    public function addRuleNode(RuleNodeInterface $ruleNode);

    /**
     * Registers that a given rule will follow another rule if that rule evals to true.
     *
     * @param string $parentName The name of the initial rule
     * @param string $thenName   The name of the rule to goto if the parent rule evals to true
     *
     * @return self
     */
    public function addThenRule($parentName, $thenName);

    /**
     * Registers that a given rule will follow another rule if that rule evals to false.
     *
     * @param string $parentName The name of the initial rule
     * @param string $elseName   The name of the rule to goto if the parent rule evals to false
     *
     * @return self
     */
    public function addElseRule($parentName, $elseName);

    /**
     * Builds and returns the updated ruleset.
     *
     * @return RulesetInterface The updated ruleset
     */
    public function build();
}
