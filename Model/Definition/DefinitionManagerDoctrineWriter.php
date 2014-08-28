<?php

namespace Mesd\RuleBundle\Model\Definition;

use Mesd\RuleBundle\Model\Definition\DefinitionManager;

use Doctrine\ORM\EntityManagerInterface;

use Mesd\RuleBundle\Entity\ContextEntity;
use Mesd\RuleBundle\Entity\AttributeEntity;
use Mesd\RuleBundle\Entity\ActionEntity;
use Mesd\RuleBundle\Entity\ServiceEntity;
use Mesd\RuleBundle\Entity\InputEntity;
use Mesd\RuleBundle\Entity\RulesetEntity;

class DefinitionManagerDoctrineWriter
{
    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The entity manager to use when writing to the db
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Big ugly array map of definitions
     * @var array
     */
    private $definitions;

    /**
     * Big ugly array map to track which db entries are no longer used
     * @var array
     */
    private $tracking;

    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param EntityManagerInterface $em The entity manager to use to set the definitions
     */
    public function __construct(EntityManagerInterface $em) {
        //Set the entity manager
        $this->em = $em;

        //Init the map
        $this->definitions = array();
        $this->definitions['contexts'] = array();
        $this->definitions['cntxactn'] = array();
        $this->definitions['cntxattr'] = array();
        $this->definitions['rulesets'] = array();
        $this->definitions['services'] = array();
        $this->definitions['servactn'] = array();
        $this->definitions['servattr'] = array();
        $this->definitions['inputs'] = array();

        $this->tracking = array();
        $this->tracking['contexts'] = array();
        $this->tracking['cntxactn'] = array();
        $this->tracking['cntxattr'] = array();
        $this->tracking['rulesets'] = array();
        $this->tracking['services'] = array();
        $this->tracking['servactn'] = array();
        $this->tracking['servattr'] = array();
        $this->tracking['inputs'] = array();
    }


    /////////////
    // METHODS //
    /////////////


    /**
     * Write the definition manager to the database
     *
     * @param  DefinitionManager $dm     The definition manager to save
     * @param  boolean           $delete Wether to delete definitions not in the dm but in the database (false)
     *
     * @return array                     An array of messages detailing what changes will be made
     */
    public function write(DefinitionManager $dm, $delete = false) {
        //Load in the existing things
        $this->loadExisting();

        //Update the mapping from the definition manager
        $messages = $this->updateFromDefinitionManager($dm);

        //Delete no longer used definitions
        if ($delete) {
            $messages = array_merge($messages, $this->deleteUnused());
        }

        //return the messages
        return $messages;
    }


    /////////////////////
    // PRIVATE METHODS //
    /////////////////////


    /**
     * Load existing definitions into the map
     */
    private function loadExisting() {
        //Load the contexts
        $contexts = $this->em->getRepository('MesdRuleBundle:ContextEntity')->loadAll();
        foreach($contexts as $context) {
            $this->definitions['contexts'][$context->getName()] = $context;
            $this->tracking['contexts'][$context->getName()] = false;
            $this->definitions['cntxattr'][$context->getName()] = array();
            $this->definitions['cntxactn'][$context->getName()] = array();

            //Add the context attributes and actions to the map also
            foreach($context->getAttributes() as $attr) {
                $this->definitions['cntxattr'][$context->getName()][$attr->getName()] = $attr;
                $this->tracking['cntxattr'][$context->getName()][$attr->getName()] = false;
            }
            foreach($context->getActions() as $actn) {
                $this->definitions['cntxactn'][$context->getName()][$actn->getName()] = $actn;
                $this->tracking['cntxactn'][$context->getName()][$actn->getName()] = false;
            }
        }

        //Load the rulesets
        $rulesets = $this->em->getRepository('MesdRuleBundle:RulesetEntity')->loadAll();
        foreach($rulesets as $ruleset) {
            $this->definitions['rulesets'][$ruleset->getName()] = $ruleset;
            $this->tracking['rulesets'][$ruleset->getName()] = false;
        }

        //Load the services
        $services = $this->em->getRepository('MesdRuleBundle:ServiceEntity')->loadAll();
        foreach($services as $service) {
            $this->definitions['services'][$service->getName()] = $service;
            $this->tracking['services'][$service->getName()] = false;
            $this->definitions['servattr'][$service->getName()] = array();
            $this->definitions['servactn'][$service->getName()] = array();

            //Add the service attributes and actions to the map also
            foreach($service->getAttributes() as $attr) {
                $this->definitions['servattr'][$service->getName()][$attr->getName()] = $attr;
                $this->tracking['servattr'][$service->getName()][$attr->getName()] = false;
            }
            foreach($service->getActions() as $actn) {
                $this->definitions['servactn'][$service->getName()][$actn->getName()] = $actn;
                $this->tracking['servactn'][$service->getName()][$actn->getName()] = false;
            }
        }

        //Load the inputs
        $inputs = $this->em->getRepository('MesdRuleBundle:InputEntity')->loadAll();
        foreach($inputs as $input) {
            $this->definitions['inputs'][$input->getName()] = $input;
            $this->tracking['inputs'][$input->getName()] = false;
        }
    }


