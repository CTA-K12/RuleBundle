<?php

namespace Mesd\RuleBundle\Model\Definition;

use Mesd\RuleBundle\Model\Definition\DefinitionManagerLoaderInterface;

use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;
use Mesd\RuleBundle\Model\Definition\DefinitionManager;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Yaml\Parser;

class DefinitionManagerFileLoader implements DefinitionManagerLoaderInterface
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Errors
    const ERROR_CONFIG_FILE_NOT_SET = 'The path to the configuration file for the ruleset definitions was not set before atempting to load';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The definition manager that this class is working on building
     * @var DefinitionManagerInterface
     */
    private $dm;

    /**
     * The path to the definition manager configuration file
     * @var string
     */
    private $configFile;

    /**
     * The root directory
     * @var string
     */
    private $rootDir;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param DefinitionManagerInterface $dm         Reference to the definition manager service
     * @param string                     $configFile The config file to load from
     */
    public function __construct(ContainerInterface $container, $configFile) {
        //Init the definition manager
        $this->dm = new DefinitionManager($container);

        //Set the config file
        $this->configFile = $configFile;

        //Get the root directory of the project
        $this->rootDir = $container->get('kernel')->getRootDir();
    }


    /////////////
    // METHODS //
    /////////////


    /**
     * Loads the information from the config file into the definition manager
     *
     * @return DefinitionManagerInterface The reference to the loaded definition manager
     */
    public function load() {
        if (null === $this->configFile) {
            throw new \Exception(self::ERROR_CONFIG_FILE_NOT_SET);
        }

        //Load
        $yaml = new Parser();
        try {
            $config = $yaml->parse(file_get_contents($this->rootDir . $this->configFile));
        } catch(\Exception $e) {
            throw $e;
        }

        //Register the inputs
        if (array_key_exists('inputs', $config)) {
            foreach($config['inputs'] as $name => $definition) {
                if (array_key_exists('params', $definition)) {
                    $params = $definition['params'];
                } else {
                    $params = array();
                }

                $this->dm->registerInput($name, $definition['class'], $params);
            }
        }

        //Register the contexts
        if (array_key_exists('contexts', $config)) {
            foreach($config['contexts'] as $name => $definition) {
                $this->dm->registerContext($name, $definition['classification'], $definition['type']);
                if (array_key_exists('attributes', $definition)) {
                    foreach($definition['attributes'] as $attrName => $attrDefinition) {
                        $this->dm->registerContextAttribute($attrName, $name, $attrDefinition['class'], $attrDefinition['input']);
                    }
                }
                if (array_key_exists('actions', $definition)) {
                    foreach($definition['actions'] as $actionName => $actionDefinition) {
                        $this->dm->registerContextAction($actionName, $name, $actionDefinition['class'], $actionDefinition['input']);
                    }
                }
            }
        }

        //Register the services
        if (array_key_exists('services', $config)) {
            //attributes
            if (array_key_exists('attributes', $config['services'])) {
                foreach($config['services']['attributes'] as $name => $attrDefinition) {
                    $this->dm->registerServiceAttribute($name, $attrDefinition['service'], $attrDefinition['class'], $attrDefinition['input']);
                }
            }
            //actions
            if (array_key_exists('actions', $config['services'])) {
                foreach($config['services']['actions'] as $name => $actionDefinition) {
                    $this->dm->registerServiceAction($name, $actionDefinition['service'], $actionDefinition['class'], $actionDefinition['input']);
                }
            }
        }

        //Register the rulesets
        if (array_key_exists('rulesets', $config)) {
            foreach($config['rulesets'] as $name => $rulesetDefinition) {
                $this->dm->registerRuleset($name, $rulesetDefinition);
            }
        }

        //Return the loaded up definition manager
        return $this->dm;
    }


    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////


    /**
     * Gets the Reference to the definition manager
     *
     * @return DefinitionManagerInterface
     */
    public function getDefinitionManager()
    {
        return $this->dm;
    }
}