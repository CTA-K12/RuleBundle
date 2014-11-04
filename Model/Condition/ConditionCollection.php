<?php

namespace Mesd\RuleBundle\Model\Condition;

use Mesd\RuleBundle\Model\Condition\ConditionInterface;

class ConditionCollection implements ConditionInterface
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Conjunction Disjunction enum
    const ANY_CONDITION = 0;
    const ALL_CONDITION = 1;

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The queue of conditions
     * @var \SplQueue
     */
    private $conditionQueue;

    /**
     * Whether to chain the conditions together by ands or by ors
     * @var int
     */
    private $chain;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param int $chain True to require all the conditions to pass to return true, false to require only one
     */
    public function __construct($chain = null) {
        //Set the chain flag
        if (self::ANY_CONDITION === $chain) {
            $this->chain = self::ANY_CONDITION;
        } else {
            $this->chain = self::ALL_CONDITION;
        }

        //Init the queue
        $this->conditionQueue = new \SplQueue();
    }


    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * The array of contexts
     *
     * @return boolean           The evaluation of the conditions
     */
    public function evaluate() {
        //Give the return a start value (true if we are anding, false if we are oring)
        $return = $this->isAll() ? true : false;

        //Evaluate each condition
        foreach($this->conditionQueue as $condition) {
            $eval = $condition->evaluate();

            //Add in the eval to the current return
            if ($this->isAll()) {
                $return = $return && $eval;
            } else {
                $return = $return || $eval;
            }
            
            //If the ALL is required and return is now false, stop, or if ANY is required and return is true, stop
            if (($this->isAll() && !$return) || ($this->isAny() && $return)) {
                break;
            }
        }

        //Return the return value
        return $return;
    }


    /**
     * Returns true if the condition is a collection of conditions
     *
     * @return boolean Whether the condition is a collection of conditions or not
     */
    public function isCollection() {
        return true;
    }


    /////////////
    // METHODS //
    /////////////


    /**
     * Appends a new condition to this condition collection
     *
     * @param ConditionInterface $condition The condition to add to the collection
     */
    public function addCondition(ConditionInterface $condition) {
        $this->conditionQueue->enqueue($condition);
    }


    /**
     * Return the conditions that make up this collection
     *
     * @return \SplQueue The condition queue
     */
    public function getConditions() {
        return $this->conditionQueue;
    }


    /**
     * Returns true if the conditions in this collection require all to be true
     *
     * @return boolean If conditions are chained by AND
     */
    public function isAll() {
        return self::ALL_CONDITION == $this->chain;
    }


    /**
     * Returns ture if the conditions in this collection require only one to be true
     *
     * @return boolean If conditions are chained by OR
     */
    public function isAny() {
        return self::ANY_CONDITION == $this->chain;
    }
}