    /**
     * Update the mapping with the information from the definition manager
     *
     * @param  DefinitionManager $dm The definition manager
     *
     * @return array                 Array of messages detailing the changes made
     */
    private function updateFromDefinitionManager(DefinitionManager $dm) {
        //Init the messages array
        $messages = array();

        //Get the inputs
        foreach($dm->getAllInputDefinitions() as $name => $input) {
            if (array_key_exists($name, $this->definitions['inputs'])) {
                $this->tracking['inputs'][$name] = true;
                //Check that the existing matches
                if ($this->definitions['inputs'][$name]->getClass() !== $input['class']) {
                    $this->definitions['inputs'][$name]->setClass($input['class']);
                    $messages[] = 'Update input class of ' . $name;
                }
                if ($this->definitions['inputs'][$name]->getParmas() !== serialize($input['params'])) {
                    $this->definitions['inputs'][$name]->setParams(serialize($input['params']));
                    $messages[] = 'Update input params of ' . $name;
                }
                $this->em->persist($this->definitions['inputs'][$name]);
            } else {
                //Create new
                $this->definitions['inputs'][$name] = new InputEntity();
                $this->definitions['inputs'][$name]->setName($name);
                $this->definitions['inputs'][$name]->setClass($input['class']);
                $this->definitions['inputs'][$name]->setParams(serialize($input['params']));
                $this->em->persist($this->definitions['inputs'][$name]);
                $messages[] = 'Created new input: ' . $name;
            }
        }

        //Get the contexts
        foreach($dm->getAllContextDefinitions() as $name => $context) {
            if (array_key_exists($name, $this->definitions['contexts'])) {
                $this->tracking['contexts'][$name] = true;
                //Check that the entity matches the definition
                if ($this->definitions['contexts'][$name]->getType() !== $context['cType']) {
                    $this->definitions['contexts'][$name]->setType($context['cType']);
                    $messages[] = 'Updated context type of ' . $name;
                }
                if ($this->definitions['contexts'][$name]->getClassification() !== $context['cName']) {
                    $this->definitions['contexts'][$name]->setClassification($context['cName']);
                    $messages[] = 'Updated context classification of ' . $name;
                }
                $this->em->persist($this->definitions['contexts'][$name]);
            } else {
                //Create new
                $this->definitions['contexts'][$name] = new ContextEntity();
                $this->definitions['contexts'][$name]->setName($name);
                $this->definitions['contexts'][$name]->setType($context['cType']);
                $this->definitions['contexts'][$name]->setClassification($context['cName']);
                $this->definitions['cntxattr'][$name] = array();
                $this->definitions['cntxactn'][$name] = array();
                $this->em->persist($this->definitions['contexts'][$name]);
                $messages[] = 'New context created: ' . $name;
            }

            //Check this contexts attributes
            foreach($dm->getContextAttributeDefinitions($name) as $attrName => $attr) {
                if (array_key_exists($attrName, $this->definitions['cntxattr'][$name])) {
                    $this->tracking['cntxattr'][$name][$attrName] = true;
                    //Check if the existing needs updated
                    if ($this->definitions['cntxattr'][$name][$attrName]->getClass() !== $attr['class']) {
                        $this->definitions['cntxattr'][$name][$attrName]->setClass($attr['class']);
                        $messages[] = 'Updated class of ' . $attrName . ' for context ' . $name;
                    }
                    if ($this->definitions['cntxattr'][$name][$attrName]->getInput()->getName() !== $attr['input']) {
                        $this->definitions['cntxattr'][$name][$attrName]->setInput($this->definitions['inputs'][$attr['input']]);
                        $messages[] = 'Updated input of ' . $attrName . ' for context ' . $name;
                    }
                    $this->em->persist($this->definitions['cntxattr'][$name][$attrName]);
                } else {
                    //Create new
                    $this->definitions['cntxattr'][$name][$attrName] = new AttributeEntity();
                    $this->definitions['cntxattr'][$name][$attrName]->setName($attrName);
                    $this->definitions['cntxattr'][$name][$attrName]->setClass($attr['class']);
                    $this->definitions['cntxattr'][$name][$attrName]->setContext($this->definitions['contexts'][$name]);
                    $this->definitions['cntxattr'][$name][$attrName]->setInput($this->definitions['inputs'][$attr['input']]);
                    $this->em->persist($this->definitions['cntxattr'][$name][$attrName]);
                    $messages[] = 'Created new attribute ' . $attrName . ' for context ' . $name;
                }
            }

            //Check this contexts actions
            foreach($dm->getContextActionDefinitions($name) as $actnName => $actn) {
                if (array_key_exists($actnName, $this->definitions['cntxactn'][$name])) {
                    $this->tracking['cntxactn'][$name][$actnName] = true;
                    //Check if the existing needs updated
                    if ($this->definitions['cntxactn'][$name][$actnName]->getClass() !== $actn['class']) {
                        $this->definitions['cntxactn'][$name][$actnName]->setClass($actn['class']);
                        $messages[] = 'Updated class of ' . $actnName . ' for context ' . $name;
                    }
                    if ($this->definitions['cntxactn'][$name][$actnName]->getInput()->getName() !== $actn['input']) {
                        $this->definitions['cntxactn'][$name][$actnName]->setInput($this->definitions['inputs'][$actn['input']]);
                        $messages[] = 'Updated input of ' . $actnName . ' for context ' . $name;
                    }
                    $this->em->persist($this->definitions['cntxactn'][$name][$actnName]);
                } else {
                    //Create new
                    $this->definitions['cntxactn'][$name][$actnName] = new ActionEntity();
                    $this->definitions['cntxactn'][$name][$actnName]->setName($actnName);
                    $this->definitions['cntxactn'][$name][$actnName]->setClass($actn['class']);
                    $this->definitions['cntxactn'][$name][$actnName]->setContext($this->definitions['contexts'][$name]);
                    $this->definitions['cntxactn'][$name][$actnName]->setInput($this->definitions['inputs'][$actn['input']]);
                    $this->em->persist($this->definitions['cntxactn'][$name][$actnName]);
                    $messages[] = 'Created new action ' . $actnName . ' for context ' . $name;
                }
            }
        }

        //Get the services via the service actions and attributes
        foreach($dm->getAllServiceAttributeDefinitions() as $attrName => $attr) {
            //Check that the service was setup
            if (!array_key_exists($attr['service'], $this->definitions['services'])) {
                //Create it
                $this->definitions['services'][$attr['service']] = new ServiceEntity();
                $this->definitions['services'][$attr['service']]->setName($attr['service']);
                $this->definitions['servattr'][$attr['service']] = array();
                $this->definitions['servactn'][$attr['service']] = array();
                $this->em->persist($this->definitions['services'][$attr['service']]);
                $messages[] = 'Created new service: ' . $attr['service'];
            } else {
                $this->tracking['services'][$attr['service']] = true;
            }

            //Check if the service attribute exists
            if (array_key_exists($attrName, $this->definitions['servattr'][$attr['service']])) {
                $this->tracking['servattr'][$attr['service']][$attrName] = true;
                //Check if the existing needs updated
                if ($this->definitions['servattr'][$attr['service']][$attrName]->getClass() !== $attr['class']) {
                    $this->definitions['servattr'][$attr['service']][$attrName]->setClass($attr['class']);
                    $messages[] = 'Updated class of ' . $attrName . ' for context ' . $attr['service'];
                }
                if ($this->definitions['servattr'][$attr['service']][$attrName]->getInput()->getName() !== $attr['input']) {
                    $this->definitions['servattr'][$attr['service']][$attrName]->setInput($this->definitions['inputs'][$attr['input']]);
                    $messages[] = 'Updated input of ' . $attrName . ' for context ' . $attr['service'];
                }
                $this->em->persist($this->definitions['servattr'][$attr['service']][$attrName]);
            } else {
                //Create new
                $this->definitions['servattr'][$attr['service']][$attrName] = new AttributeEntity();
                $this->definitions['servattr'][$attr['service']][$attrName]->setName($attrName);
                $this->definitions['servattr'][$attr['service']][$attrName]->setClass($attr['class']);
                $this->definitions['servattr'][$attr['service']][$attrName]->setService($this->definitions['services'][$attr['service']]);
                $this->definitions['servattr'][$attr['service']][$attrName]->setInput($this->definitions['inputs'][$attr['input']]);
                $this->em->persist($this->definitions['servattr'][$attr['service']][$attrName]);
                $messages[] = 'Created new attribute ' . $attrName . ' for service ' . $attr['service'];
            }
        }

        //This whole method is REALLLLLLLLY UGLY, hopefully Ill fix it eventually
        foreach($dm->getAllServiceActionDefinitions() as $actnName => $actn) {
            //Check that the service was setup
            if (!array_key_exists($actn['service'], $this->definitions['services'])) {
                //Create it
                $this->definitions['services'][$actn['service']] = new ServiceEntity();
                $this->definitions['services'][$actn['service']]->setName($actn['service']);
                $this->definitions['servattr'][$actn['service']] = array();
                $this->definitions['servactn'][$actn['service']] = array();
                $this->em->persist($this->definitions['services'][$actn['service']]);
                $messages[] = 'Created new service: ' . $actn['service'];
            } else {
                $this->tracking['services'][$actn['service']] = true;
            }

            //Check if the service attribute exists
            if (array_key_exists($actnName, $this->definitions['servactn'][$actn['service']])) {
                $this->tracking['servactn'][$actn['service']][$actnName] = true;
                //Check if the existing needs updated
                if ($this->definitions['servactn'][$actn['service']][$actnName]->getClass() !== $actn['class']) {
                    $this->definitions['servactn'][$actn['service']][$actnName]->setClass($actn['class']);
                    $messages[] = 'Updated class of ' . $actnName . ' for context ' . $actn['service'];
                }
                if ($this->definitions['servactn'][$actn['service']][$actnName]->getInput()->getName() !== $actn['input']) {
                    $this->definitions['servactn'][$actn['service']][$actnName]->setInput($this->definitions['inputs'][$actn['input']]);
                    $messages[] = 'Updated input of ' . $actnName . ' for context ' . $actn['service'];
                }
                $this->em->persist($this->definitions['servactn'][$actn['service']][$actnName]);
            } else {
                //Create new
                $this->definitions['servactn'][$actn['service']][$actnName] = new ActionEntity();
                $this->definitions['servactn'][$actn['service']][$actnName]->setName($actnName);
                $this->definitions['servactn'][$actn['service']][$actnName]->setClass($actn['class']);
                $this->definitions['servactn'][$actn['service']][$actnName]->setService($this->definitions['services'][$actn['service']]);
                $this->definitions['servactn'][$actn['service']][$actnName]->setInput($this->definitions['inputs'][$actn['input']]);
                $this->em->persist($this->definitions['servactn'][$actn['service']][$actnName]);
                $messages[] = 'Created new action ' . $actnName . ' for service ' . $actn['service'];
            }
        }

        //Get rulesets
        foreach($dm->getAllRulesetDefinitions() as $name => $ruleset) {
            if (array_key_exists($name, $this->definitions['rulesets'])) {
                $this->tracking['rulesets'][$name] = true;
                //Check if needs updated
                $checkoff = array();
                foreach($ruleset['contexts'] as $contextName) {
                    $checkoff[$contextName] = $this->definitions['contexts'][$contextName];
                }
                $existingContexts = $this->definitions['rulesets'][$name]->getContext();
                foreach($existingContexts as $existingContext) {
                    if (in_array($existingContext->getName(), $ruleset['contexts'])) {
                        unset($checkoff[$existingContext->getName()]);
                    } else {
                        $this->definitions['rulesets'][$name]->removeContext($existingContext);
                        $messages[] = 'Removing context ' . $existingContext->getName() . ' from ruleset ' . $name;
                    }
                }
                //Add the contexts remaining in the checkoff
                foreach($checkoff as $newContext) {
                    $this->definitions['rulesets'][$name]->addContext($newContext);
                    $messages[] = 'Adding context ' . $newContext->getName() . ' to ruleset ' . $name;
                }
                $this->em->persist($this->definitions['rulesets'][$name]);
            } else {
                //Create new
                $this->definitions['rulesets'][$name] = new RulesetEntity();
                $this->definitions['rulesets'][$name]->setName($name);
                foreach($ruleset['contexts'] as $contextName) {
                    $this->definitions['rulesets'][$name]->addContext($this->definitions['contexts'][$contextName]);
                }
                $this->em->persist($this->definitions['rulesets'][$name]);
                $messages[] = 'Created new ruleset: ' . $name;
            }
        }

        //Return the messages
        return $messages;
    }


