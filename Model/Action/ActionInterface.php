<?php

namespace Mesd\RuleBundle\Model\Action;

use Mesd\RuleBundle\Model\Input\InputInterface;

interface ActionInterface
{
    /**
     * Gets the name of the action.
     *
     * @return string The Actions name
     */
    public function getName();

    /**
     * Sets the name of the action.
     *
     * @param string $name The name to assign to the action
     */
    public function setName($name);

    /**
     * Gets the description of the action.
     *
     * @return string|null The Actions description if it exists
     */
    public function getDescription();

    /**
     * Get the name of the parent context/service.
     *
     * @return string The parent name
     */
    public function getParentName();

    /**
     * Perform the action.
     */
    public function perform();

    /**
     * Sets the input to be used by the rule form when this action is selected.
     *
     * @param InputInterface $input The input interface to set
     */
    public function setInput(InputInterface $input);

    /**
     * Gets the input used by this action for the rule form.
     *
     * @return InputInterface The input used by this action
     */
    public function getInput();

    /**
     * Sets the value of the input.
     *
     * @param mixed $value The input value
     */
    public function setInputValue($value);

    /**
     * Get the raw input value.
     *
     * @return mixed The raw input value
     */
    public function getInputValue();
}
