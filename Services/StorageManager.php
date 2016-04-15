<?php

namespace Mesd\RuleBundle\Services;

use Doctrine\ORM\EntityManager;
use Mesd\RuleBundle\Entity\ActionCallEntity;
use Mesd\RuleBundle\Entity\ConditionCollectionEntity;
use Mesd\RuleBundle\Entity\ConditionEntity;
use Mesd\RuleBundle\Entity\RuleEntity;
use Mesd\RuleBundle\Entity\RulesetEntity;
use Mesd\RuleBundle\Model\Action\AbstractContextAction;
use Mesd\RuleBundle\Model\Action\ActionInterface;
use Mesd\RuleBundle\Model\Attribute\AbstractContextAttribute;
use Mesd\RuleBundle\Model\Builder\ConditionCollectionBuilderInterface;
use Mesd\RuleBundle\Model\Builder\ConditionCollectionContainableInterface;
use Mesd\RuleBundle\Model\Builder\RuleBuilderInterface;
use Mesd\RuleBundle\Model\Condition\ConditionCollection;
use Mesd\RuleBundle\Model\Condition\ConditionInterface;
use Mesd\RuleBundle\Model\Condition\StandardCondition;
use Mesd\RuleBundle\Model\Rule\RuleNodeInterface;
use Mesd\RuleBundle\Model\Ruleset\Ruleset;
use Mesd\RuleBundle\Model\Ruleset\RulesetInterface;

