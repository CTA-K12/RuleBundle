<?php

namespace Mesd\RuleBundle\Model\Definition;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DefinitionManagerDoctrineLoader implements DefinitionManagerLoaderInterface
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
     * The definition manager that this class is working on building.
     *
     * @var DefinitionManagerInterface
     */
    private $dm;

    /**
     * The entity manager to load from.
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
     * @param DefinitionManagerInterface $dm     Reference to the definition manager service
     * @param string                     $emName The name of the entity manager to use
     */
    public function __construct(ContainerInterface $container, $emName)
    {
        //Init the definition manager
        $this->dm = new DefinitionManager($container);

        //Get the entity manager
        $this->em = $container->get('doctrine')->getManager($emName);
    }

    /////////////
    // METHODS //
    /////////////


    /**
     * Loads the information from the config file into the definition manager.
     *
     * @return DefinitionManagerInterface The reference to the loaded definition manager
     */
    public function load()
    {
        //Load all of the inputs
        $inputs = $this->em->getRepository('MesdRuleBundle:InputEntity')->loadAll();
        foreach ($inputs as $input) {
            if (null !== $input->getParams()) {
                $params = unserialize($input->getParams());
            } else {
                $params = [];
            }
            $this->dm->registerInput($input->getName(), $input->getClass(), $params);
        }

        //Load all of the contexts
        $contexts = $this->em->getRepository('MesdRuleBundle:ContextEntity')->loadAll();
        foreach ($contexts as $context) {
            $this->dm->registerContext($context->getName(), $context->getClassification(), $context->getType());
            foreach ($context->getAttributes() as $attribute) {
                $this->dm->registerContextAttribute($attribute->getName(), $context->getName(), $attribute->getClass(), $attribute->getInput()->getName());
            }
            foreach ($context->getActions() as $action) {
                $this->dm->registerContextAction($action->getName(), $context->getName(), $action->getClass(), $action->getInput()->getName());
            }
        }

        //Load all of the services
        $services = $this->em->getRepository('MesdRuleBundle:ServiceEntity')->loadAll();
        foreach ($services as $service) {
            foreach ($service->getAttributes() as $attribute) {
                $this->dm->registerServiceAttribute($attribute->getName(), $service->getName(), $attribute->getClass(), $attribute->getInput()->getName());
            }
            foreach ($service->getActions() as $action) {
                $this->dm->registerServiceAction($action->getName(), $service->getName(), $action->getClass(), $action->getInput()->getName());
            }
        }

        //Load all of the rulesets
        $rulesets = $this->em->getRepository('MesdRuleBundle:RulesetEntity')->loadAll();
        foreach ($rulesets as $ruleset) {
            //Build the children array
            $children = ['contexts' => []];
            foreach ($ruleset->getContext() as $context) {
                $children['contexts'][] = $context->getName();
            }
            $this->dm->registerRuleset($ruleset->getName(), $children);
        }

        //Return the loaded up definition manager
        return $this->dm;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////


    /**
     * Gets the Reference to the definition manager.
     *
     * @return DefinitionManagerInterface
     */
    public function getDefinitionManager()
    {
        return $this->dm;
    }

    /**
     * Gets the The path to the definition manager configuration file.
     *
     * @return string
     */
    public function getConfigFile()
    {
        return $this->configFile;
    }

    /**
     * Sets the The path to the definition manager configuration file.
     *
     * @param string $configFile the config file
     *
     * @return self
     */
    public function setConfigFile($configFile)
    {
        $this->configFile = $configFile;

        return $this;
    }
}
