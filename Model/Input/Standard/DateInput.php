<?php

namespace Mesd\RuleBundle\Model\Input\Standard;

use Mesd\RuleBundle\Model\Input\InputInterface;

use Symfony\Component\Form\FormBuilderInterface;

class DateInput implements InputInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The inputted value
     * @var \DateTime
     */
    private $value;

    /**
     * The raw input value
     * @var mixed
     */
    private $rawInput;

    /**
     * The name assigned to the input
     * @var string
     */
    private $name;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     */
    public function __construct() {
        //Nothing here for now
    }


    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Get the value that the input represents
     *
     * @return int The input value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set the raw input of the value from the form
     *
     * @param mixed $rawInput The raw input (e.g. An entity id or string from form input)
     */
    public function setRawInput($rawInput) {
        //Convert from Y/m/d to datetime
        $this->value = new \DateTime($rawInput,, new DateTimeZone('America/Los_Angeles'));
        $this->rawInput = $rawInput;
    }


    /**
     * Get the raw input value
     *
     * @return mixed The raw input value
     */
    public function getRawInput() {
        return $this->rawInput;
    }

    /**
     * Returns the string name of the form type
     *
     * @return string Form type
     */
    public function getFormType() {
        return 'datetime';
    }

    /**
     * Returns the options array for the form
     *
     * @return array The form options
     */
    public function getFormOptions() {
        return array('widget' => 'single_text', 'format' => 'yyyy-MM-dd');
    }


    /**
     * Gets the name assigned to the input
     *
     * @return string The name of the input
     */
    public function getName() {
        return $this->name;
    }


    /**
     * Assigns a name to the input
     *
     * @param string $name The name to assign to the input
     */
    public function setName($name) {
        $this->name = $name;
    }
}