<?php

namespace Mesd\RuleBundle\Model\Builder;


interface ActionBuilderInterface
{
    /**
     * Sets the action to the be the service action with the given name in the definition manager.
     *
     * @param string $name The name of the service action in the definition manager
     *
     * @return self
     */
    public function serviceAction($name);

    /**
     * Sets the action to the be the context action with the given context name and action name in the definition manager.
     *
     * @param string $contextName The name of the context owning the action in the definition manager
     * @param string $actionName  The name of the action in the definition manager
     *
     * @return self
     */
    public function contextAction($contextName, $actionName);

    /**
     * The value of the input to give to the actions input object.
     *
     * @param mixed $inputValue The input value
     *
     * @return self
     */
    public function setInputValue($inputValue);

    /**
     * Complete the construction of the action and return the parent builder.
     *
     * @return RuleBuilderInterface The parent builder
     */
    public function end();
}
