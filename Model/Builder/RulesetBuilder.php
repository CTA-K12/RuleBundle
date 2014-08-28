<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Builder\RulesetBuilderInterface;

use Mesd\RuleBundle\Model\Ruleset\Ruleset;
use Mesd\RuleBundle\Model\Rule\RuleNodeInterface;
use Mesd\RuleBundle\Model\Builder\RuleBuilderInterface;
use Mesd\RuleBundle\Model\Builder\RuleBuilder;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;
use Mesd\RuleBundle\Model\Context\ContextCollectionInterface;

class RulesetBuilder implements RulesetBuilderInterface
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Errors
    const ERROR_RULE_NOT_FOUND = 'The requested rule is not registered with this builder';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The ruleset being built
     * @var Ruleset
     */
    private $ruleset;

    /**
     * Array detailing what rules follow which rules
     * @var array
     */
    private $ruleMapping;

    /**
     * Definition Manager
     * @var DefinitionManagerInterface
     */
    private $definitionManager;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param DefintionManagerInterface $definitonManager The definition manager
     * @param string                    $name             The name of the ruleset
     */
    public function __construct(DefinitionManagerInterface $definitionManager, $name) {
        //Set stuff
        $this->definitionManager = $definitionManager;

        //Get the ruleset definition from the dm
        $this->ruleset = $definitionManager->getRuleset($name);
    }


    /////////////////////////
    // IMPLEMENTED METHODS //
    /////////////////////////


    /**
     * Starts a new rule to add to the ruleset and returns the rule builder
     *
     * @param  string               $name The name of the new rule
     *
     * @return RuleBuilderInterface       The new builder for the new rule
     */
    public function startRule($name) {
        //Add the rule name to the mapping
        $this->ruleMapping[$name] = array('node' => null, 'then' => array(), 'else' => array(), 'root' => true);

        //Create the new rule builder and return it
        return new RuleBuilder($this, $name);
    }


    /**
     * Add a rule node to the rule builder
     *
     * @param  RuleNodeInterface $ruleNode The rule node to add to the ruleset
     *
     * @return self
     */
    public function addRuleNode(RuleNodeInterface $ruleNode) {
        //Add the rule node to the mapping
        $this->ruleMapping[$ruleNode->getName()]['node'] = $ruleNode;

        return $this;
    }


    /**
     * Registers that a given rule will follow another rule if that rule evals to true
     *
     * @param  string $parentName The name of the initial rule
     * @param  string $thenName   The name of the rule to goto if the parent rule evals to true
     *
     * @return self
     */
    public function addThenRule($parentName, $thenName) {
        //Check that the parent rule exists
        if (!array_key_exists($parentName, $this->ruleMapping)) {
            throw new \Exception(self::ERROR_RULE_NOT_FOUND);
        }

        //Add the mapping
        $this->ruleMapping[$parentName]['then'][] = $thenName;

        return $this;
    }


    /**
     * Registers that a given rule will follow another rule if that rule evals to false
     *
     * @param  string $parentName The name of the initial rule
     * @param  string $elseName   The name of the rule to goto if the parent rule evals to false
     *
     * @return self
     */
    public function addElseRule($parentName, $elseName) {
        //Check that the parent rule exists
        if (!array_key_exists($parentName, $this->ruleMapping)) {
            throw new \Exception(self::ERROR_RULE_NOT_FOUND);
        }

        //Add the mapping
        $this->ruleMapping[$parentName]['else'][] = $elseName;

        return $this;
    }


    /**
     * Builds and returns the updated ruleset
     *
     * @return RulesetInterface The updated ruleset
     */
    public function build() {
        //Go through each of the mapping entries and connect the nodes
        if (0 < count($this->ruleMapping)) {
            foreach($this->ruleMapping as $mapEntry) {
                //Connect the thens
                foreach($mapEntry['then'] as $thenName) {
                    //Check that the name exists
                    if (array_key_exists($thenName, $this->ruleMapping)) {
                        //Add the then node to the parent node
                        $mapEntry['node']->addThenRule($this->ruleMapping[$thenName]['node']);

                        //mark the then node as no longer being a root
                        $mapEntry[$thenName]['root'] = false;
                    } else {
                        throw new \Exception(self::ERROR_RULE_NOT_FOUND);
                    }
                }

                //Connect the elses
                foreach($mapEntry['else'] as $elseName) {
                    //Check that the name exists
                    if (array_key_exists($elseName, $this->ruleMapping)) {
                        //Add the then node to the parent node
                        $mapEntry['node']->addElseRule($this->ruleMapping[$elseName]['node']);

                        //mark the then node as no longer being a root
                        $mapEntry[$elseName]['root'] = false;
                    } else {
                        throw new \Exception(self::ERROR_RULE_NOT_FOUND);
                    }
                }
            }

            //Second pass: get the nodes that are roots and add them to the ruleset
            foreach($this->ruleMapping as $mapEntry) {
                if ($mapEntry['root']) {
                    $this->ruleset->addRootRuleNode($mapEntry['node']);
                }
            }
        }

        //Return the underlying ruleset
        return $this->ruleset;
    }


    /**
     * Returns a reference to the defintion manager
     * 
     * @return DefinitionManagerInterface The defintion manager
     */
    public function getDefinitionManager() {
        return $this->definitionManager;
    }


    /**
     * Get the context collection currently associated with the ruleset
     *
     * @return ContextCollectionInterface The context collection
     */
    public function getContextCollection() {
        return $this->ruleset->getContextCollection();
    }
}