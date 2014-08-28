<?php

namespace Mesd\RuleBundle\Model\Comparator;

use Mesd\RuleBundle\Model\Compoerator\Operator;

interface ComparatorInterface {

    /**
     * Gets the operators associated with this comparator
     * 
     * @return array Array of operator classes
     */
    public function getOperators();

    /**
     * Sets the current operator to the operator with the given value
     *
     * @param string $operatorValue The value of the operator to set as current
     */
    public function setCurrentOperator($operatorValue);

    /**
     * Gets the current operator
     *
     * @return Operator The current operator
     */
    public function getCurrentOperator();

    /**
     * Compare the values with the operator currently set
     *
     * @param  mixed   $leftValue     The left value
     * @param  mixed   $rightValue    The right value
     *
     * @return boolean                The result of the comparison
     */
    public function compare($leftValue, $rightValue);
}