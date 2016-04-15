<?php

namespace Mesd\RuleBundle\Services;

use Mesd\RuleBundle\Model\Builder\RulesetBuilderInterface;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerLoaderInterface;
use Mesd\RuleBundle\Model\Defintion\DefinitionManagerInterface;
use Mesd\RuleBundle\Model\Ruleset\RulesetInterface;

class RulesService
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The definition manager loader.
     *
     * @var DefinitionManagerLoaderInterface
     */
    private $dmLoader;

    /**
     * The loaded definition manager.
     *
     * @var DefintionManagerInterface
     */
    private $dm;

    /**
     * Whether the definition has been loaded or not.
     *
     * @var boolean
     */
    private $hasLoaded;

    /**
     * Entity Manager.
     *
     * @var EntityManager
     */
    private $em;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor.
     *
     * @param DefinitionManagerLoaderInterface $dmLoader The definiton manager loader
     */
    public function __construct(DefinitionManagerLoaderFactory $dmLoaderFactory)
    {
        //Get the loader
        $this->dmLoader = $dmLoaderFactory->getLoader();

        //Get the em
        $this->em = $dmLoaderFactory->getEntityManager();

        //Set the hasLoaded flag to false
        $this->hasLoaded = false;
    }

    /////////////
    // METHODS //
    /////////////


    /**
     * Get an instance of the storage manager.
     *
     * @return StorageManager An instance of the storage manager
     */
    public function getStorageManager()
    {
        return new StorageManager($this, $this->em);
    }

    /**
     * Load a ruleset from the database.
     *
     * @param string $rulesetName The name of the ruleset
     *
     * @return RulesetInterface The ruleset
     */
    public function load($rulesetName)
    {
        return $this->getStorageManager()->load($rulesetName);
    }

    /**
     * Get the ruleset object by name.
     *
     * @param string $name The name of the ruleset to get
     *
     * @return RulesetInterface The ruleset associated with the given name
     */
    public function getRulesetDefinition($name)
    {
        return $this->getDefinitionManager()->getRuleset($name);
    }

    /**
     * Gets the ruleset builder for the ruleset with the given name.
     *
     * @param string $name The name of the ruleset to get a builder for
     *
     * @return RulesetBuilderInterface The builder for the given ruleset
     */
    public function getRulesetBuilder($name)
    {
        return $this->getDefinitionManager()->getRulesetBuilder($name);
    }

    /**
     * Loads the defintion manager.
     *
     * @return DefinitionManagerInterface The loaded definition manager
     */
    public function loadDefinitionManager()
    {
        //Call the loader
        $this->dm = $this->dmLoader->load();

        //Set the hasLoaded flag
        $this->hasLoaded = true;

        //return the dm
        return $this->dm;
    }

    /**
     * Gets the definition Manager.
     *
     * @return DefinitionManagerInterface The definition manager
     */
    public function getDefinitionManager()
    {
        //return the dm if its loaded, else load it
        if ($this->hasLoaded) {
            return $this->dm;
        } else {
            return $this->loadDefinitionManager();
        }
    }

    /**
     * Gets a list of all the names of the rulesets in the definition manager.
     *
     * @return array List of ruleset names
     */
    public function getRulesetNames()
    {
        return $this->getDefinitionManager()->getRulesetNames();
    }
}