    /**
     * Delete the definitions not contained in the definition manager from the file
     *
     * @return array The array of messages detailing which definitions are to be deleted
     */
    private function deleteUnused() {
        $messages = array();
        //Delete unused contexts
        foreach($this->tracking['contexts'] as $contextName => $used) {
            if (!$used) {
                $context = $this->defintions['contexts'][$contextName];
                //Delete the attributes and actions
                foreach($context->getAttributes() as $attr) {
                    $this->em->remove($attr);
                }
                foreach($context->getActions() as $actn) {
                    $this->em->remove($actn);
                }
                $this->em->remove($context);
                $messages[] = 'Deleting context: ' . $contextName;
            }
        }

        //Delete unused context attributes
        foreach($this->tracking['cntxattr'] as $contextName => $attrs) {
            foreach($attrs as $attrName => $used) {
                if (!$used) {
                    $this->em->remove($this->definitions['cntxattr'][$contextName][$attrName]);
                    $messages[] = 'Deleting attribute ' . $attrName . ' for context ' . $contextName;
                }
            }
        }

        //Delete unused context actions
        foreach($this->tracking['cntxactn'] as $contextName => $actns) {
            foreach($actns as $actnName => $used) {
                if (!$used) {
                    $this->em->remove($this->definitions['cntxactn'][$contextName][$actnName]);
                    $messages[] = 'Deleting action ' . $actnName . ' for context ' . $contextName;
                }
            }
        }

        //Delete unused services
        foreach($this->tracking['services'] as $serviceName => $used) {
            if (!$used) {
                $service = $this->definitions['services'][$serviceName];
                //Delete the attributes and actions
                foreach($service->getAttributes() as $attr) {
                    $this->em->remove($attr);
                }
                foreach($service->getActions() as $actn) {
                    $this->em->remove($actn);
                }
                $this->em->remove($service);
                $messages[] = 'Deleting service: ' . $serviceName;
            }
        }

        //Delete unused service attributes
        foreach($this->tracking['servattr'] as $serviceName => $attrs) {
            foreach($attrs as $attrName => $used) {
                if (!$used) {
                    $this->em->remove($this->definitions['servattr'][$serviceName][$attrName]);
                    $messages[] = 'Deleting attribute ' . $attrName . ' for service ' . $serviceName;
                }
            }
        }

        //Delete unused service actions
        foreach($this->tracking['servactn'] as $serviceName => $actns) {
            foreach($actns as $actnName => $used) {
                if (!$used) {
                    $this->em->remove($this->definitions['servactn'][$serviceName][$actnName]);
                    $messages[] = 'Deleting action ' . $actnName . ' for service ' . $serviceName;
                }
            }
        }

        //Delete unused inputs
        foreach($this->tracking['inputs'] as $inputName => $used) {
            if (!$used) {
                $this->em->remove($this->definitions['inputs'][$inputName]);
                $messages[] = 'Deleting input: ' . $inputName;
            }
        }

        //Delete unused rulesets
        foreach($this->tracking['rulesets'] as $rulesetName => $used) {
            if (!$used) {
                $this->em->remove($this->definitions['rulesets'][$rulesetName]);
                $messages[] = 'Deleting ruleset: ' . $rulesetName;
            }
        }

        //return the message
        return $messages;
    }
}