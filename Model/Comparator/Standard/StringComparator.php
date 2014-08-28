<?php

namespace Mesd\RuleBundle\Model\Comparator\Standard;

use Mesd\RuleBundle\Model\Comparator\ComparatorInterface;
use Mesd\RuleBundle\Model\Comparator\Operator;

class StringComparator implements ComparatorInterface
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
        $this->operators['ispre'] = new Operator('ispre', 'is prefix of', false);
        $this->operators['haspre'] = new Operator('haspre', 'has prefix of', false);
        $this->operators['issuf'] = new Operator('issuf', 'is suffix of', false);
        $this->operators['hassuf'] = new Operator('hassuf', 'has suffix of', false);
        $this->operators['contns'] = new Operator('contns', 'contains', false);
        $this->operators['contnd'] = new Operator('contnd', 'is contained in', false);
        $this->operators['ltabc'] = new Operator('ltabc', 'comes alphabetically before', false);
        $this->operators['gtabc'] = new Operator('gtabc', 'comes alphabetically after', false);
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
                    $res = (strtolower($leftValue) == strtolower($rightValue));
                    break;
                case 'neq':
                    $res = (strtolower($leftValue) != strtolower($rightValue));
                    break;
                case 'ispre':
                    $res = (1 == preg_match('/^' . $leftValue . '/i', $rightValue));
                    break;
                case 'haspre':
                    $res = (1 == preg_match('/^' . $rightValue . '/i', $leftValue));
                    break;
                case 'issuf':
                    $res = (1 == preg_match('/' . $leftValue . '$/i', $rightValue));
                    break;
                case 'hassuf':
                    $res = (1 == preg_match('/' . $rightValue . '$/i', $leftValue));
                    break;
                case 'contns':
                    $res = (1 == preg_match('/' . $leftValue . '/i', $rightValue));
                    break;
                case 'contnd':
                    $res = (1 == preg_match('/' . $rightValue . '/i', $leftValue));
                    break;
                case 'ltabc':
                    $res = (0 <= strcmp($leftValue, $rightValue));
                    break;
                case 'gtabc':
                    $res = (0 <= strcmp($rightValue, $leftValue));
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