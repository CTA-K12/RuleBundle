<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Action\ActionInterface;
use Mesd\RuleBundle\Model\Condition\ConditionCollection;
use Mesd\RuleBundle\Model\Context\ContextCollectionInterface;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;
use Mesd\RuleBundle\Model\Rule\Rule;
use Mesd\RuleBundle\Model\Rule\RuleNode;

class RuleBuilder implements RuleBuilderInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The underlying rule.
     *
     * @var Rule
     */
    private $rule;

    /**
     * The rule node that will wrap the underlying rule.
     *
     * @var RuleNode
     */
    private $ruleNode;

    /**
     * The parent ruleset builder.
     *
     * @var RulesetBuilderInterface
     */
    private $parentBuilder;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param RulesetBuilderInterface $parentBuilder The parent ruleset builder
     * @param string                  $name          The name to give the rule
     */
    public function __construct(RulesetBuilderInterface $parentBuilder, $name)
    {
        //Set stuff
        $this->parentBuilder = $parentBuilder;

        //Intialize the new rule
        $this->rule = new Rule($name);

        //Intialize the rule node
        $this->ruleNode = new RuleNode($this->rule);
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Returns a new condition collection builder.
     *
     * @param int $chain The chain flag (either ConditionCollection::ALL or ConditionCollection::ANY)
     *
     * @return ConditionCollectionBuilderInterface The condition collection builder
     */
    public function startConditionCollection($chain)
    {
        //Create a new condition collection builder
        return new ConditionCollectionBuilder($this, $chain);
    }

    /**
     * Shorthand for this->conditions(ConditionCollection::ANY).
     *
     * @return ConditionCollectionBuilderInterface The condition collection builder
     */
    public function startConditionCollectionAny()
    {
        return $this->startConditionCollection(ConditionCollection::ANY_CONDITION);
    }

    /**
     * Shorthand for this->conditions(ConditionCollection::ANY).
     *
     * @return ConditionCollectionBuilderInterface The condition collection builder
     */
    public function startConditionCollectionAll()
    {
        return $this->startConditionCollection(ConditionCollection::ALL_CONDITION);
    }

    /**
     * Start a new action to be called if the rule evals to true.
     *
     * @return ActionBuilderInterface Builder for the new action
     */
    public function startThenAction()
    {
        //Create a new builder and return it
        return new ActionBuilder($this, ActionBuilder::TYPE_THEN);
    }

    /**
     * Start a new action to be called if the rule evals to false.
     *
     * @return ActionBuilderInterface Builder for the new action
     */
    public function startElseAction()
    {
        //Create a new builder and return it
        return new ActionBuilder($this, ActionBuilder::TYPE_ELSE);
    }

    /**
     * Add an action to be called if the conditions evaluate to true.
     *
     * @param ActionInterface $action The action to add
     */
    public function addThenAction(ActionInterface $action)
    {
        //Add the action to the underlying rule
        $this->rule->addThenAction($action);
    }

    /**
     * Add an action to be called if the conidtions evaluate to false.
     *
     * @param ActionInterface $action THe action to add
     */
    public function addElseAction(ActionInterface $action)
    {
        //Add the action to the underlying rule
        $this->rule->addElseAction($action);
    }

    /**
     * Add a rule to process if this rule evals to true.
     *
     * @param string $name The name of the rule
     *
     * @return self
     */
    public function addThenRule($name)
    {
        //Register the rule mapping with the ruleset builder
        $this->parentBuilder->addThenRule($this->rule->getName(), $name);

        return $this;
    }

    /**
     * Add a rule to process if this evals to false.
     *
     * @param string $name The name of the rule
     *
     * @return self
     */
    public function addElseRule($name)
    {
        //Register the rule mapping with the ruleset builder
        $this->parentBuilder->addElseRule($this->rule->getName(), $name);

        return $this;
    }

    /**
     * Method to return the original RulesetBuilder to allow for chaining.
     *
     * @return RulesetBuilderInterface The original rulesetbuilder that spawned this rule builder
     */
    public function end()
    {
        //Add the rule node to the ruleset builder
        $this->parentBuilder->addRuleNode($this->ruleNode);

        //Return the parent rulebuilder
        return $this->parentBuilder;
    }

    /**
     * Add or set the condtions collection of the object.
     *
     * @param CondtionCollection $condtionCollection The condition collection to add or set
     */
    public function addConditionCollection(ConditionCollection $conditionCollection)
    {
        $this->rule->setConditions($conditionCollection);
    }

    /**
     * Returns a reference to the defintion manager.
     *
     * @return DefinitionManagerInterface The defintion manager
     */
    public function getDefinitionManager()
    {
        return $this->parentBuilder->getDefinitionManager();
    }

    /**
     * Get the context collection currently associated with the ruleset.
     *
     * @return ContextCollectionInterface The context collection
     */
    public function getContextCollection()
    {
        return $this->parentBuilder->getContextCollection();
    }
}
