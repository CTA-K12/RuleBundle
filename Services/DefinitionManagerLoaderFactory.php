<?php

namespace Mesd\RuleBundle\Services;

use Mesd\RuleBundle\Model\Definition\DefinitionManagerLoaderInterface;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerFileLoader;
use Mesd\RuleBundle\Model\Definition\DefinitionManagerDoctrineLoader;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DefinitionManagerLoaderFactory
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * Which definition loader to use
     * @var string
     */
    private $source;

    /**
     * The entity manager to load from if the source is database
     * @var string
     */
    private $emName;

    /**
     * Path to the file containing the definition manager config
     * @var string
     */
    private $definitionFile;

    /**
     * The symfony service container
     * @var ContainerInterface
     */
    private $container;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param ContainerInterface $container The container to pass along to the definition managers
     */
    public function __construct(ContainerInterface $container) {
        //Set stuff
        $this->container = $container;
    }


    /////////////
    // METHODS //
    /////////////


    /**
     * Returns the definition manager loader
     *
     * @return DefinitionManagerLoaderInterface The loaded for the given source string
     */
    public function getLoader() {
        if ('file' === $this->source) {
            return new DefinitionManagerFileLoader($this->container, $this->definitionFile);
        } else {
            return new DefinitionManagerDoctrineLoader($this->container, $this->emName);
        }
    }


    /**
     * Get the entity manager
     *
     * @return EntityManager Entity Manager
     */
    public function getEntityManager() {
        return $this->container->get('doctrine')->getManager($this->emName);
    }


    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Gets the Which definition loader to use.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the Which definition loader to use.
     *
     * @param string $source the source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Gets the The entity manager to load from if the source is database.
     *
     * @return string
     */
    public function getEmName()
    {
        return $this->emName;
    }

    /**
     * Sets the The entity manager to load from if the source is database.
     *
     * @param string $emName the em name
     *
     * @return self
     */
    public function setEmName($emName)
    {
        $this->emName = $emName;

        return $this;
    }

    /**
     * Gets the Path to the file containing the definition manager config.
     *
     * @return string
     */
    public function getDefinitionFile()
    {
        return $this->definitionFile;
    }

    /**
     * Sets the Path to the file containing the definition manager config.
     *
     * @param string $definitionFile the definition file
     *
     * @return self
     */
    public function setDefinitionFile($definitionFile)
    {
        $this->definitionFile = $definitionFile;

        return $this;
    }
}