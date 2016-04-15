<?php

namespace Mesd\RuleBundle\Model\Rule;

use Mesd\RuleBundle\Model\Action\ActionInterface;
use Mesd\RuleBundle\Model\Condition\ConditionCollection;

class Rule implements RuleInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The collection of conditions that make up the if section of a given rule.
     *
     * @var ConditionCollection
     */
    private $conditions;

    /**
     * The name of the rule.
     *
     * @var string
     */
    private $name;

    /**
     * The description of the rule.
     *
     * @var string
     */
    private $description;

    /**
     * The then actions.
     *
     * @var \SplQueue
     */
    private $thenActions;

    /**
     * The else actions.
     *
     * @var \SplQueue
     */
    private $elseActions;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     */
    public function __construct($name)
    {
        //Set the name of the rule
        $this->name = $name;

        //Construct a default condition chain as a placeholder
        $this->conditions = new ConditionCollection();

        //Init the action queues
        $this->thenActions = new \SplQueue();
        $this->elseActions = new \SplQueue();
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Evaluate the rule.
     *
     * @return boolean The resulting evaluation of the rule
     */
    public function evaluate()
    {
        //Eval the conditions
        $eval = $this->conditions->evaluate();

        //Run the then/else actions
        if ($eval) {
            foreach ($this->thenActions as $action) {
                $action->perform();
            }
        } else {
            foreach ($this->elseActions as $action) {
                $action->perform();
            }
        }

        //return eval
        return $eval;
    }

    /**
     * Returns the name of the rule.
     *
     * @return string The name of the rule
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the description of the rule (or null if there is none).
     *
     * @return string|null The description of the rule
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add an action to be called when the rule evals to true.
     *
     * @param ActionInterface $action The action to call
     */
    public function addThenAction(ActionInterface $action)
    {
        $this->thenActions->enqueue($action);
    }

    /**
     * Get the actions to be called if the rule evals to true.
     *
     * @return \SplQueue The then actions
     */
    public function getThenActions()
    {
        return $this->thenActions;
    }

    /**
     * Add an action to be called when the rule evals to false.
     *
     * @param ActionInterface $action The action to call
     */
    public function addElseAction(ActionInterface $action)
    {
        $this->elseActions->enqueue($action);
    }

    /**
     * Get the actions to be called if the rule evals to false.
     *
     * @return \SplQueue The else actions
     */
    public function getElseActions()
    {
        return $this->elseActions;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Gets the The collection of conditions that make up the if section of a given rule.
     *
     * @return ConditionCollection
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Sets the collection of conditions that make up the if section of the rule.
     *
     * @param ConditionCollection $conditions The condition collection to have this rule evaluate
     */
    public function setConditions(ConditionCollection $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * Sets the The name of the rule.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the The description of the rule.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
