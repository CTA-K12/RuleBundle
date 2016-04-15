<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Condition\ConditionInterface;
use Mesd\RuleBundle\Model\Context\ContextCollectionAwareInterface;

interface ConditionCollectionBuilderInterface extends ConditionCollectionContainableInterface, ContextCollectionAwareInterface
{
    /**
     * starts a new condition to add to the collection.
     *
     * @return ConditionBuilderInterface A builder for the new condition
     */
    public function startCondition();

    /**
     * Add a condition object to the collection.
     *
     * @param ConditionInterface $condition The condition  object to add to the collection
     */
    public function addCondition(ConditionInterface $condition);

    /**
     * Ends the construction of the current collection.
     *
     * @return ConditionCollectionContainableInterface The parent builder
     */
    public function end();
}
