<?php

namespace Mesd\RuleBundle\Model\Comparator;

class Operator
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The value that will be used in the option tag for the operator
     * @var string
     */
    private $value;

    /**
     * The english name that will be used as the option text for the operator
     * @var string
     */
    private $name;

    /**
     * Whether the option allows for multiple inputs
     * @var boolean
     */
    private $multiple;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param string  $value    The value that will be used in the option tag for the operator
     * @param string  $name     The english name that will be used as the option text for the operator
     * @param boolean $multiple Whether the option allows for multiple inputs
     */
    public function __construct($value, $name, $multiple = false) {
        //Set stuff
        $this->value = $value;
        $this->name = $name;
        $this->multiple = $multiple;
    }


    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Gets the The value that will be used in the option tag for the operator.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the The value that will be used in the option tag for the operator.
     *
     * @param string $value the value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the The english name that will be used as the option text for the operator.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the The english name that will be used as the option text for the operator.
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
     * Gets the Whether the option allows for multiple inputs.
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Sets the Whether the option allows for multiple inputs.
     *
     * @param boolean $multiple the multiple
     *
     * @return self
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }
}