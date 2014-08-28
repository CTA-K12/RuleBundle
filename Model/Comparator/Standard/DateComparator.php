<?php

namespace Mesd\RuleBundle\Model\Comparator\Standard;

use Mesd\RuleBundle\Model\Comparator\ComparatorInterface;
use Mesd\RuleBundle\Model\Comparator\Operator;

class DateComparator implements ComparatorInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The current operator
     * @var Operator
     */
    private $currentOperator;

    /**
     * The array of operators that can be used in this comparator
     * @var array
     */
    private $operators;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     */
    public function __construct() {
        //Generate the operator array
        $this->operators = [];
        $this->operators['eq'] = new Operator('eq', 'equals', false);
        $this->operators['neq'] = new Operator('neq', 'not equals', false);
        $this->operators['isbef'] = new Operator('isbef', 'is before', false);
        $this->operators['isbefon'] = new Operator('isbefon', 'is before or on', false);
        $this->operators['isaft'] = new Operator('isaft', 'is after', false);
        $this->operators['isafton'] = new Operator('isafton', 'is after or on', false);
        $this->operators['in'] = new Operator('in', 'is one of', true);
    }


    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Gets the operators associated with this comparator
     * 
     * @return array Array of operator classes
     */
    public function getOperators() {
        return $this->operators;
    }


    /**
     * Sets the current operator to the operator with the given value
     *
     * @param string $operatorValue The value of the operator to set as current
     */
    public function setCurrentOperator($operatorValue) {
        if (array_key_exists($operatorValue, $this->operators)) {
            $this->currentOperator = $this->operators[$operatorValue];
        }
    }


    /**
     * Gets the current operator
     *
     * @return Operator The current operator
     */
    public function getCurrentOperator() {
        return $this->currentOperator;
    }


    /**
     * Compare the values with the operator currently set
     *
     * @param  mixed   $leftValue     The left value
     * @param  mixed   $rightValue    The right value
     *
     * @return boolean                The result of the comparison
     */
    public function compare($leftValue, $rightValue) {
        if (null !== $this->currentOperator) {
            switch ($this->currentOperator->getValue()) {
                case 'eq':
                    $res = ($leftValue->format('Y-m-d') == $rightValue->format('Y-m-d'));
                    break;
                case 'neq':
                    $res = ($leftValue->format('Y-m-d') != $rightValue->format('Y-m-d'));
                    break;
                case 'isbef':
                    $res = ($leftValue->format('Y-m-d') < $rightValue->format('Y-m-d'));
                    break;
                case 'isbefon':
                    $res = ($leftValue->format('Y-m-d') <= $rightValue->format('Y-m-d'));
                    break;
                case 'isaft':
                    $res = ($leftValue->format('Y-m-d') > $rightValue->format('Y-m-d'));
                    break;
                case 'isafton':
                    $res = ($leftValue->format('Y-m-d') >= $rightValue->format('Y-m-d'));
                    break;
                case 'in':
                    if (is_array($rightValue)) {
                        $res = in_array($leftValue, $rightValue);
                    } else {
                        $res = ($leftValue == $rightValue);
                    }
                    break;
                default:
                    $res = false;
                    break;
            }
        } else {
            $res = false;
        }

        return $res;
    }
}