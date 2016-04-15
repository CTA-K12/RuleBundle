<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Condition\ConditionCollection;
use Mesd\RuleBundle\Model\Condition\ConditionInterface;
use Mesd\RuleBundle\Model\Context\ContextCollectionInterface;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;

class ConditionCollectionBuilder implements ConditionCollectionBuilderInterface
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The parent builder object.
     *
     * @var ConditionCollectionContainableInterface
     */
    private $parentBuilder;

    /**
     * The underlying condition collection.
     *
     * @var ConditionCollection
     */
    private $conditionCollection;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param ConditionCollectionContainableInterface $parentBuilder The parent builder object
     * @param int                                     $chain         The chain type
     */
    public function __construct(ConditionCollectionContainableInterface $parentBuilder, $chain)
    {
        //Set stuff
        $this->parentBuilder = $parentBuilder;

        //Initialize
        $this->conditionCollection = new ConditionCollection($chain);
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * starts a new condition to add to the collection.
     *
     * @return ConditionBuilderInterface A builder for the new condition
     */
    public function startCondition()
    {
        //Create a new condition builder
        return new ConditionBuilder($this);
    }

    /**
     * Add a condition object to the collection.
     *
     * @param ConditionInterface $condition The condition  object to add to the collection
     *
     * @return self
     */
    public function addCondition(ConditionInterface $condition)
    {
        $this->conditionCollection->addCondition($condition);

        return $this;
    }

    /**
     * Start a new condition collection to embed in this collection.
     *
     * @param int $chain A flag pertaining to whether this is any or all collection
     *
     * @return ConditionCollectionBuilderInterface Builder for the new collection
     */
    public function startConditionCollection($chain)
    {
        return new ConditionCollectionBuilder($this, $chain);
    }

    /**
     * Short hand for the startConditionCollection(ANY).
     *
     * @return ConditionCollectionBuilderInterface Builder for the new collection
     */
    public function startConditionCollectionAll()
    {
        return $this->startConditionCollection(ConditionCollection::ALL_CONDITION);
    }

    /**
     * Short hand for the startConditionCollection(ALL).
     *
     * @return ConditionCollectionBuilderInterface Builder for the new collection
     */
    public function startConditionCollectionAny()
    {
        return $this->startConditionCollection(ConditionCollection::ANY_CONDITION);
    }

    /**
     * Ends the construction of the current collection.
     *
     * @return ConditionCollectionContainableInterface The parent builder
     */
    public function end()
    {
        //Add the condition to the parent builder
        $this->parentBuilder->addConditionCollection($this->conditionCollection);

        //return the parent
        return $this->parentBuilder;
    }

    /**
     * Add or set the condtions collection of the object.
     *
     * @param ConditionCollection $conditionCollection The condition collection to add or set
     *
     * @return self
     */
    public function addConditionCollection(ConditionCollection $conditionCollection)
    {
        return $this->addCondition($conditionCollection);
    }

    /**
     * Returns a reference to the defintion manager.
     *
     * @return DefinitionManagerInterface The defintion manager
     */
    public function getDefinitionManager()
    {
        return $this->parentBuilder->getDefinitionManager();
    }

    /**
     * Get the context collection currently associated with the ruleset.
     *
     * @return ContextCollectionInterface The context collection
     */
    public function getContextCollection()
    {
        return $this->parentBuilder->getContextCollection();
    }
}
