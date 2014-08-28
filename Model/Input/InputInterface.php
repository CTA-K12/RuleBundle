<?php

namespace Mesd\RuleBundle\Model\Input;

use Symfony\Component\Form\FormBuilderInterface;

interface InputInterface
{
    /**
     * Get the value that the input represents
     *
     * @return mixed The input value
     */
    public function getValue();

    /**
     * Set the raw input of the value from the form
     *
     * @param mixed $rawInput The raw input (e.g. An entity id or string from form input)
     */
    public function setRawInput($rawInput);

    /**
     * Get the raw input value
     *
     * @return mixed The raw input value
     */
    public function getRawInput();

    /**
     * Returns the string name of the form type
     *
     * @return string Form type
     */
    public function getFormType();

    /**
     * Returns the options array for the form
     *
     * @return array The form options
     */
    public function getFormOptions();

    /**
     * Return the name assigned to the input
     *
     * @return string The inputs name
     */
    public function getName();

    /**
     * Sets the name of the input
     *
     * @param string $name The name to assign to the input
     */
    public function setName($name);
}