class StorageManager
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Errors
    const ERROR_RULESET_NOT_FOUND  = 'The requested ruleset was not found';
    const ERROR_RULESET_NOT_IN_DEF = 'The requested ruleset is not in the saved definitions';
    const ERROR_MISSING_ATTRIBUTE  = 'The attribute $name was not in the saved definitions';
    const ERROR_MISSING_ACTION     = 'The action $name was not in the saved defintions';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The rules service.
     *
     * @var RulesService
     */
    private $rulesService;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * A list of rule entities that the ruleset being written has.
     *
     * @var array
     */
    private $ruleEntityList;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param RulesService  $rulesService The rules service managing the rules
     * @param EntityManager $em           The entity manager in charge of the rules database items
     */
    public function __construct(RulesService $rulesService, EntityManager $em)
    {
        //Save the references
        $this->rulesService = $rulesService;
        $this->em           = $em;
    }

    /////////////
    // METHODS //
    /////////////


    /**
     * Load the ruleset from the database.
     *
     * @param string $rulesetName The name of the ruleset to load
     *
     * @return RulesetInterface The loaded ruleset object
     */
    public function load($rulesetName)
    {
        //Check that the name has a corresponding ruleset
        $rulesetEntity = $this->em->getRepository('MesdRuleBundle:RulesetEntity')->findOneByName($rulesetName);
        if (null === $rulesetEntity) {
            throw new \Exception(self::ERROR_RULESET_NOT_FOUND);
        }

        //Create a new builder
        $builder = $this->rulesService->getRulesetBuilder($rulesetName);

        //put info into builder
        foreach ($rulesetEntity->getRules() as $ruleEntity) {
            $ruleBuilder = $builder->startRule($ruleEntity->getName());
            if ($ruleEntity->getConditionCollection()) {
                $ruleBuilder = $this->buildConditions($ruleBuilder, $ruleEntity->getConditionCollection());
            }
            foreach ($ruleEntity->getThenActions() as $actionCallEntity) {
                $ruleBuilder = $this->buildAction($ruleBuilder, $actionCallEntity, true);
            }
            foreach ($ruleEntity->getElseActions() as $actionCallEntity) {
                $ruleBuilder = $this->buildAction($ruleBuilder, $actionCallEntity, false);
            }
            foreach ($ruleEntity->getThenRules() as $thenRule) {
                $ruleBuilder->addThenRule($thenRule->getName());
            }
            foreach ($ruleEntity->getElseRules() as $elseRule) {
                $ruleBuilder->addElseRule($elseRule->getName());
            }

            $builder = $ruleBuilder->end();
        }

        //return the ruleset
        return $builder->build();
    }

    /**
     * Save the given ruleset to the database.
     *
     * @param Ruleset $ruleset The ruleset object to write to the database
     *
     * @return boolean Whether the write was successful or not
     */
    public function save(Ruleset $ruleset)
    {
        //Get the ruleset entity that should be in place from the definitions
        $rulesetEntity = $this->em->getRepository('MesdRuleBundle:RulesetEntity')->findOneByName($ruleset->getName());
        if (null === $rulesetEntity) {
            throw new \Exception(self::ERROR_RULESET_NOT_IN_DEF);
        }

        //Convert the ruleset to entities
        $rulesetEntity = $this->convertToEntities($rulesetEntity, $ruleset);

        //flush
        $this->em->flush();

        //return return true
        return true;
    }

    /**
     * Validates an array of ruleset data to ensure that it follows standards.
     *
     * @param array $rulesetData The array data to check
     *
     * @return array An array of errors if any exist
     */
    public function validateArray(array $rulesetData)
    {
        $errors = [];

        //Check that rule names are unique
        if (array_key_exists('rules', $rulesetData)) {
            $ruleNames = [];
            foreach ($rulesetData['rules'] as $index => $ruleData) {
                //Check that the name does not exist in the rule name array
                if (in_array($ruleData['name'], $ruleNames)) {
                    $errors['rule-' . $index] = 'Rule name already in use';
                } else {
                    $ruleNames[] = $ruleData['name'];
                }
            }
        }

        //Return the errors
        return $errors;
    }

    /**
     * Builds a ruleset from array data.
     *
     * @param array $rulesetData The array of ruleset data
     *
     * @return RulesetInterface The ruleset object built from the data array
     */
    public function buildFromArray(array $rulesetData)
    {
        //Start the builder
        $builder = $this->rulesService->getRulesetBuilder($rulesetData['rulesetName']);

        //Start with the rules
        if (array_key_exists('rules', $rulesetData)) {
            foreach ($rulesetData['rules'] as $ruleData) {
                $ruleBuilder = $builder->startRule($ruleData['name']);

                //Go through the rule data and rebuild the array with the
                if (isset($ruleData['conditions'])) {
                    $conditionArray = $this->modifyInputConditionArray($ruleData['collections'], $ruleData['conditions']);
                } else {
                    $conditionArray = [];
                }

                //Handle each root collection
                foreach ($conditionArray as $index => $collectionData) {
                    $ruleBuilder = $this->handleCollectionArray($collectionData, $ruleBuilder);
                }

                //Handle the actions
                if (array_key_exists('actions', $ruleData)) {
                    foreach ($ruleData['actions'] as $index => $actionData) {
                        $ruleBuilder = $this->handleActionArray($actionData, $ruleBuilder);
                    }
                }

                //Handle the followup rules
                if (array_key_exists('followupRules', $ruleData)) {
                    foreach ($ruleData['followupRules'] as $index => $followupRuleData) {
                        $ruleBuilder = $this->handleFollowupRuleArray($followupRuleData, $ruleBuilder);
                    }
                }

                //Build the rule
                $builder = $ruleBuilder->end();
            }
        }

        //Return the final product
        return $builder->build();
    }

    /**
     * Transforms a ruleset object into array data.
     *
     * @param Ruleset $ruleset The ruleset to transform
     *
     * @return array The resulting array
     */
    public function transformToArray(Ruleset $ruleset)
    {
        $rulesetData = [];

        //Set the name
        $rulesetData['rulesetName'] = $ruleset->getName();

        //Set the default start values
        $rulesetData['ruleIndex'] = 0;
        $rulesetData['rules']     = [];

        //handle each root rule
        foreach ($ruleset->getRootRuleNodes() as $ruleNode) {
            $rulesetData = $this->transformRuleNodeToArray($ruleNode, $rulesetData);
        }

        //Return the completed array
        return $rulesetData;
    }

    /////////////////////
    // PRIVATE METHODS //
    /////////////////////


    /**
     * Converts a ruleset into entities to save in the database.
     *
     * @param RulesetEntity $rulesetEntity The root ruleset entity
     * @param Ruleset       $ruleset       The ruleset object
     *
     * @return RulesetEntity The root entity with the new entities attached
     */
    private function convertToEntities(RulesetEntity $rulesetEntity, Ruleset $ruleset)
    {
        //Create a list of rules under the ruleset entity as root rules
        $this->ruleEntityList = [];
        foreach ($rulesetEntity->getRules() as $ruleEntity) {
            $this->ruleEntityList[$ruleEntity->getName()]['entity'] = $ruleEntity;
            $this->ruleEntityList[$ruleEntity->getName()]['used']   = false;
        }

        //foreach root node in the ruleset, handle the nodes
        foreach ($ruleset->getRootRuleNodes() as $rootNode) {
            $this->handleRuleNode($rulesetEntity, $rootNode);
        }

        //Handle the unused rules
        foreach ($this->ruleEntityList as $ruleEntity) {
            if (!$ruleEntity['used']) {
                $this->deleteRule($ruleEntity['entity']);
            }
        }

        //Return the rulesetEntity
        return $rulesetEntity;
    }

    /**
     * Handle a rule node in regard to generating its entities.
     *
     * @param RulesetEntity     $rulesetEntity The root ruleset entity
     * @param RuleNodeInterface $node          The node to handle
     *
     * @return RuleEntity The rule entity
     */
    private function handleRuleNode(RulesetEntity $rulesetEntity, RuleNodeInterface $node)
    {
        $update = true;
        //Check if the rule has an existing entity
        if (array_key_exists($node->getName(), $this->ruleEntityList)) {
            //Check if existing entity needs updating (ignore if used is set to true, already been visited)
            if (!$this->ruleEntityList[$node->getName()]['used']) {
                //Delete the existing collection
                if ($this->ruleEntityList[$node->getName()]['entity']->getConditionCollection()) {
                    $this->deleteConditionCollection($this->ruleEntityList[$node->getName()]['entity']->getConditionCollection());
                }

                //Create new
                $newConditionCollection = $this->createConditionCollection($node->getRule()->getConditions());
                $newConditionCollection->setRule($this->ruleEntityList[$node->getName()]['entity']);
                $this->ruleEntityList[$node->getName()]['entity']->addConditionCollection($newConditionCollection);
                $this->em->persist($newConditionCollection);

                //mark node as visited
                $this->ruleEntityList[$node->getName()]['used'] = true;
                $ruleEntity                                     = $this->ruleEntityList[$node->getName()]['entity'];
            } else {
                $ruleEntity = $this->ruleEntityList[$node->getName()]['entity'];
                $update     = false;
            }
        } else {
            //Create new
            $ruleEntity = new RuleEntity();
            $ruleEntity->setName($node->getName());
            $ruleEntity->setRuleset($rulesetEntity);
            $newConditionCollection = $this->createConditionCollection($node->getRule()->getConditions());
            $ruleEntity->addConditionCollection($newConditionCollection);
            $newConditionCollection->setRule($ruleEntity);
            $this->em->persist($newConditionCollection);

            //Add to the entity list
            $this->ruleEntityList[$node->getName()] = ['entity' => $ruleEntity, 'used' => true];
        }

        if ($update) {
            //Handle the actions
            //Then Actions
            //Generate a list of existing actions
            $existing = [];
            foreach ($ruleEntity->getThenActions() as $actionCallEntity) {
                if (null !== $actionCallEntity->getAction()->getContext()) {
                    $name =
                        $actionCallEntity->getAction()->getContext()->getName()
                        . $actionCallEntity->getAction()->getName()
                        . $actionCallEntity->getRawInputValue();
                } else {
                    $name =
                        $actionCallEntity->getAction()->getService()->getName()
                        . $actionCallEntity->getAction()->getName()
                        . $actionCallEntity->getRawInputValue();
                }
                $existing[$name]['used']   = false;
                $existing[$name]['entity'] = $actionCallEntity;
            }

            //Find the new actions and mark the actions that remain
            foreach ($node->getRule()->getThenActions() as $action) {
                if (array_key_exists($action->getParentName() . $action->getName() . $action->getInputValue(), $existing)) {
                    //Mark as seen
                    $existing[$action->getParentName() . $action->getName() . $action->getInputValue()]['used'] = true;
                } else {
                    //Create new
                    $newAction = $this->createActionCall($action);
                    $ruleEntity->addThenAction($newAction);
                    $newAction->setThenRule($ruleEntity);
                    $this->em->persist($newAction);
                }
            }

            //Remove the action calls that are no longer prevalent
            foreach ($existing as $ex) {
                if (!$ex['used']) {
                    $ruleEntity->removeThenAction($ex['entity']);
                    $this->em->remove($ex['entity']);
                }
            }

            //Else Actions
            //Generate a list of existing actions
            $existing = [];
            foreach ($ruleEntity->getElseActions() as $actionCallEntity) {
                if (null !== $actionCallEntity->getAction()->getContext()) {
                    $name =
                        $actionCallEntity->getAction()->getContext()->getName()
                        . $actionCallEntity->getAction()->getName()
                        . $actionCallEntity->getRawInputValue();
                } else {
                    $name =
                        $actionCallEntity->getAction()->getService()->getName()
                        . $actionCallEntity->getAction()->getName()
                        . $actionCallEntity->getRawInputValue();
                }
                $existing[$name]['used']   = false;
                $existing[$name]['entity'] = $actionCallEntity;
            }

            //Find the new actions and mark the actions that remain
            foreach ($node->getRule()->getElseActions() as $action) {
                if (array_key_exists($action->getParentName() . $action->getName() . $action->getInputValue(), $existing)) {
                    //Mark as seen
                    $existing[$action->getParentName() . $action->getName() . $action->getInputValue()]['used'] = true;
                } else {
                    //Create new
                    $newAction = $this->createActionCall($action);
                    $ruleEntity->addElseAction($newAction);
                    $newAction->setElseRule($ruleEntity);
                    $this->em->persist($newAction);
                }
            }

            //Remove the action calls that are no longer prevalent
            foreach ($existing as $ex) {
                if (!$ex['used']) {
                    $ruleEntity->removeElseAction($ex['entity']);
                    $this->em->remove($ex['entity']);
                }
            }

            //Handle the follow up rules
            //Then
            foreach ($ruleEntity->getThenRules() as $thenRule) {
                $ruleEntity->removeThenRule($thenRule);
            }
            foreach ($node->getThenRules() as $thenNode) {
                $ruleEntity->addThenRule($this->handleRuleNode($rulesetEntity, $thenNode));
            }

            //Else
            foreach ($ruleEntity->getElseRules() as $elseRule) {
                $ruleEntity->removeElseRule($elseRule);
            }
            foreach ($node->getElseRules() as $elseNode) {
                $ruleEntity->addElseRule($this->handleRuleNode($rulesetEntity, $elseNode));
            }

            //Persist changes
            $this->em->persist($ruleEntity);
        }

        //Return
        return $ruleEntity;
    }

    /**
     * Delete the rule.
     *
     * @param RuleEntity $ruleEntity The rule entity to delete
     */
    private function deleteRule(RuleEntity $ruleEntity)
    {
        //Delete the conditions
        $this->deleteConditionCollection($ruleEntity->getConditionCollection());

        //Delete the action calls
        foreach ($ruleEntity->getThenActions() as $actionCallEntity) {
            $this->em->remove($actionCallEntity);
        }
        foreach ($ruleEntity->getElseActions() as $actionCallEntity) {
            $this->em->remove($actionCallEntity);
        }

        //Delete the rule
        $this->em->remove($ruleEntity);
    }

    /**
     * Create a new action call entity.
     *
     * @param ActionInterface $action The action object
     *
     * @return ActionCallEntity The new entity
     */
    private function createActionCall(ActionInterface $action)
    {
        $entity = new ActionCallEntity();
        $entity->setRawInputValue($action->getInputValue());

        //Get the action
        if ($action instanceof AbstractContextAction) {
            $actionEntity = $this->em->getRepository('MesdRuleBundle:ActionEntity')
                ->getContextAction($action->getParentName(), $action->getName());
        } else {
            $actionEntity = $this->em->getRepository('MesdRuleBundle:ActionEntity')
                ->getServiceAction($action->getParentName(), $action->getName());
        }

        //Check that the action exists
        if (null === $actionEntity) {
            throw new \Exception(str_replace('$name', $action->getName(), self::ERROR_MISSING_ACTION));
        }

        //Set the action and return
        $entity->setAction($actionEntity);

        return $entity;
    }

    /**
     * Creates a new condition collection entity from a condition collection object.
     *
     * @param ConditionCollection $collection The condition collection object to create entities from
     *
     * @return ConditionCollectionEntity The new condition collection entity
     */
    private function createConditionCollection(ConditionCollection $collection)
    {
        //Create the collection entity
        $collectionEntity = new ConditionCollectionEntity();
        if ($collection->isAny()) {
            $collectionEntity->setChainType('any');
        } else {
            $collectionEntity->setChainType('all');
        }

        //Add the children
        foreach ($collection->getConditions() as $child) {
            if ($child->isCollection()) {
                $newCollection = $this->createConditionCollection($child);
                $collectionEntity->addSubCollection($newCollection);
                $newCollection->setParent($collectionEntity);
                $this->em->persist($newCollection);
            } else {
                $newCondition = $this->createCondition($child);
                $collectionEntity->addCondition($newCondition);
                $newCondition->setCollection($collectionEntity);
                $this->em->persist($newCondition);
            }
        }

        //Return the new entity
        return $collectionEntity;
    }

    /**
     * Delete a condition collection and its children from the database.
     *
     * @param ConditionCollectionEntity $collectionEntity The collection to delete
     */
    private function deleteConditionCollection(ConditionCollectionEntity $collectionEntity)
    {
        //Delete subcollections
        foreach ($collectionEntity->getSubCollections() as $child) {
            $collectionEntity->removeSubCollection($child);
            $this->deleteConditionCollection($child);
        }

        //Delete conditions
        foreach ($collectionEntity->getConditions() as $child) {
            $collectionEntity->removeCondition($child);
            $this->deleteCondition($child);
        }

        //Delete this
        $this->em->remove($collectionEntity);
    }

    /**
     * Creates a new condition entity from a standard condition object.
     *
     * @param StandardCondition $condition The condition object to create the entity from
     *
     * @return ConditionEntity The condition entity
     */
    private function createCondition(StandardCondition $condition)
    {
        //Create the new condition entity
        $conditionEntity = new ConditionEntity();

        //Get the attribute entity reference
        $attributeEntity = $this->em->getRepository('MesdRuleBundle:AttributeEntity')
            ->findOneByName($condition->getAttribute()->getName());
        if (null === $attributeEntity) {
            throw new \Exception(str_replace('$name', $condition->getAttribute()->getName(), self::ERROR_MISSING_ATTRIBUTE));
        }

        //Set the condition entity values
        $conditionEntity->setAttribute($attributeEntity);
        $conditionEntity->setRawInputValue($condition->getInputValue());
        $conditionEntity->setOperatorValue($condition->getOperatorValue());

        //Return
        return $conditionEntity;
    }

    /**
     * Delete a condition.
     *
     * @param ConditionEntity $conditionEntity The condition to delete
     */
    private function deleteCondition(ConditionEntity $conditionEntity)
    {
        $this->em->remove($conditionEntity);
    }

    /**
     * Adds a new action to the rule builder from the action call entity.
     *
     * @param RuleBuilderInterface $ruleBuilder      The builder for the rule to add the action to
     * @param ActionCallEntity     $actionCallEntity The call entity
     * @param boolean              $then             If true, add as then rule, else add else rule
     *
     * @return RuleBuilderInterface The rule builder
     */
    private function buildAction(RuleBuilderInterface $ruleBuilder, ActionCallEntity $actionCallEntity, $then)
    {
        //Create the action builder
        if ($then) {
            $actionBuilder = $ruleBuilder->startThenAction();
        } else {
            $actionBuilder = $ruleBuilder->startElseAction();
        }

        //Add the action name and input value
        if (null !== $actionCallEntity->getAction()->getContext()) {
            $actionBuilder->contextAction($actionCallEntity->getAction()->getContext()->getName(), $actionCallEntity->getAction()->getName());
        } else {
            $actionBuilder->serviceAction($actionCallEntity->getAction()->getName());
        }
        $actionBuilder->setInputValue($actionCallEntity->getRawInputValue());

        //Return
        return $actionBuilder->end();
    }

    /**
     * Builds the conditions when loading from the database.
     *
     * @param ConditionCollectionContainableInterface $parentBuilder    The parent builder
     * @param ConditionCollectionEntity               $collectionEntity The condition collection entity
     *
     * @return ConditionCollectionContainableInterface The parent builder
     */
    private function buildConditions(
        ConditionCollectionContainableInterface $parentBuilder,
        ConditionCollectionEntity $collectionEntity
    ) {
        //Check what type of conditions to start
        if ('any' === $collectionEntity->getChainType()) {
            $collectionBuilder = $parentBuilder->startConditionCollectionAny();
        } else {
            $collectionBuilder = $parentBuilder->startConditionCollectionAll();
        }

        //Build the sub conditions
        foreach ($collectionEntity->getSubCollections() as $subCollection) {
            $collectionBuilder = $this->buildConditions($collectionBuilder, $subCollection);
        }

        //Build the conditions
        foreach ($collectionEntity->getConditions() as $condition) {
            $conditionBuilder = $collectionBuilder->startCondition();
            if (null !== $condition->getAttribute()->getContext()) {
                $conditionBuilder = $conditionBuilder->setContextAttribute(
                    $condition->getAttribute()->getContext()->getName(),
                    $condition->getAttribute()->getName()
                );
            } else {
                $conditionBuilder = $conditionBuilder->setServiceAttribute(
                    $condition->getAttribute()->getName()
                );
            }
            $conditionBuilder = $conditionBuilder
                ->setOperatorValue($condition->getOperatorValue())
                ->setInputValue($condition->getRawInputValue());
            $collectionBuilder = $conditionBuilder->end();
        }

        //return the parent builder
        return $collectionBuilder->end();
    }

    /**
     * Takes the input array of collections and conditions and converts them to a more tree like structure.
     *
     * @param array $collectionsData The array of collections from the rule data
     * @param array $conditionsData  The array of conditions from the rule data
     *
     * @return array The new array
     */
    private function modifyInputConditionArray($collectionsData, $conditionsData)
    {
        //Add a condition array to each collection array
        foreach ($collectionsData as $index => $collectionData) {
            $collectionsData[$index]['conditions']  = [];
            $collectionsData[$index]['collections'] = [];
        }

        //Add each condition to its parent collection
        foreach ($conditionsData as $index => $conditionData) {
            if (array_key_exists($conditionData['parentCollection'], $collectionsData)) {
                $collectionsData[$conditionData['parentCollection']]['conditions'][] = $conditionData;
            }
        }

        //Arrange the collections
        $children = [];
        foreach ($collectionsData as $index => $collectionData) {
            if (array_key_exists($collectionData['parentIndex'], $collectionsData)) {
                $collectionsData[$collectionData['parentIndex']]['collections'][] = $collectionData;
                $children[]                                                       = $index;
            }
        }

        //Remove the non-root collections from the array
        foreach ($children as $child) {
            unset($collectionsData[$child]);
        }

        //Return the modified array
        return $collectionsData;
    }

    /**
     * Convert an array of collection data into an object via the ruleset builders.
     *
     * @param array                                   $collectionData The array of collection data
     * @param ConditionCollectionContainableInterface $parentBuilder  The parent builder to add the collection to
     *
     * @return ConditionCollectionContainableInterface The parent builder
     */
    private function handleCollectionArray($collectionData, ConditionCollectionContainableInterface $parentBuilder)
    {
        //Create a new collection builder
        if ('any' === $collectionData['chainType']) {
            $collectionBuilder = $parentBuilder->startConditionCollectionAny();
        } else {
            $collectionBuilder = $parentBuilder->startConditionCollectionAll();
        }

        //Add the conditions
        foreach ($collectionData['conditions'] as $conditionData) {
            $collectionBuilder = $this->handleConditionArray($conditionData, $collectionBuilder);
        }

        //Add children collections
        foreach ($collectionData['collections'] as $childCollectionData) {
            $collectionBuilder = $this->handleCollectionArray($childCollectionData, $collectionBuilder);
        }

        //Return the modified builder
        return $collectionBuilder->end();
    }

    /**
     * Convert an array of condition data into an object via the ruleset builders.
     *
     * @param array                               $conditionData The array of condition data
     * @param ConditionCollectionBuilderInterface $parentBuilder The parent builder
     *
     * @return ConditionCollectionBuilderInterface The modified parent builder
     */
    private function handleConditionArray($conditionData, ConditionCollectionBuilderInterface $parentBuilder)
    {
        //Create the condition builder
        $conditionBuilder = $parentBuilder->startCondition();

        //Determine the attribute type and set it
        if (0 === strlen(trim($conditionData['attribute']['context']))) {
            $conditionBuilder->setServiceAttribute($conditionData['attribute']['name']);
        } else {
            $conditionBuilder->setContextAttribute($conditionData['attribute']['context'], $conditionData['attribute']['name']);
        }

        //Set the operator and input values
        $conditionBuilder
            ->setOperatorValue($conditionData['operatorValue'])
            ->setInputValue($conditionData['inputValue']);

        //Return the modified parent builder
        return $conditionBuilder->end();
    }

    /**
     * Convert an array of action data into an object via the ruleset builders.
     *
     * @param array                $actionData    The array of action data
     * @param RuleBuilderInterface $parentBuilder The parent builder
     *
     * @return RuleBuilderInterface The modified builder
     */
    private function handleActionArray($actionData, RuleBuilderInterface $parentBuilder)
    {
        //Create the action builder
        if ('then' === $actionData['type']) {
            $actionBuilder = $parentBuilder->startThenAction();
        } else {
            $actionBuilder = $parentBuilder->startElseAction();
        }

        //Determine the type of the action and set it
        if (0 === strlen(trim($actionData['action']['context']))) {
            $actionBuilder->serviceAction($actionData['action']['name']);
        } else {
            $actionBuilder->contextAction($actionData['action']['context'], $actionData['action']['name']);
        }

        //Set the input
        $actionBuilder->setInputValue($actionData['inputValue']);

        //Return the modified parent
        return $actionBuilder->end();
    }

    /**
     * Covnert an array of followup rule data into an object via the rule builder.
     *
     * @param array                $followupRuleData The followup rule data
     * @param RuleBuilderInterface $ruleBuilder      The rule builder
     *
     * @return RuleBuilderInterface The modified rule builder
     */
    private function handleFollowupRuleArray($followupRuleData, RuleBuilderInterface $ruleBuilder)
    {
        //Determine the type of the followup data (e.g. then or else)
        if ('then' === $followupRuleData['type']) {
            $ruleBuilder->addThenRule($followupRuleData['name']);
        } else {
            $ruleBuilder->addElseRule($followupRuleData['name']);
        }

        //Return the modified parent builder
        return $ruleBuilder;
    }

    /**
     * Transform a rule node object to an array.
     *
     * @param RuleNode $ruleNode    The node to transform
     * @param array    $rulesetData The ruleset array
     *
     * @return array The modified ruleset array
     */
    private function transformRuleNodeToArray(RuleNodeInterface $ruleNode, $rulesetData)
    {
        //Increment the index by 1
        $index = $rulesetData['ruleIndex'];
        $rulesetData['ruleIndex']++;

        //Put the rule into the array
        $rule     = $ruleNode->getRule();
        $ruleData = [
            'name'               => $rule->getName(),
            'collectionIndex'    => 0,
            'conditionIndex'     => 0,
            'actionIndex'        => 0,
            'followupRulesIndex' => 0,
            'collections'        => [],
            'conditions'         => [],
            'actions'            => [],
            'followupRules'      => [],
        ];

        //Handle the conditions
        $ruleData = $this->transformConditionCollectionToArray($rule->getConditions(), $ruleData);

        //Handle the actions
        foreach ($rule->getThenActions() as $action) {
            $ruleData = $this->transformActionToArray($action, $ruleData, 'then');
        }
        foreach ($rule->getElseActions() as $action) {
            $ruleData = $this->transformActionToArray($action, $ruleData, 'else');
        }

        //Handle followup rules
        foreach ($ruleNode->getThenRules() as $thenRule) {
            if (!$this->ruleInArray($rulesetData, $thenRule->getName())) {
                $rulesetData = $this->transformRuleNodeToArray($thenRule, $rulesetData);
            }
            $ruleData = $this->transformFollowupRuleToArray($thenRule, $ruleData, 'then');
        }
        foreach ($ruleNode->getElseRules() as $elseRule) {
            if (!$this->ruleInArray($rulesetData, $elseRule->getName())) {
                $rulesetData = $this->transformRuleNodeToArray($elseRule, $rulesetData);
            }
            $ruleData = $this->transformFollowupRuleToArray($elseRule, $ruleData, 'else');
        }

        //put the ruledata into the ruleset data
        $rulesetData['rules'][$index] = $ruleData;

        //Return the modified ruleset data array
        return $rulesetData;
    }

    /**
     * Transform a condition collection object to an array.
     *
     * @param ConditionInterface $collection The collection object to transform
     * @param array              $ruleData   The rule array
     * @param int                $parent     The parent collection (or -1 if at top)
     *
     * @return array The modified rule array
     */
    private function transformConditionCollectionToArray(ConditionInterface $collection, $ruleData, $parent = -1)
    {
        //Add the collection to the array
        $index = $ruleData['collectionIndex'];
        $ruleData['collectionIndex']++;

        $ruleData['collections'][$index]['parentIndex'] = $parent;
        if ($collection->isAny()) {
            $ruleData['collections'][$index]['chainType'] = 'any';
        } else {
            $ruleData['collections'][$index]['chainType'] = 'all';
        }

        //Handle the children
        $conditions       = [];
        $childCollections = [];
        foreach ($collection->getConditions() as $child) {
            //Check if is collection
            if ($child->isCollection()) {
                $childCollections[] = $child;
            } else {
                $conditions[] = $child;
            }
        }

        //handle the conditions
        foreach ($conditions as $condition) {
            $ruleData = $this->transformConditionToArray($condition, $ruleData, $index);
        }

        //handle the collections
        foreach ($childCollections as $child) {
            $ruleData = $this->transformConditionCollectionToArray($child, $ruleData, $index);
        }

        //return the modified rule data array
        return $ruleData;
    }

    /**
     * Transform a condition object to an array.
     *
     * @param ConditionInterface $condition       The condition object to transform
     * @param array              $ruleData        The rule data array
     * @param int                $collectionIndex The index of the parent collection
     *
     * @return array The modified rule data array
     */
    private function transformConditionToArray(ConditionInterface $condition, $ruleData, $collectionIndex)
    {
        //add the condition to the array
        $index = $ruleData['conditionIndex'];
        $ruleData['conditionIndex']++;

        //Create the attribute array
        $attribute = [
            'name' => $condition->getAttribute()->getName(),
        ];

        if ($condition->getAttribute() instanceof AbstractContextAttribute) {
            $attribute['context'] = $condition->getAttribute()->getParentName();
        } else {
            $attribute['context'] = '';
        }

        //Create the condition array in the ruledata array
        $ruleData['conditions'][$index] = [
            'attribute'        => $attribute,
            'operatorValue'    => $condition->getOperatorValue(),
            'inputValue'       => $condition->getInputValue(),
            'parentCollection' => $collectionIndex,
        ];

        //return the modified rule data array
        return $ruleData;
    }

    /**
     * Transfrom a action object to an array.
     *
     * @param ActionInterface $action   The action object to transform
     * @param array           $ruleData the rule data
     * @param string          $type     the type of action it is (then or else)
     *
     * @return array The modified rule data array
     */
    private function transformActionToArray(ActionInterface $action, $ruleData, $type)
    {
        //Add the action to the array
        $index = $ruleData['actionIndex'];
        $ruleData['actionIndex']++;

        //Create the action array
        $actionArray = [
            'name' => $action->getName(),
        ];

        if ($action instanceof AbstractContextAction) {
            $actionArray['context'] = $action->getParentName();
        } else {
            $actionArray['context'] = '';
        }

        //create the action array in the ruledata array
        $ruleData['actions'][$index] = [
            'type'       => $type,
            'action'     => $actionArray,
            'inputValue' => $action->getInputValue(),
        ];

        //return the modified array
        return $ruleData;
    }

    /**
     * Transform a follwup rule to an array.
     *
     * @param RuleNodeInterface $ruleNode The rule node of the followup rule
     * @param array             $ruleData The rule data array
     * @param string            $type     The type of followup rule (then or else)
     *
     * @return array The modified
     */
    private function transformFollowupRuleToArray(RuleNodeInterface $ruleNode, $ruleData, $type)
    {
        //Get the current index value and increment it
        $index = $ruleData['followupRulesIndex'];
        $ruleData['followupRulesIndex']++;

        //Add to the ruledata array
        $ruleData['followupRules'][$index] = [
            'type' => $type,
            'name' => $ruleNode->getName(),
        ];

        //return the modified ruledata array
        return $ruleData;
    }

    /**
     * Whether a rule is already in the array of output.
     *
     * @param array  $rulesetData The ruleset data array
     * @param string $ruleName    The name of the rule
     *
     * @return boolean Whether it is in the array or not
     */
    private function ruleInArray($rulesetData, $ruleName)
    {
        $return = false;
        if (isset($rulesetData['rules'])) {
            foreach ($rulesetData['rules'] as $ruleData) {
                if (isset($ruleData['name'])) {
                    if ($ruleData['name'] == $ruleName) {
                        $return = true;
                        break;
                    }
                }
            }
        }

        return $return;
    }
}
