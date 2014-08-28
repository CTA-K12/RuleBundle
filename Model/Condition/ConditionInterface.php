<?php

namespace Mesd\RuleBundle\Model\Condition;

interface ConditionInterface
{
    /**
     * Evaluates the condition
     *
     * @return boolean           The result of the condition
     */
    public function evaluate();

    /**
     * Returns true if the condition is a collection of conditions
     *
     * @return boolean Whether the condition is a collection of conditions or not
     */
    public function isCollection();
}