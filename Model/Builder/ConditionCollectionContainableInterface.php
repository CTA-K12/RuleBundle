<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Condition\ConditionCollection;

interface ConditionCollectionContainableInterface extends DefinitionManagerAwareInterface
{
    /**
     * Add or set the condtions collection of the object.
     *
     * @param ConditionCollection $conditionCollection The condition collection to add or set
     */
    public function addConditionCollection(ConditionCollection $conditionCollection);

    /**
     * Starts a new condition collection to embed in this collection.
     *
     * @param int $chain A flag pertaining to whether this is any or all collection
     *
     * @return ConditionCollectionBuilderInterface Builder for the new collection
     */
    public function startConditionCollection($chain);

    /**
     * Short hand for the startConditionCollection(ANY).
     *
     * @return ConditionCollectionBuilderInterface Builder for the new collection
     */
    public function startConditionCollectionAll();

    /**
     * Short hand for the startConditionCollection(ALL).
     *
     * @return ConditionCollectionBuilderInterface Builder for the new collection
     */
    public function startConditionCollectionAny();
}
