<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Context\ContextCollectionInterface;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;
use Mesd\RuleBundle\Model\Ruleset\Ruleset;
use Mesd\RuleBundle\Model\Rule\RuleNodeInterface;

class RulesetBuilder implements RulesetBuilderInterface
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Errors
    const ERROR_THEN_RULE_NOT_FOUND = 'The requested THEN rule is not registered with this builder';
    const ERROR_ELSE_RULE_NOT_FOUND = 'The requested ELSE rule is not registered with this builder';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The ruleset being built.
     *
     * @var Ruleset
     */
    private $ruleset;

    /**
     * Array detailing what rules follow which rules.
     *
     * @var array
     */
    private $ruleMapping;

    /**
     * Definition Manager.
     *
     * @var DefinitionManagerInterface
     */
    private $definitionManager;

    //////////////////
    // BASE METHODS //
    //////////////////

    /**
     * Constructor.
     *
     * @param DefintionManagerInterface $definitonManager The definition manager
     * @param string                    $name             The name of the ruleset
     */
    public function __construct(
        DefinitionManagerInterface $definitionManager,
                                   $name
    ) {
        //Set stuff
        $this->definitionManager = $definitionManager;

        //Get the ruleset definition from the dm
        $this->ruleset = $definitionManager->getRuleset($name);
    }

    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////

    /**
     * Starts a new rule to add to the ruleset and returns the rule builder.
     *
     * @param string $name The name of the new rule
     *
     * @return RuleBuilderInterface The new builder for the new rule
     */
    public function startRule($name)
    {
        //Add the rule name to the mapping
        $this->ruleMapping[$name] = ['node' => null, 'then' => [], 'else' => [], 'root' => true];

        //Create the new rule builder and return it
        return new RuleBuilder($this, $name);
    }

    /**
     * Add a rule node to the rule builder.
     *
     * @param RuleNodeInterface $ruleNode The rule node to add to the ruleset
     *
     * @return self
     */
    public function addRuleNode(RuleNodeInterface $ruleNode)
    {
        //Add the rule node to the mapping
        $this->ruleMapping[$ruleNode->getName()]['node'] = $ruleNode;

        return $this;
    }

    /**
     * Registers that a given rule will follow another rule if that rule evals to true.
     *
     * @param string $parentName The name of the initial rule
     * @param string $thenName   The name of the rule to goto if the parent rule evals to true
     *
     * @return self
     */
    public function addThenRule(
        $parentName,
        $thenName
    ) {
        //Check that the parent rule exists
        if (!array_key_exists($parentName, $this->ruleMapping)) {
            throw new \Exception(self::ERROR_THEN_RULE_NOT_FOUND . " : " . $thenName);
        }

        //Add the mapping
        $this->ruleMapping[$parentName]['then'][] = $thenName;

        return $this;
    }

    /**
     * Registers that a given rule will follow another rule if that rule evals to false.
     *
     * @param string $parentName The name of the initial rule
     * @param string $elseName   The name of the rule to goto if the parent rule evals to false
     *
     * @return self
     */
    public function addElseRule(
        $parentName,
        $elseName
    ) {
        //Check that the parent rule exists
        if (!array_key_exists($parentName, $this->ruleMapping)) {
            throw new \Exception(self::ERROR_ELSE_RULE_NOT_FOUND . " : " . $elseName);
        }

        //Add the mapping
        $this->ruleMapping[$parentName]['else'][] = $elseName;

        return $this;
    }

    /**
     * Builds and returns the updated ruleset.
     *
     * @return RulesetInterface The updated ruleset
     */
    public function build()
    {
        //Go through each of the mapping entries and connect the nodes
        if (0 < count($this->ruleMapping)) {
            foreach ($this->ruleMapping as $mapEntry) {
                //Connect the thens
                foreach ($mapEntry['then'] as $thenName) {
                    //Check that the name exists
                    if (array_key_exists($thenName, $this->ruleMapping)) {
                        //Add the then node to the parent node
                        $mapEntry['node']->addThenRule($this->ruleMapping[$thenName]['node']);

                        //mark the then node as no longer being a root
                        $this->ruleMapping[$thenName]['root'] = false;
                    } else {
                        throw new \Exception(self::ERROR_THEN_RULE_NOT_FOUND . " : " . $thenName);
                    }
                }

                //Connect the elses
                foreach ($mapEntry['else'] as $elseName) {
                    //Check that the name exists
                    if (array_key_exists($elseName, $this->ruleMapping)) {
                        //Add the then node to the parent node
                        $mapEntry['node']->addElseRule($this->ruleMapping[$elseName]['node']);

                        //mark the then node as no longer being a root
                        $this->ruleMapping[$elseName]['root'] = false;
                    } else {
                        throw new \Exception(self::ERROR_ELSE_RULE_NOT_FOUND . " : " . $elseName);
                    }
                }
            }

            //Second pass: get the nodes that are roots and add them to the ruleset
            foreach ($this->ruleMapping as $mapEntry) {
                if ($mapEntry['root']) {
                    $this->ruleset->addRootRuleNode($mapEntry['node']);
                }
            }
        }

        foreach ($this->ruleMapping as $name => $mapping) {
            $list[$mapping['node']->getName()]['then'] = [];
            foreach ($mapping['node']->getThenRules() as $then) {
                $list[$mapping['node']->getName()][] = $then->getName();
            }
            foreach ($mapping['node']->getElseRules() as $else) {
                $list[$mapping['node']->getName()][] = $else->getName();
            }
        }

        $this->ruleset->setAdjacencyList($list);

        foreach ($this->ruleMapping as $name => $mapping) {
            $then = $this->getThenLabel($mapping['node']);
            $else = $this->getElseLabel($mapping['node']);

            $relList[$mapping['node']->getName()][$then] = [];
            foreach ($mapping['node']->getThenRules() as $thenRule) {
                $relList[$mapping['node']->getName()][$then][] = $thenRule->getName();
            }

            $relList[$mapping['node']->getName()][$else] = [];
            foreach ($mapping['node']->getElseRules() as $elseRule) {
                $relList[$mapping['node']->getName()][$else][] = $elseRule->getName();
            }
        }

        $this->ruleset->setRelatedList($relList);

        //Return the underlying ruleset
        return $this->ruleset;
    }

    public function getThenLabel($node)
    {
        return $this->getNodeLabel($node, 'then');
    }

    public function getElseLabel($node)
    {
        return $this->getNodeLabel($node, 'else');
    }

    public function getNodeLabel(
        $node,
        $type
    ) {
        $label = "";

        $getActions = 'get' . ucfirst($type) . 'Actions';
        while (!$node->getRule()->$getActions()->isEmpty()) {
            $action = $node->getRule()->$getActions()->deQueue();
            $label .= ($action->getDescription() ?: get_class($action)) . ';';
        }

        return ($label ?: $type);
    }
/**
 * Returns a reference to the defintion manager.
 *
 * @return DefinitionManagerInterface The defintion manager
 */
    public function getDefinitionManager()
    {
        return $this->definitionManager;
    }

/**
 * Get the context collection currently associated with the ruleset.
 *
 * @return ContextCollectionInterface The context collection
 */
    public function getContextCollection()
    {
        return $this->ruleset->getContextCollection();
    }
}
