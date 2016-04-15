<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Action\ActionInterface;

class ActionBuilder implements ActionBuilderInterface
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Enum
    const TYPE_THEN = 0;
    const TYPE_ELSE = 1;

    //Errors
    const ERROR_NOT_VALID_TYPE = 'The given type is not valid';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The underlying action.
     *
     * @var ActionInterface
     */
    private $action;

    /**
     * The input value.
     *
     * @var mixed
     */
    private $inputValue;

    /**
     * The parent rule builder.
     *
     * @var RuleBuilderInterface
     */
    private $parentBuilder;

    /**
     * The type of action this (e.g. Whether a THEN or ELSE).
     *
     * @var int
     */
    private $type;

    ////////////////////
    // STATIC METHODS //
    ////////////////////


    /**
     * Returns the array of valid types.
     *
     * @return array Valid types (Name => enum)
     */
    public static function getValidTypes()
    {
        return ['then' => self::TYPE_THEN, 'else' => self::TYPE_ELSE];
    }

    /**
     * Checks whether a give int value matches a valid type.
     *
     * @param int $type The int value of the type to check
     *
     * @return boolean Whether the given type value is valid or not
     */
    public static function isValidType($type)
    {
        return in_array($type, self::getValidTypes());
    }

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param RuleBuilderInterface $parentBuilder The parent builder
     */
    public function __construct(RuleBuilderInterface $parentBuilder, $type)
    {
        //Check the validatity of type
        if (!self::isValidType($type)) {
            throw new \Exception(self::ERROR_NOT_VALID_TYPE);
        }

        //Set stuff
        $this->type          = $type;
        $this->parentBuilder = $parentBuilder;
        $this->action        = null;
        $this->inputValue    = null;
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Sets the action to the be the service action with the given name in the definition manager.
     *
     * @param string $name The name of the service action in the definition manager
     *
     * @return self
     */
    public function serviceAction($name)
    {
        //Get the action from the parent builders definition manager reference
        $this->action = $this->parentBuilder->getDefinitionManager()->getServiceAction($name);

        return $this;
    }

    /**
     * Sets the action to the be the context action with the given context name and action name in the definition manager.
     *
     * @param string $contextName The name of the context owning the action in the definition manager
     * @param string $actionName  The name of the action in the definition manager
     *
     * @return self
     */
    public function contextAction($contextName, $actionName)
    {
        //Get the action from the ruleset builders context collection
        $this->action = $this->parentBuilder->getContextCollection()->createContextAction($contextName, $actionName);

        return $this;
    }

    /**
     * The value of the input to give to the actions input object.
     *
     * @param mixed $inputValue The input value
     *
     * @return self
     */
    public function setInputValue($inputValue)
    {
        //Save input value until the end method is called
        $this->inputValue = $inputValue;

        return $this;
    }

    /**
     * Complete the construction of the action and return the parent builder.
     *
     * @return RuleBuilderInterface The parent builder
     */
    public function end()
    {
        //Bundle up everything
        $this->action->setInputValue($this->inputValue);

        //Give the parent builder the new action
        if (self::TYPE_THEN === $this->type) {
            $this->parentBuilder->addThenAction($this->action);
        } elseif (self::TYPE_ELSE === $this->type) {
            $this->parentBuilder->addElseAction($this->action);
        }

        //Return the parent builder
        return $this->parentBuilder;
    }
}
